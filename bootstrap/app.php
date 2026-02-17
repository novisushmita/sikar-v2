<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth.token' => \App\Http\Middleware\AuthenticateWithToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
         $exceptions->render(function (
            MethodNotAllowedHttpException $e,
            $request
        ) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Method tidak diizinkan',
                    'allowed_methods' => $e->getHeaders()['Allow'] ?? []
                ], 405);
            }
        });
    })->create();
