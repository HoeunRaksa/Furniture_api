<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WebAuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            $credentials = [
                'email' => $request->username,
                'password' => $request->password
            ];

            Log::info('Web login attempt with email', [
                'email' => $request->username,
            ]);

            if (Auth::attempt($credentials, $request->filled('remember'))) {
                $request->session()->regenerate();

                Log::info('Web login successful', ['email' => $request->username]);

                return redirect()->intended(route('home'));
            }

            Log::warning('Web login failed: invalid credentials', ['username' => $request->username]);

            return back()->withErrors([
                'username' => 'Invalid credentials',
            ])->withInput();
        } catch (\Throwable $e) {
            Log::error('Web login exception', [
                'username' => $request->username,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'username' => 'Something went wrong. Please try again later.',
            ])->withInput();
        }
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('User logged out');

        return redirect('/login');
    }
}
