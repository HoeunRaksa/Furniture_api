<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class BankPaymentController extends Controller
{
    public function showPaymentPage($invoice_no)
    {
        $order = \App\Models\Order::where('invoice_no', $invoice_no)
            ->orWhere('invoice_no', 'INV-'.$invoice_no)
            ->firstOrFail();

        if ($order->payment_status === 'paid') {
            return view('bank.success', compact('order'));
        }

        return view('bank.pay', compact('order'));
    }

    public function processPayment(Request $request, $invoice_no)
    {
        \Illuminate\Support\Facades\Log::info("Processing payment for: $invoice_no");

        $request->validate([
            'account_number' => 'required',
            'password' => 'required',
        ]);

        $order = \App\Models\Order::where('invoice_no', $invoice_no)
            ->orWhere('invoice_no', 'INV-'.$invoice_no)
            ->firstOrFail();

        \Illuminate\Support\Facades\Log::info('Order found: '.$order->id.' Status: '.$order->payment_status);

        if ($order->payment_status === 'paid') {
            return redirect()->route('pay.show', $invoice_no);
        }

        $account = BankAccount::where('account_number', $request->account_number)->first();

        if (! $account) {
            \Illuminate\Support\Facades\Log::error('Account not found: '.$request->account_number);

            return back()->with('error', 'Invalid account number.');
        }

        if (! Hash::check($request->password, $account->password)) {
            \Illuminate\Support\Facades\Log::error('Password mismatch for account: '.$request->account_number);

            return back()->with('error', 'Invalid password.');
        }

        if ($account->balance < $order->total_price) {
            \Illuminate\Support\Facades\Log::error('Insufficient balance. Acc: '.$account->balance.' Order: '.$order->total_price);

            return back()->with('error', 'Insufficient balance.');
        }

        // Deduct balance
        $account->decrement('balance', $order->total_price);

        // Update order status directly
        $order->payment_status = 'paid';
        $order->status = 'processing';
        $order->save();

        \Illuminate\Support\Facades\Log::info('SUCCESS! Order '.$order->invoice_no.' marked as PAID.');

        return redirect()->route('pay.show', $invoice_no)->with('success', 'Payment successful!');
    }
}
