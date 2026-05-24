<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Pastikan ini ada

class AuthUser
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = Auth::user();

        // Jika user adalah ADM, arahkan ke admin dashboard
        if ($user->utype === 'ADM') {
            return redirect()->route('admin.dashboard')->with('info', 'Anda diarahkan ke dashboard admin.');
        }

        // Jika user adalah OWN, arahkan ke owner dashboard
        if ($user->utype === 'OWN') {
            return redirect()->route('owner.dashboard');
        }

        // Izinkan hanya USR mengakses halaman user
        if ($user->utype === 'USR') {
            return $next($request);
        }

        return redirect()->route('index')->with('error', 'You do not have permission to access the user dashboard.');
    }
}