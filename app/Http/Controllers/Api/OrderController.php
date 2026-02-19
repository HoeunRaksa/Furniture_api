<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
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
     * Get orders for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $orders = Order::with(['user', 'items.product.images'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Orders retrieved successfully',
            'data' => $orders,
        ]);
    }

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

            $responseData = $order->load(['items.product', 'user'])->toArray();

            // Generate QR code if payment method is QR
            if ($request->payment_method === 'QR') {
                $invoiceNo = $order->invoice_no;
                // Use the bank domain for payment page
                $paymentUrl = "https://bank.furniture.learner-teach.online/pay/$invoiceNo";

                // Use a public QR code API to generate the image
                $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data='.urlencode($paymentUrl);
                try {
                    $qrImageData = @file_get_contents($qrUrl);
                    if ($qrImageData) {
                        $responseData['qr_image'] = 'data:image/png;base64,'.base64_encode($qrImageData);
                    } else {
                        // Fallback: just return the URL if file_get_contents fails
                        $responseData['qr_image'] = $qrUrl;
                    }
                } catch (\Exception $e) {
                    $responseData['qr_image'] = $qrUrl;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'data' => $responseData,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Order creation failed: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get transaction details for QR payment.
     */
    public function getTransactionDetails($tranId)
    {
        $order = Order::where('invoice_no', $tranId)
            ->orWhere('invoice_no', 'INV-'.$tranId)
            ->first();

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found for ID: '.$tranId,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'merchant' => 'Furniture Store',
                'amount' => $order->total_price,
                'currency' => 'USD',
                'invoice_no' => $order->invoice_no,
                'description' => 'Payment for Furniture Order',
            ],
        ]);
    }

    /**
     * Finalize payment (Simulate bank callback/confirmation).
     */
    public function finalizePayment(Request $request, $tranId)
    {
        $order = Order::where('invoice_no', $tranId)
            ->orWhere('invoice_no', 'INV-'.$tranId)
            ->first();

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found for ID: '.$tranId,
            ], 404);
        }

        if ($order->payment_status === 'paid') {
            return response()->json([
                'success' => true,
                'message' => 'Payment already completed',
                'data' => $order,
            ]);
        }

        // Optional: Simulate actual balance deduction if an account number is provided
        if ($request->has('account_number')) {
            $bankAccount = BankAccount::where('account_number', $request->account_number)->first();

            if (! $bankAccount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bank account not found',
                ], 404);
            }

            if ($bankAccount->balance < $order->total_price) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient balance in bank account',
                ], 400);
            }

            // Deduct the amount
            $bankAccount->decrement('balance', $order->total_price);
        }

        // Update order status
        $order->update([
            'payment_status' => 'paid',
            'status' => 'processing',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment successful',
            'data' => $order,
        ]);
    }

    /**
     * Check order status (for polling).
     */
    public function checkStatus($invoice_no)
    {
        $order = Order::where('invoice_no', $invoice_no)
            ->orWhere('invoice_no', 'INV-'.$invoice_no)
            ->first();

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'status' => $order->status,
                'payment_status' => $order->payment_status,
            ],
        ]);
    }
}
