<?php

namespace App\Http\Controllers\Api\Bank;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BankAccountController extends Controller
{
    /**
     * Bank Login - Authenticate using Account Number and Password
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_number' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $account = BankAccount::where('account_number', $request->account_number)
            ->where('is_active', true)
            ->first();

        if (! $account || ! Hash::check($request->password, $account->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid account number or password.',
            ], 401);
        }

        // Generate Token
        $token = Str::random(60);
        $account->update(['api_token' => $token]);

        return response()->json([
            'success' => true,
            'message' => 'Bank login successful.',
            'data' => $account,
        ]);
    }

    /**
     * Get Account Details - Requires BankAuthMiddleware
     */
    public function getAccountDetails(Request $request)
    {
        // Account is attached to request by BankAuthMiddleware
        $account = $request->get('bank_account');

        return response()->json([
            'success' => true,
            'data' => $account,
        ]);
    }

    /**
     * Seed initial bank data for testing
     */
    public function seedTestData()
    {
        $data = [
            [
                'account_number' => '123456789',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'password' => Hash::make('123456'),
                'balance' => 5000.00,
                'profile_image' => 'https://ui-avatars.com/api/?name=John+Doe&background=random',
            ],
            [
                'account_number' => '987654321',
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'password' => Hash::make('654321'),
                'balance' => 250.50,
                'profile_image' => 'https://ui-avatars.com/api/?name=Jane+Smith&background=random',
            ],
        ];

        foreach ($data as $item) {
            BankAccount::updateOrCreate(
                ['account_number' => $item['account_number']],
                $item
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Bank test data seeded. Accounts: 123456789 (John) and 987654321 (Jane).',
        ]);
    }
}
