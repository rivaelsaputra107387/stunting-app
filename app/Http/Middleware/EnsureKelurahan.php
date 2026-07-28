<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureKelurahan
{
    /**
     * Handle an incoming request.
     * Only allow users with 'kelurahan' role.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()?->role !== 'kelurahan') {
            abort(403, 'Akses ditolak. Anda bukan admin Kelurahan.');
        }

        return $next($request);
    }
}
