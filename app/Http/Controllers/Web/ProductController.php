<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProductVariant;
use App\Models\ProductDiscount;
use App\Models\ProductDescriptionLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('products.index');
    }

    /**
     * Get data for DataTables.
     */
    public function data(Request $request)
    {
        if ($request->ajax()) {
            $query = Product::with(['category', 'images']);

            return DataTables::of($query)
                ->addColumn('image', function ($row) {
                    $imageUrl = $row->images->first() ? asset($row->images->first()->image_url) : asset('placeholder.png');
                    return '<img src="' . $imageUrl . '" class="rounded-3 shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">';
                })
                ->addColumn('category_name', function ($row) {
                    return $row->category ? $row->category->name : '-';
                })
                ->editColumn('price', function ($row) {
                    return '$' . number_format($row->price, 2);
                })
                ->editColumn('discount', function ($row) {
                    return $row->discount ? $row->discount . '%' : 'None';
                })
                ->addColumn('actions', function ($row) {
                    $edit = '<a href="' . route('products.edit', $row->id) . '" class="btn btn-sm btn-primary me-1">Edit</a>';
                    $delete = '<button data-url="' . route('products.destroy', $row->id) . '" class="btn btn-sm btn-danger delete-product">Delete</button>';
                    return $edit . $delete;
                })
                ->rawColumns(['image', 'actions'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $attributes = Attribute::with('values')->get();
        return view('products.create', compact('categories', 'attributes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'stock' => 'nullable|integer',
            'images.*' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:5120',
            'description_lines' => 'nullable|array',
            'variants' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            $product = Product::create([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price ?? 0,
                'discount' => $request->discount ?? 0,
                'stock' => $request->stock ?? 0,
                'active' => $request->boolean('active', true),
                'is_featured' => $request->boolean('is_featured', false),
                'is_recommended' => $request->boolean('is_recommended', false),
            ]);

            // Handle Images
            if ($request->hasFile('images')) {
                $uploadPath = public_path('uploads/products');
                if (!File::exists($uploadPath)) {
                    File::makeDirectory($uploadPath, 0755, true);
                }

                foreach ($request->file('images') as $image) {
                    $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move($uploadPath, $filename);
                    $product->images()->create([
                        'image_url' => 'uploads/products/' . $filename,
                    ]);
                }
            }

            // Description Lines
            if ($request->description_lines) {
                foreach ($request->description_lines as $index => $line) {
                    if ($line) {
                        $product->descriptionLines()->create([
                            'text' => $line,
                            'sort_order' => $index,
                        ]);
                    }
                }
            }

            // Variants
            if ($request->variants) {
                foreach ($request->variants as $variantData) {
                    $variant = $product->variants()->create([
                        'sku' => $variantData['sku'] ?? null,
                        'price' => $variantData['price'] ?? 0,
                    ]);

                    if (isset($variantData['attributes'])) {
                        $variant->attributes()->attach($variantData['attributes']);
                    }
                }
            }

            // Discount
            if ($request->filled('discount_value')) {
                $product->discounts()->create([
                    'name' => $request->discount_name ?? $product->name . ' Discount',
                    'value' => $request->discount_value,
                    'is_percentage' => $request->boolean('is_percentage', true),
                    'active' => true,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'msg' => 'Product created successfully',
                'location' => route('products.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating product', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'msg' => 'Error creating product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $product = Product::with(['images', 'variants.attributes', 'discounts', 'descriptionLines'])->findOrFail($id);
        $categories = Category::all();
        $attributes = Attribute::with('values')->get();
        return view('products.edit', compact('product', 'categories', 'attributes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'stock' => 'nullable|integer',
            'images.*' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:5120',
            'description_lines' => 'nullable|array',
            'variants' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            $product = Product::findOrFail($id);
            $product->update([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price ?? 0,
                'discount' => $request->discount ?? 0,
                'stock' => $request->stock ?? 0,
                'active' => $request->boolean('active', true),
                'is_featured' => $request->boolean('is_featured', false),
                'is_recommended' => $request->boolean('is_recommended', false),
            ]);

            // Handle Images
            if ($request->hasFile('images')) {
                $uploadPath = public_path('uploads/products');
                if (!File::exists($uploadPath)) {
                    File::makeDirectory($uploadPath, 0755, true);
                }

                foreach ($request->file('images') as $image) {
                    $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move($uploadPath, $filename);
                    $product->images()->create([
                        'image_url' => 'uploads/products/' . $filename,
                    ]);
                }
            }

            // Update Description Lines
            $product->descriptionLines()->delete();
            if ($request->description_lines) {
                foreach ($request->description_lines as $index => $line) {
                    if ($line) {
                        $product->descriptionLines()->create([
                            'text' => $line,
                            'sort_order' => $index,
                        ]);
                    }
                }
            }

            // Update Variants
            $product->variants()->each(function($variant) {
                $variant->attributes()->detach();
                $variant->delete();
            });

            if ($request->variants) {
                foreach ($request->variants as $variantData) {
                    $variant = $product->variants()->create([
                        'sku' => $variantData['sku'] ?? null,
                        'price' => $variantData['price'] ?? 0,
                    ]);

                    if (isset($variantData['attributes'])) {
                        $variant->attributes()->attach($variantData['attributes']);
                    }
                }
            }

            // Update Discount
            if ($request->filled('discount_value')) {
                $product->discounts()->update(['active' => false]);
                $product->discounts()->create([
                    'name' => $request->discount_name ?? $product->name . ' Discount',
                    'value' => $request->discount_value,
                    'is_percentage' => $request->boolean('is_percentage', true),
                    'active' => true,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'msg' => 'Product updated successfully',
                'location' => route('products.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating product', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'msg' => 'Error updating product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $product = Product::findOrFail($id);

            // Delete files
            foreach ($product->images as $image) {
                if ($image->image_url && File::exists(public_path($image->image_url))) {
                    File::delete(public_path($image->image_url));
                }
            }

            $product->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'msg' => 'Product deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting product', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'msg' => 'Failed to delete product'
            ], 500);
        }
    }

    /**
     * Delete specific product image.
     */
    public function deleteImage($id)
    {
        try {
            $image = ProductImage::findOrFail($id);
            $imagePath = public_path($image->image_url);
            
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
            
            $image->delete();

            return response()->json([
                'success' => true,
                'msg' => 'Image deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Error deleting image: ' . $e->getMessage()
            ], 500);
        }
    }
}
