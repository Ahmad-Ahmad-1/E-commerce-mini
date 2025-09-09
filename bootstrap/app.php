<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (NotFoundHttpException $e) {
            if ($e->getPrevious() instanceof ModelNotFoundException) {
                $class = class_basename($e->getPrevious()->getModel());
                $ids = implode(',', $e->getPrevious()->getIds());
                return response()->json(
                    ["message" => "The resource you requested: [$class][$ids] does not exist."],
                    404
                );
            }
            return response()->json(["message" => "The URL you requested does not exist."], 404);
        });
        $exceptions->render(function (HttpException $e) {
            if ($e->getStatusCode() === 419) {
                return response()->json(
                    [
                        'message' =>
                        "CSRF Token Mismatch. Make sure you are getting the CSRF cookie from /sanctum/csrf-cookie and sending it in request's X-XSRF-TOKEN header"
                    ],
                    419
                );
            }
        });
    })->create();
