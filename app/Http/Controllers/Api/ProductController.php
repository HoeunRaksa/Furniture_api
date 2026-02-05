<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        try {
            $products = Product::with(['images', 'category'])->latest()->get();

            $products->each(function ($product) {
                $product->images->each(function ($image) {
                    if ($image->image_url) {
                        $image->full_url = url($image->image_url);
                    }
                });
            });

            return response()->json([
                'success' => true,
                'data' => $products,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch products', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch products',
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:225',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'stock' => 'required|integer',
            'images.*' => 'required|image|mimes:jpg,png,jpeg,webp|max:5120', // Max 5MB
        ]);

        try {
            $product = Product::create($request->only([
                'category_id', 'name', 'description', 'price', 'discount', 'stock',
            ]));

            if ($request->hasFile('images')) {
                // Create directory if not exists
                $uploadPath = public_path('uploads/products');
                if (! File::exists($uploadPath)) {
                    File::makeDirectory($uploadPath, 0755, true);
                }

                foreach ($request->file('images') as $image) {
                    // Generate unique filename
                    $filename = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();

                    // Move to public directory
                    $image->move($uploadPath, $filename);

                    // Create image record
                    $product->images()->create([
                        'image_url' => 'uploads/products/'.$filename,
                    ]);
                }
            }

            // Load relationships with full URLs
            $product->load(['images', 'category']);
            $product->images->transform(function ($image) {
                if ($image->image_url) {
                    $image->full_url = url($image->image_url);
                }

                return $image;
            });

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => $product,
            ], 201);
        } catch (\Exception $e) {
            // Delete uploaded images if exists
            if (isset($product) && $product->images) {
                foreach ($product->images as $image) {
                    if (File::exists(public_path($image->image_url))) {
                        File::delete(public_path($image->image_url));
                    }
                }
                $product->images()->delete();
            }

            Log::error('Failed to create product', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $product = Product::with(['images', 'category'])->findOrFail($id);

            // Add full URL for images
            $product->images->transform(function ($image) {
                if ($image->image_url) {
                    $image->full_url = url($image->image_url);
                }

                return $image;
            });

            return response()->json([
                'success' => true,
                'data' => $product,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        // Handle _method field from FormData
        if ($request->has('_method')) {
            $request->request->remove('_method');
        }

        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'sometimes|required|exists:categories,id',
            'name' => 'sometimes|required|string|max:225',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric',
            'discount' => 'nullable|numeric',
            'stock' => 'sometimes|required|integer',
            'images.*' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:5120',
        ]);

        try {
            $product->update($request->only([
                'category_id', 'name', 'description', 'price', 'discount', 'stock',
            ]));

            if ($request->hasFile('images')) {
                // Create directory if not exists
                $uploadPath = public_path('uploads/products');
                if (! File::exists($uploadPath)) {
                    File::makeDirectory($uploadPath, 0755, true);
                }

                foreach ($request->file('images') as $image) {
                    // Generate unique filename
                    $filename = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();

                    // Move to public directory
                    $image->move($uploadPath, $filename);

                    // Create image record
                    $product->images()->create([
                        'image_url' => 'uploads/products/'.$filename,
                    ]);
                }
            }

            // Load relationships with full URLs
            $product->load(['images', 'category']);
            $product->images->transform(function ($image) {
                if ($image->image_url) {
                    $image->full_url = url($image->image_url);
                }

                return $image;
            });

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => $product,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update product', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);

            // Delete associated images
            foreach ($product->images as $image) {
                if ($image->image_url && File::exists(public_path($image->image_url))) {
                    File::delete(public_path($image->image_url));
                }
            }

            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete product', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Upload additional images to existing product
    public function uploadImages(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'images.*' => 'required|image|mimes:jpg,png,jpeg,webp|max:5120',
        ]);

        try {
            if ($request->hasFile('images')) {
                // Create directory if not exists
                $uploadPath = public_path('uploads/products');
                if (! File::exists($uploadPath)) {
                    File::makeDirectory($uploadPath, 0755, true);
                }

                foreach ($request->file('images') as $image) {
                    // Generate unique filename
                    $filename = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();

                    // Move to public directory
                    $image->move($uploadPath, $filename);

                    // Create image record
                    $product->images()->create([
                        'image_url' => 'uploads/products/'.$filename,
                    ]);
                }
            }

            // Load relationships with full URLs
            $product->load(['images', 'category']);
            $product->images->transform(function ($image) {
                if ($image->image_url) {
                    $image->full_url = url($image->image_url);
                }

                return $image;
            });

            return response()->json([
                'success' => true,
                'message' => 'Images uploaded successfully',
                'data' => $product,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to upload images', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload images',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Delete specific image
    public function deleteImage($productId, $imageId)
    {
        try {
            $product = Product::findOrFail($productId);
            $image = $product->images()->findOrFail($imageId);

            // Delete file
            if ($image->image_url && File::exists(public_path($image->image_url))) {
                File::delete(public_path($image->image_url));
            }

            // Delete record
            $image->delete();

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete image', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
