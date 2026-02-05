<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Public welcome page
     */
    public function welcome(): View
    {
        $categories_count = \App\Models\Category::where('is_active', true)->count();
        $products_count = \App\Models\Product::count();
        $customers_count = \App\Models\User::where('role', 'customer')->count() ?: '1.2k+';

        return view('welcome', compact('categories_count', 'products_count', 'customers_count'));
    }

    /**
     * Admin dashboard
     */
    public function admin(): View
    {
        // Check authentication in production
        $metrics = [
            'total_orders' => 1247,
            'pending_orders' => 23,
            'revenue_this_month' => 145890,
            'new_customers' => 89,
        ];

        return view('admin.dashboard', compact('metrics'));
    }

    /**
     * API endpoint for featured products
     */
    public function getFeaturedProducts(): JsonResponse
    {
        $products = [
            [
                'id' => 1,
                'name' => 'Oslo Lounge Chair',
                'price' => 1299,
                'image' => 'oslo-chair.jpg',
                'collection' => 'Scandinavian Comfort',
            ],
            [
                'id' => 2,
                'name' => 'Brooklyn Dining Table',
                'price' => 2499,
                'image' => 'brooklyn-table.jpg',
                'collection' => 'Urban Industrial',
            ],
            [
                'id' => 3,
                'name' => 'Monaco Velvet Sofa',
                'price' => 3299,
                'image' => 'monaco-sofa.jpg',
                'collection' => 'Modern Minimalist',
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }
}
