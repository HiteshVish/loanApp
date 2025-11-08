<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\IsAdmin::class,
            'kyc' => \App\Http\Middleware\CheckKycStatus::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle API requests - return JSON instead of redirects
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.'
                ], 401);
            }
        });

        // Handle validation errors for API
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The given data was invalid.',
                    'errors' => $e->errors()
                ], 422);
            }
        });

        // Handle unauthorized (403) for API
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. ' . $e->getMessage()
                ], 403);
            }
        });

        // Handle not found (404) for API
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not Found'
                ], 404);
            }
        });

        // Handle PostTooLargeException (file upload size exceeded)
        $exceptions->render(function (\Illuminate\Http\Exceptions\PostTooLargeException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The uploaded files are too large. Please ensure each file is under 2MB.'
                ], 413);
            }
            
            if ($request->is('kyc/*')) {
                return back()->with('error', 'The uploaded files are too large. Please ensure each file is under 2MB. Try reducing image quality or compressing PDF files.');
            }
            
            return back()->with('error', 'The uploaded data is too large. Please reduce the file sizes and try again.');
        });

        // Handle all other exceptions for API
        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
                
                return response()->json([
                    'success' => false,
                    'message' => $statusCode === 500 ? 'Server Error' : $e->getMessage()
                ], $statusCode);
            }
        });
    })->create();
