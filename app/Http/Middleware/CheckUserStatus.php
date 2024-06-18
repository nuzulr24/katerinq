<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class checkUserStatus
{
    public function handle($request, Closure $next)
    {
        // Check if the user is authenticated
        if (Auth::check()) {
            $user = Auth::user();

            // Check the user's status
            if ($user->status === 3) {
                return back()
                    ->with('swal', swal_alert('error', 'Akun anda belum aktif, harap tunggu administrator untuk pengaktifan dalam 1x24 jam.'));
            }
        }

        return $next($request);
    }
}
