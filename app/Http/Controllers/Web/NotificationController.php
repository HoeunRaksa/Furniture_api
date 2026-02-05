<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Check for new orders created since the given timestamp.
     */
    public function checkNewOrders(Request $request)
    {
        $lastTimestamp = $request->get('since');
        
        // Always fetch the 5 most recent orders for the dropdown
        $latestOrders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $newOrders = collect([]);

        // If we have a timestamp, check for ANY orders created after that time
        if ($lastTimestamp) {
            $newOrders = Order::with('user')
                ->where('created_at', '>', Carbon::parse($lastTimestamp))
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return response()->json([
            'new_orders' => $newOrders->map(function($order) {
                return $this->formatOrder($order);
            }),
            'latest_orders' => $latestOrders->map(function($order) {
                return $this->formatOrder($order);
            }),
            'count' => $newOrders->count(),
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    private function formatOrder($order) 
    {
        return [
            'id' => $order->id,
            'invoice_no' => 'Order #' . $order->id,
            'customer' => $order->user ? $order->user->full_name : 'Guest',
            'amount' => '$' . number_format($order->total_price, 2),
            'time' => $order->created_at->diffForHumans(),
            'url' => route('orders.index') // Link to the orders list
        ];
    }
}
