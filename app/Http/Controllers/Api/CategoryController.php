<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;

class CategoryController extends Controller
{
    #[OA\Get(
        path: '/api/categories',
        responses: [
            new OA\Response(response: 200, description: 'List all active categories'),
        ]
    )]
    public function index()
    {
        try {
            $categories = Category::where('is_active', true)->latest()->get();

            $categories->each(function ($category) {
                if ($category->image_path) {
                    $category->image_full_url = url($category->image_path);
                } else {
                    $category->image_full_url = null;
                }
            });

            return response()->json([
                'success' => true,
                'data' => $categories,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch categories', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch categories',
            ], 500);
        }
    }
}
