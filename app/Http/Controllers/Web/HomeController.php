<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Middleware is defined in routes/web.php
    }

    /**
     * Show the application dashboard.
     */
    public function index(): View
    {
        $metrics = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'revenue_this_month' => Order::whereIn('status', ['paid', 'delivered'])
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_price'),
            'new_customers' => User::where('role', 'user')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        // Sales Trend (7 Days)
        $salesData = Order::whereIn('status', ['paid', 'delivered'])
            ->where('created_at', '>=', now()->subDays(6))
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%m/%d') as date"),
                DB::raw('SUM(total_price) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top Categories
        $topCategories = Category::withCount('products')
            ->orderBy('products_count', 'desc')
            ->take(5)
            ->get();

        return view('home.index', compact('metrics', 'salesData', 'topCategories'));
    }
}
