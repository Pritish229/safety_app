<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function loginView()
    {
        if (Auth::check()) {
            $user = Auth::user();
            return redirect($this->getRedirectUrl($user));
        }

        return view('Auth.Login');
    }
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            return response()->json([
                'success' => true,
                'message' => 'Login successful!',
                'redirect' => $this->getRedirectUrl($user),
            ]);
        }

        return response()->json([
            'success' => false,
            'errors' => ['email' => ['Invalid email or password.']],
        ], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully!',
                'redirect' => route('Auth.LoginView'),
            ]);
        }

        return redirect()->route('Auth.LoginView')->with('success', 'Logged out successfully!');
    }

    protected function getRedirectUrl($user)
    {
        if ($user->hasPermission('view-dashboard')) {
            return route('dashboard');
        }
        return route('unauthorized'); 
    }
}
