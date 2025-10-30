<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Please log in.',
                ], 401);
            }
            return redirect()->route('Auth.LoginView')->with('error', 'Please log in to access this page.');
        }

        if (!Auth::user()->hasRole($role)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have the required permissions.',
                ], 403);
            }
            return redirect()->route('Auth.LoginView')->with('error', 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}