<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $role = $request->user()->role;
        $value = gettype($role) === 'string' ? $role : $role->value;

        if (!in_array($value, $roles)) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
