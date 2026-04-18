<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * @param  array<int, string>  $roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if ($user === null) {
            abort(401);
        }

        $role = $user->role instanceof \BackedEnum ? (string) $user->role->value : (string) $user->role;

        if (! in_array($role, $roles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
