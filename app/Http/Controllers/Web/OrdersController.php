<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('orders.index');
    }

    /**
     * Get data for DataTables.
     */
    public function data(Request $request)
    {
        if ($request->ajax()) {
            $query = Order::with('user');

            return DataTables::of($query)
                ->editColumn('invoice_no', function ($row) {
                    return $row->invoice_no ?? 'ID: ' . $row->id;
                })
                ->editColumn('user_name', function ($row) {
                    return $row->user ? $row->user->username : 'N/A';
                })
                ->editColumn('total_price', function ($row) {
                    return '$' . number_format($row->total_price, 2);
                })
                ->editColumn('status', function ($row) {
                    $badges = [
                        'pending' => 'bg-warning',
                        'paid' => 'bg-success',
                        'shipped' => 'bg-info',
                        'delivered' => 'bg-primary',
                        'cancelled' => 'bg-danger',
                    ];
                    $badge = $badges[$row->status] ?? 'bg-secondary';
                    return '<span class="badge ' . $badge . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('actions', function ($row) {
                    $invoiceUrl = route('orders.print', $row->id);
                    $deleteUrl = route('orders.destroy', $row->id);
                    return '
                    <div class="dropdown text-center">
                        <button class="btn btn-sm btn-light border dropdown-toggle no-caret" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                            <li><a class="dropdown-item view-order py-2" href="javascript:void(0)" data-id="' . $row->id . '"><i class="bi bi-eye me-2 text-primary"></i> View Details</a></li>
                            <li><a class="dropdown-item py-2" href="' . $invoiceUrl . '" target="_blank"><i class="bi bi-printer me-2 text-success"></i> Print Invoice</a></li>
                            <li><a class="dropdown-item py-2 view-map" href="javascript:void(0)" data-lat="' . ($row->lat ?? '') . '" data-long="' . ($row->long ?? '') . '"><i class="bi bi-geo-alt me-2 text-info"></i> View on Map</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item py-2 delete-order text-danger" href="javascript:void(0)" data-url="' . $deleteUrl . '"><i class="bi bi-trash me-2"></i> Delete</a></li>
                        </ul>
                    </div>';
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }
    }

    /**
     * Print invoice for the order.
     */
    public function printInvoice($id)
    {
        $order = Order::with(['user', 'items.product'])->findOrFail($id);
        $business = \App\Models\Business::first();
        return view('orders.invoice', compact('order', 'business'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $order = Order::with(['user', 'items.product'])->findOrFail($id);
        return response()->json($order);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $order = Order::findOrFail($id);
            $order->delete();
            return response()->json([
                'success' => true,
                'msg' => 'Order deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Failed to delete order',
            ], 500);
        }
    }
}
