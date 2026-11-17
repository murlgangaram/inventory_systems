<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Check whether the authenticated account has an allowed role.
     */
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        $account = $request->user();

        if (!$account || !in_array($account->role, $roles, true)) {
            abort(403, 'You are not authorized to access this page.');
        }

        return $next($request);
    }
}

