<?php

namespace App\Http\Middleware;

use Mini\Foundation\Request;
use Mini\Interfaces\Middleware\MiddlewareContract;

class Authenticate implements MiddlewareContract
{
    public function handle(Request $request, \Closure $next)
    {
        return $request->get('auth') === '跳过' ? $next($request) : '权限验证未通过';
    }
}