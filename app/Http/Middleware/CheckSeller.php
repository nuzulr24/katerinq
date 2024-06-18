<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Seller;

class CheckSeller
{
    public function handle($request, Closure $next)
    {
        // Check if the user is authenticated
        if (Auth::check()) {
            $user = Auth::user();

            if(Seller::where('user_id', $user->id)->count() < 0) {
                return redirect()->to(site_url('user', '/'))->with('swal', swal_alert('error', 'Akun anda tidak terdaftar sebagai seller, laman ini hanya dapat diakses oleh pengguna seller!'));
            }
        }

        return $next($request);
    }
}
