<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
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
        $exceptions->render(function (UnauthorizedException $e) {
            return response()->json([
                'message' => 'You do not have the required roles or permissions to access this resource.',
                'required_roles' => $e->getRequiredRoles(),       // array of roles (if roles were checked)
                'required_permissions' => $e->getRequiredPermissions(), // array of permissions (if permissions were checked)
            ], 403);
        });
        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            $message = $e->getMessage();
            preg_match('/Supported methods:\s(.+)$/', $message, $matches);
            $allowed = isset($matches[1]) ? explode(', ', $matches[1]) : [];
            return response()->json([
                'message' => 'The HTTP method used in request is not allowed for this endpoint.',
                'used_method' => $request->method(),
                'allowed_methods' => $allowed,
            ], 405);
        });
    })->create();
