<?php

namespace App\Http\Controllers\Api\Bank;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class BankAccountController extends Controller
{
    #[OA\Post(
        path: "/api/bank/login",
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "account_number", type: "string"),
                    new OA\Property(property: "password", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Bank login successful"),
            new OA\Response(response: 401, description: "Invalid credentials")
        ]
    )]
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

    #[OA\Get(
        path: "/api/bank/account",
        security: [['api_token' => []]],
        responses: [
            new OA\Response(response: 200, description: "Bank account details"),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function getAccountDetails(Request $request)
    {
        // Account is attached to request by BankAuthMiddleware
        $account = $request->get('bank_account');

        return response()->json([
            'success' => true,
            'data' => $account,
        ]);
    }

    #[OA\Post(
        path: "/api/bank/seed",
        responses: [
            new OA\Response(response: 200, description: "Test data seeded")
        ]
    )]
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
