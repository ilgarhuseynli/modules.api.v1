<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'auth.gates' => \App\Http\Middleware\AuthGates::class,
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
//        $exceptions->renderable(function (\Throwable $e, $request) {
//            // Check if the route belongs to the API prefix
//            if ($request->is('api/*')) {
//                $status = $e instanceof HttpException ? $e->getStatusCode() : 500;
//
//                if ($e instanceof AuthenticationException) {
//                    $status = 401;
//                }
//
//                return response()->json([
//                    'error' => $e->getMessage(),
//                    'code' => $status,
//                ], $status);
//            }
//        });
    })->create();
