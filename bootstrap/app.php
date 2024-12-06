<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureUserIsSubscribed;
use Clue\Redis\Protocol\Model\Request;
use Illuminate\Auth\AuthenticationException;

use function Termwind\render;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function(AuthenticationException $e, Request $request){
            if($request()->is('api/*')){
                return response()->json([
                    'message' => $e->getMessage(),
                ], 401);
            }
        });
    })->create();