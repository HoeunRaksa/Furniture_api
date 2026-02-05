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
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:225',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'stock' => 'required|integer',
            'images.*' => 'required|image|mimes:jpg,png,jpeg,webp|max:5120',
        ]);

        try {
            DB::beginTransaction();

            $product = Product::create($request->only([
                'category_id', 'name', 'description', 'price', 'discount', 'stock',
            ]));

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
        $product = Product::with('images')->findOrFail($id);
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
            'name' => 'required|string|max:225',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'stock' => 'required|integer',
            'images.*' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:5120',
        ]);

        try {
            DB::beginTransaction();

            $product = Product::findOrFail($id);
            $product->update($request->only([
                'category_id', 'name', 'description', 'price', 'discount', 'stock',
            ]));

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
            
            if ($image->image_url && File::exists(public_path($image->image_url))) {
                File::delete(public_path($image->image_url));
            }

            $image->delete();

            return response()->json([
                'success' => true,
                'msg' => 'Image removed'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Failed to remove image'
            ], 500);
        }
    }
}
