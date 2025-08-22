<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Cek jika role user tidak sama dengan role yang diizinkan
        if ($request->user()->role->value !== $role) {
            // Jika tidak cocok, kembalikan halaman error 403 (Forbidden)
            abort(403, 'ANDA TIDAK PUNYA AKSES KE HALAMAN INI.');
        }

        return $next($request);
    }
}