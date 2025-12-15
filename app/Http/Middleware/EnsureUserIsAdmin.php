<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Only allow admin (quan_ly) or superadmin (admin)
        // Checks if user role is EITHER 'quan_ly' OR 'admin'
        if (!in_array(Auth::user()->vai_tro, ['quan_ly', 'admin'])) {
            
            // If user is employee, redirect to their dashboard
            if (Auth::user()->vai_tro === 'nhan_vien') {
                return redirect()->route('employee.dashboard');
            }

            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
