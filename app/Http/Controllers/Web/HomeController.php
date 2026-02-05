<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
            'total_orders' => 0,
            'pending_orders' => 0,
            'revenue_this_month' => 0,
            'new_customers' => 0,
        ];

        return view('home.index', compact('metrics'));
    }
}
