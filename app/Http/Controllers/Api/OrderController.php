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
use OpenApi\Attributes as OA;

class OrderController extends Controller
{
    #[OA\Get(
        path: "/api/orders",
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: "List user orders"),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function index(Request $request)
    {
        $user = Auth::user();
        $orders = Order::with(['user', 'items.product.images'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $orders->each(function ($order) {
            $order->items->each(function ($item) {
                if ($item->product) {
                    $item->product->images->each(function ($image) {
                        if ($image->image_url) {
                            $image->full_url = url($image->image_url);
                        }
                    });
                }
            });
        });

        return response()->json([
            'success' => true,
            'message' => 'Orders retrieved successfully',
            'data' => $orders,
        ]);
    }

    #[OA\Post(
        path: "/api/orders",
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "items", type: "array", items: new OA\Items(
                        properties: [
                            new OA\Property(property: "product_id", type: "integer"),
                            new OA\Property(property: "quantity", type: "integer")
                        ]
                    )),
                    new OA\Property(property: "shipping_address", type: "string"),
                    new OA\Property(property: "lat", type: "number"),
                    new OA\Property(property: "long", type: "number"),
                    new OA\Property(property: "phone_number", type: "string"),
                    new OA\Property(property: "payment_method", type: "string", enum: ["Cash", "QR"])
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Order created"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
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

            $responseData = $order->load(['items.product.images', 'user'])->toArray();

            // Set full URLs for product images in response
            foreach ($responseData['items'] as &$item) {
                if (isset($item['product']['images'])) {
                    foreach ($item['product']['images'] as &$image) {
                        if (isset($image['image_url'])) {
                            $image['full_url'] = url($image['image_url']);
                        }
                    }
                }
            }

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

    #[OA\Get(
        path: "/api/qr/details/{tranId}",
        parameters: [
            new OA\Parameter(name: "tranId", in: "path", required: true, schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Get transaction details")
        ]
    )]
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

    #[OA\Post(
        path: "/api/qr/pay/{tranId}",
        parameters: [
            new OA\Parameter(name: "tranId", in: "path", required: true, schema: new OA\Schema(type: "string"))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "account_number", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Payment successful")
        ]
    )]
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

            // ✅ Use string values for safe decimal compare
            $balance = (string) $bankAccount->balance;
            $total = (string) $order->total_price;

            // ✅ bccomp returns: -1 if balance < total, 0 if equal, 1 if greater
            if (bccomp($balance, $total, 2) === -1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient balance in bank account',
                    'data' => [
                        'balance' => $balance,
                        'total_price' => $total,
                    ],
                ], 400);
            }

            // ✅ Deduct safely using bcsub (avoid float bug)
            $newBalance = bcsub($balance, $total, 2);

            $bankAccount->update([
                'balance' => $newBalance,
            ]);
        }

        // ✅ Update order status
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

    #[OA\Get(
        path: "/api/orders/{id}",
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Order details"),
            new OA\Response(response: 404, description: "Order not found")
        ]
    )]
    public function show($id)
    {
        $user = Auth::user();
        $order = Order::with(['user', 'items.product.images'])
            ->where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        // Add full URLs for product images
        $order->items->each(function ($item) {
            if ($item->product) {
                $item->product->images->each(function ($image) {
                    if ($image->image_url) {
                        $image->full_url = url($image->image_url);
                    }
                });
            }
        });

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    #[OA\Get(
        path: "/api/orders/{invoice_no}/status",
        parameters: [
            new OA\Parameter(name: "invoice_no", in: "path", required: true, schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Order status"),
            new OA\Response(response: 404, description: "Order not found")
        ]
    )]
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
