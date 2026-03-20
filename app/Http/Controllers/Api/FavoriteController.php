<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;

class FavoriteController extends Controller
{
    #[OA\Get(
        path: '/api/favorites',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'List favorites'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function index(Request $request)
    {
        try {
            $favorites = Favorite::where('user_id', $request->user()->id)
                ->with('product.images') // Fixed: was 'product_id', should be 'product'
                ->latest()
                ->get();

            // Add full URL for product images
            $favorites->transform(function ($favorite) {
                if ($favorite->product && $favorite->product->images) {
                    $favorite->product->images->transform(function ($image) {
                        if ($image->image_url) {
                            $image->full_url = url($image->image_url);
                        }

                        return $image;
                    });
                }

                return $favorite;
            });

            return response()->json([
                'success' => true,
                'data' => $favorites,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch favorites', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch favorites',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    #[OA\Post(
        path: '/api/favorites',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'product_id', type: 'integer'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Added to favorites'),
            new OA\Response(response: 400, description: 'Already in favorites'),
        ]
    )]
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id', // Fixed: was 'product', should be 'products'
        ]);

        try {
            // Check if already exists
            $existing = Favorite::where('user_id', $request->user()->id)
                ->where('product_id', $request->product_id)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product already in favorites',
                ], 400);
            }

            $favorite = Favorite::create([
                'user_id' => $request->user()->id,
                'product_id' => $request->product_id,
            ]);

            // Load product with images
            $favorite->load('product.images');

            // Add full URL for product images
            if ($favorite->product && $favorite->product->images) {
                $favorite->product->images->transform(function ($image) {
                    if ($image->image_url) {
                        $image->full_url = url($image->image_url);
                    }

                    return $image;
                });
            }

            return response()->json([
                'success' => true,
                'message' => 'Added to favorites', // Fixed typo: was 'messgae'
                'data' => $favorite, // Fixed typo: was 'date'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to add favorite', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to add to favorites',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    #[OA\Delete(
        path: '/api/favorites/{product_id}',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'product_id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Removed from favorites'),
            new OA\Response(response: 404, description: 'Favorite not found'),
        ]
    )]
    public function destroy($product_id, Request $request)
    {
        try {
            $deleted = Favorite::where('user_id', $request->user()->id)
                ->where('product_id', $product_id)
                ->delete();

            if (! $deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Favorite not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Removed from favorites',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to remove favorite', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to remove from favorites',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Optional: Toggle favorite (add/remove in one endpoint)
    #[OA\Post(
        path: '/api/favorites/toggle',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'product_id', type: 'integer'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Favorite status toggled'),
        ]
    )]
    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        try {
            $favorite = Favorite::where('user_id', $request->user()->id)
                ->where('product_id', $request->product_id)
                ->first();

            if ($favorite) {
                // Remove from favorites
                $favorite->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Removed from favorites',
                    'is_favorite' => false,
                ]);
            } else {
                // Add to favorites
                $favorite = Favorite::create([
                    'user_id' => $request->user()->id,
                    'product_id' => $request->product_id,
                ]);

                $favorite->load('product.images');

                // Add full URL for product images
                if ($favorite->product && $favorite->product->images) {
                    $favorite->product->images->transform(function ($image) {
                        if ($image->image_url) {
                            $image->full_url = url($image->image_url);
                        }

                        return $image;
                    });
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Added to favorites',
                    'is_favorite' => true,
                    'data' => $favorite,
                ], 201);
            }
        } catch (\Exception $e) {
            Log::error('Failed to toggle favorite', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle favorite',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Check if product is favorited
    #[OA\Get(
        path: '/api/favorites/check/{product_id}',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'product_id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Favorite status'),
        ]
    )]
    public function check($product_id, Request $request)
    {
        try {
            $isFavorite = Favorite::where('user_id', $request->user()->id)
                ->where('product_id', $product_id)
                ->exists();

            return response()->json([
                'success' => true,
                'is_favorite' => $isFavorite,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to check favorite', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to check favorite status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
