<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
            'captcha' => 'required|integer'
        ]);

        $captchaCode = session('captcha_code');
        if ($request->captcha != $captchaCode) {
            $this->generateCaptcha();
            return back()->withErrors(['captcha' => 'Incorrect security verification.'])->withInput();
        }

        // Support both email and username
        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        
        $credentials = [
            $loginField => $request->login,
            'password' => $request->password,
        ];

        // Custom validation to allow only active users
        if (Auth::attempt(array_merge($credentials, ['status' => 'Active']))) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        $this->generateCaptcha();
        return back()->withErrors([
            'login' => 'The provided credentials do not match our records or the account is inactive.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function generateCaptchaApi()
    {
        $this->generateCaptcha();
        return response()->json([
            'question' => session('captcha_num1') . ' + ' . session('captcha_num2') . ' = ?'
        ]);
    }

    private function generateCaptcha()
    {
        $n1 = rand(2, 9);
        $n2 = rand(1, 9);
        session(['captcha_num1' => $n1, 'captcha_num2' => $n2, 'captcha_code' => $n1 + $n2]);
    }
}
