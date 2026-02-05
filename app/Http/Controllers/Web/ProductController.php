<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
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
            $query = Product::with(['category', 'images'])->select('products.*');

            $count = $query->count();
            Log::info("ProductController::data - Found {$count} products.");

            return DataTables::of($query)
                ->addColumn('image_url', fn($product) => $product->images->first()?->image_url ? asset($product->images->first()->image_url) : null)
                ->addColumn('category', fn($product) => $product->category?->name ?? '<span class="badge bg-secondary">No Category</span>')
                ->addColumn('price', fn($product) => '$' . number_format($product->price, 2))
                ->addColumn('discount', fn($product) => '$' . number_format($product->discount, 2))
                ->addColumn('stock', fn($product) => $product->stock)
                ->addColumn('featured', function ($product) {
                    if ($product->is_featured) {
                        return '<span class="status-badge text-white bg-warning">Featured</span>';
                    }
                    return '<span class="status-badge text-dark bg-light">Standard</span>';
                })
                ->addColumn('status', function ($product) {
                    $badges = [];
                    if ($product->is_active) {
                        $badges[] = '<span class="status-badge text-white bg-success">Active</span>';
                    } else {
                        $badges[] = '<span class="status-badge text-white bg-secondary">Inactive</span>';
                    }
                    return implode(' ', $badges);
                })
                ->addColumn('action', function ($product) {
                    return '
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="' . route('products.edit', $product->id) . '" 
                           class="btn btn-outline-primary" 
                           title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <button data-url="' . route('products.destroy', $product->id) . '" 
                           class="btn btn-outline-danger delete-product" 
                           title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>';
                })
                ->rawColumns(['category', 'featured', 'status', 'action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
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
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
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
                'is_featured' => $request->boolean('is_featured', false),
                'is_active' => $request->boolean('is_active', true),
                'active' => $request->boolean('is_active', true) // Sync legacy active column if it exists
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
        $product = Product::with(['images'])->findOrFail($id);
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
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
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
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
                'is_featured' => $request->boolean('is_featured', false),
                'is_active' => $request->boolean('is_active', true),
                'active' => $request->boolean('is_active', true)
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
