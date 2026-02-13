<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Create a new order.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // 1. Validation
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping_address' => 'nullable|string',
            'lat' => 'nullable|numeric',
            'long' => 'nullable|numeric',
            'phone_number' => 'nullable|string',
            'payment_method' => 'nullable|in:Cash,QR', // Maps to 'method' column
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $items = $request->items;
            $totalPrice = 0;
            $orderItemsData = [];

            // 2. Calculate Total Price & Prepare Items
            foreach ($items as $item) {
                $product = Product::find($item['product_id']);

                // Optional: Check stock here
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                $price = $product->price; // Use current price from DB
                // Apply discount if any? Logic depends on requirements. details omitted for simplicity unless requested.
                // Assuming price is final price.

                $lineTotal = $price * $item['quantity'];
                $totalPrice += $lineTotal;

                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                ];
            }

            // 3. Create Order
            $order = Order::create([
                'user_id' => $user->id,
                'invoice_no' => 'INV-'.strtoupper(uniqid()), // Simple invoice generation
                'total_price' => $totalPrice,
                'status' => 'pending',
                'payment_status' => 'pending',
                'shipping_status' => 'pending',
                'method' => $request->payment_method ?? 'Cash',
                'shipping_address' => $request->shipping_address,
                'lat' => $request->lat,
                'long' => $request->long,
                'phone_number' => $request->phone_number,
                'shipping_charged' => 0.00, // Default for now
            ]);

            // 4. Create Order Items
            foreach ($orderItemsData as $data) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $data['product_id'],
                    'quantity' => $data['quantity'],
                    'price' => $data['price'],
                ]);

                // Optional: Deduct stock
                $product = Product::find($data['product_id']);
                $product->decrement('stock', $data['quantity']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'data' => $order->load('items.product'), // Return order with items
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Order creation failed: '.$e->getMessage(),
            ], 500);
        }
    }
}
