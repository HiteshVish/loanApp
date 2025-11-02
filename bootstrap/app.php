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
        // Handle PostTooLargeException (file upload size exceeded)
        $exceptions->render(function (\Illuminate\Http\Exceptions\PostTooLargeException $e, $request) {
            if ($request->is('kyc/*')) {
                return back()->with('error', 'The uploaded files are too large. Please ensure each file is under 2MB. Try reducing image quality or compressing PDF files.');
            }
            
            return back()->with('error', 'The uploaded data is too large. Please reduce the file sizes and try again.');
        });
    })->create();
