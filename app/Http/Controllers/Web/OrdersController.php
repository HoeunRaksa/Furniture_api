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
                ->editColumn('user_name', function ($row) {
                    return $row->user ? $row->user->name : 'N/A';
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
                    $view = '<button data-id="' . $row->id . '" class="btn btn-sm btn-info view-order me-1">View</button>';
                    $delete = '<button data-url="' . route('orders.destroy', $row->id) . '" class="btn btn-sm btn-danger delete-order">Delete</button>';
                    return $view . $delete;
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }
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
