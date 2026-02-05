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
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        try {
            Log::info('Web login attempt', [
                'email' => $request->email,
            ]);

            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials, $request->filled('remember'))) {
                $request->session()->regenerate();

                Log::info('Web login successful', ['email' => $request->email]);

                return redirect()->intended(route('home'));
            }

            Log::warning('Web login failed: invalid credentials', ['email' => $request->email]);

            return back()->withErrors([
                'email' => 'Invalid credentials',
            ])->withInput();

        } catch (\Throwable $e) {
            Log::error('Web login exception', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'email' => 'Something went wrong. Please try again later.',
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
