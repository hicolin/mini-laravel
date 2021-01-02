<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap.php';

$kernel = $app->make(\Mini\Interfaces\Foundation\KernelContract::class);

$response = $kernel->handle(
    $request = \Mini\Foundation\Request::capture()
);

var_dump($response);

//var_dump($response);

