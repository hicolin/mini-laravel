<?php

namespace App\Http\Middleware;

use Mini\Foundation\Request;
use Mini\Interfaces\Middleware\MiddlewareContract;

class MyMiddleware implements MiddlewareContract
{
    public function handle(Request $request, \Closure $next)
    {
        return $next($request);
    }
}