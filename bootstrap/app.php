<?php

use App\Enums\HttpStatus;
use App\Http\Middleware\IsRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias(['isRole' => IsRole::class]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (NotFoundHttpException $e) {
            return response()->json(['error' => $e->getMessage()], HttpStatus::NOT_FOUND->value);
        });
    })->create();
