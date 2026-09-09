<?php

use App\Http\Middleware\UserIsAdmin;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => UserIsAdmin::class
        ]);

        $middleware->redirectGuestsTo(fn () => null);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(function (Request $request) {
            return $request->is('api/*') || $request->expectsJson();
        });

        $exceptions->render(function (
            NotFoundHttpException $e,
            Request $request
        ) {
            if (
                $request->is('api/*') &&
                $e->getPrevious() instanceof ModelNotFoundException
            ) {
                return response()->json([
                    'message' => 'Record not found.',
                ], 404);
            }

            return response()->json([
                'message' => 'The requested endpoint does not exist.',
            ], 404);
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            return response()->json([
                'message' => 'Invalid payload.',
                'errors' => $e->errors(),
            ], 422);
        });

        $exceptions->render(function (AuthorizationException $e, Request $request) {
            return response()->json([
                'message' => 'You are not authorized to access this resource.',
            ], 403);
        });

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*') && ! ($e instanceof ModelNotFoundException
                || $e instanceof NotFoundHttpException
                || $e instanceof ValidationException
                || $e instanceof AuthenticationException 
                || $e instanceof AuthorizationException)) {
                return response()->json([
                    'message' => app()->hasDebugModeEnabled()
                        ? $e->getMessage()
                        : 'Something went wrong. Please try again later.',
                ], 500);
            }
        });
    })->create();
