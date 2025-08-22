<?php

namespace App\Http\Middleware;

use App\Enums\UserRole; // <-- Import Enum Role
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- Import Auth
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Ambil user yang sedang login
                $user = Auth::user();

                // Jika user adalah ADMIN, arahkan ke dashboard admin
                if ($user->role === UserRole::ADMIN) {
                    return redirect('/admin/dashboard');
                }

                // Jika user adalah PSIKOLOG, arahkan ke dashboard psikolog
                if ($user->role === UserRole::PSIKOLOG) {
                    return redirect('/psikolog/dashboard');
                }
                
                // Jika user biasa, arahkan ke dashboard standar
                return redirect('/dashboard');
            }
        }

        return $next($request);
    }
}