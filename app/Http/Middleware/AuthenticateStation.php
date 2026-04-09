<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthenticateStation
{
    public function handle(Request $request, Closure $next)
    {
        // Nếu trong Header không có, nhưng trên URL có ?token=...
        if (!$request->bearerToken() && $request->query('token')) {
            $request->headers->set('Authorization', 'Bearer ' . $request->query('token'));
        }

        return $next($request);
    }
}
