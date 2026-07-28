<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePosyandu
{
    /**
     * Handle an incoming request.
     * Only allow users with 'posyandu' role.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()?->role !== 'posyandu') {
            abort(403, 'Akses ditolak. Anda bukan petugas Posyandu.');
        }

        return $next($request);
    }
}
