<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
        // append (no prepend): debe correr DESPUÉS de TrustProxies para que el
        // scheme detectado sea https detrás del proxy y los 301 no degraden a http.
        $middleware->append(\App\Http\Middleware\StripTrailingSlash::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
