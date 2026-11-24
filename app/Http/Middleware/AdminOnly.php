<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Hanya akun dengan is_admin = 1 yang boleh membuka halaman /admin. */
class AdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user() && $request->user()->is_admin, 403, 'Halaman ini khusus admin.');

        return $next($request);
    }
}
