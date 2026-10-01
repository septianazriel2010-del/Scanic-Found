<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Middleware ini mengunci sebuah route hanya untuk role tertentu.
     * Contoh pemakaian di routes/web.php:
     *
     *   Route::middleware(['auth', 'role:admin'])->group(...)
     *   Route::middleware(['auth', 'role:admin,staff'])->group(...)
     *
     * Kalau user login tapi role-nya tidak cocok, request ditolak 403.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
