<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class BankPaymentController extends Controller
{
    public function showPaymentPage($invoice_no)
    {
        $order = \App\Models\Order::where('invoice_no', $invoice_no)
            ->orWhere('invoice_no', 'INV-' . $invoice_no)
            ->firstOrFail();
        
        if ($order->payment_status === 'paid') {
            return view('bank.success', compact('order'));
        }
        
        return view('bank.pay', compact('order'));
    }

    public function processPayment(Request $request, $invoice_no)
    {
        $request->validate([
            'account_number' => 'required',
            'password' => 'required',
        ]);

        $order = \App\Models\Order::where('invoice_no', $invoice_no)
            ->orWhere('invoice_no', 'INV-' . $invoice_no)
            ->firstOrFail();
        
        if ($order->payment_status === 'paid') {
            return redirect()->route('pay.show', $invoice_no);
        }

        $account = BankAccount::where('account_number', $request->account_number)->first();

        if (!$account || !Hash::check($request->password, $account->password)) {
            \Illuminate\Support\Facades\Log::warning("Payment attempt failed for invoice: $invoice_no - Invalid credentials.");
            return back()->with('error', 'Invalid account number or password.');
        }

        if ($account->balance < $order->total_price) {
            \Illuminate\Support\Facades\Log::warning("Payment attempt failed for invoice: $invoice_no - Insufficient balance.");
            return back()->with('error', 'Insufficient balance.');
        }

        // Deduct balance
        $account->decrement('balance', $order->total_price);
        
        // Update order status directly
        $order->payment_status = 'paid';
        $order->status = 'processing';
        $order->save();

        \Illuminate\Support\Facades\Log::info("Payment successful for invoice: $invoice_no. New Status: " . $order->payment_status);

        return redirect()->route('pay.show', $invoice_no)->with('success', 'Payment successful!');
    }
}
