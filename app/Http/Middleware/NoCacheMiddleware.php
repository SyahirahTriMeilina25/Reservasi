<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoCacheMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Perbaikan: Tambahkan pemeriksaan untuk admin juga
        if (!Auth::guard('mahasiswa')->check() && !Auth::guard('dosen')->check() && !Auth::guard('admin')->check()) {
            return redirect()->route('login');
        }

        $response = $next($request);

        // Set header anti-cache yang kuat
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, private');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');

        return $response;
    }
}