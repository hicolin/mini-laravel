<?php

use Mini\Foundation\Application;

if ( !function_exists('app') ) {
    function app($abstract = ''){
        $app = Application::getContainer();
        return $abstract ? $app->make($abstract) : $app;
    }
}

if ( !function_exists('env') ) {
    function env($name, $default = ''){
        return $_ENV[$name] ?? $default;
    }
}

if ( !function_exists('config') ) {
    function config($key = null, $default = null)
    {
        return is_null($key) ? app('config') : app('config')->get($key, $default);
    }
}


