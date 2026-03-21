<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    #[OA\Get(
        path: '/api/products',
        responses: [
            new OA\Response(response: 200, description: 'List all products'),
        ]
    )]
    public function index()
    {
        try {
            $products = Product::with(['images', 'category'])->latest()->get();
            $categories = \App\Models\Category::where('is_active', true)->get();

            $categories->each(function ($category) {
                if ($category->image_path) {
                    $category->image_full_url = url($category->image_path);
                } else {
                    $category->image_full_url = null;
                }
            });

            $products->each(function ($product) {
                $product->images->each(function ($image) {
                    if ($image->image_url) {
                        $image->full_url = url($image->image_url);
                    }
                });

                // Check for favorite status if user is authenticated (Sanctum)
                $user = auth('sanctum')->user();
                if ($user) {
                    $product->is_favorite = \App\Models\Favorite::where('user_id', $user->id)
                        ->where('product_id', $product->id)
                        ->exists();
                } else {
                    $product->is_favorite = false;
                }
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'products' => $products,
                    'categories' => $categories,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch products', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch products',
            ], 500);
        }
    }

    #[OA\Post(
        path: '/api/admin/products',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'category_id', type: 'integer'),
                        new OA\Property(property: 'name', type: 'string'),
                        new OA\Property(property: 'description', type: 'string'),
                        new OA\Property(property: 'price', type: 'number'),
                        new OA\Property(property: 'discount', type: 'number'),
                        new OA\Property(property: 'stock', type: 'integer'),
                        new OA\Property(property: 'images[]', type: 'array', items: new OA\Items(type: 'string', format: 'binary')),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Product created'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
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
                'category_id',
                'name',
                'description',
                'price',
                'discount',
                'stock',
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

    #[OA\Get(
        path: '/api/products/{id}',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Product details'),
            new OA\Response(response: 404, description: 'Product not found'),
        ]
    )]
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

    #[OA\Post(
        path: '/api/admin/products/{id}',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'category_id', type: 'integer'),
                        new OA\Property(property: 'name', type: 'string'),
                        new OA\Property(property: 'description', type: 'string'),
                        new OA\Property(property: 'price', type: 'number'),
                        new OA\Property(property: 'discount', type: 'number'),
                        new OA\Property(property: 'stock', type: 'integer'),
                        new OA\Property(property: 'images[]', type: 'array', items: new OA\Items(type: 'string', format: 'binary')),
                        new OA\Property(property: '_method', type: 'string', example: 'PUT'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Product updated'),
            new OA\Response(response: 404, description: 'Product not found'),
        ]
    )]
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
                'category_id',
                'name',
                'description',
                'price',
                'discount',
                'stock',
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

    #[OA\Delete(
        path: '/api/admin/products/{id}',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Product deleted'),
            new OA\Response(response: 404, description: 'Product not found'),
        ]
    )]
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
    #[OA\Post(
        path: '/api/admin/products/{id}/images',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'images[]', type: 'array', items: new OA\Items(type: 'string', format: 'binary')),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Images uploaded'),
            new OA\Response(response: 404, description: 'Product not found'),
        ]
    )]
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
    #[OA\Delete(
        path: '/api/admin/products/{productId}/images/{imageId}',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'productId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'imageId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Image deleted'),
            new OA\Response(response: 404, description: 'Product or image not found'),
        ]
    )]
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
