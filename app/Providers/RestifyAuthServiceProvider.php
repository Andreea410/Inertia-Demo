<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Binaryk\LaravelRestify\Http\Controllers\Auth\LoginController;
use Binaryk\LaravelRestify\Http\Controllers\Auth\LogoutController;
use Binaryk\LaravelRestify\Http\Controllers\Auth\RegisterController;
use Binaryk\LaravelRestify\Http\Controllers\Auth\ForgotPasswordController;
use Binaryk\LaravelRestify\Http\Controllers\Auth\ResetPasswordController;
use Binaryk\LaravelRestify\Http\Controllers\Auth\VerifyController;

class RestifyAuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::middleware('api')
            ->prefix('api/restify')
            ->group(function () {
                Route::post('/login', [\Binaryk\LaravelRestify\Http\Controllers\Auth\LoginController::class, '__invoke'])
                    ->name('restify.login');

                Route::post('/register', [\Binaryk\LaravelRestify\Http\Controllers\Auth\RegisterController::class, '__invoke'])
                    ->name('restify.register');

                Route::post('/forgotPassword', [\Binaryk\LaravelRestify\Http\Controllers\Auth\ForgotPasswordController::class, '__invoke'])
                    ->name('restify.forgotPassword');

                Route::post('/resetPassword', [\Binaryk\LaravelRestify\Http\Controllers\Auth\ResetPasswordController::class, '__invoke'])
                    ->name('restify.resetPassword');

                Route::post('/verify/{id}/{hash}', [\Binaryk\LaravelRestify\Http\Controllers\Auth\VerifyController::class, '__invoke'])
                    ->name('restify.verify');

                Route::middleware('auth:sanctum')
                    ->post('/logout', [\Binaryk\LaravelRestify\Http\Controllers\Auth\LogoutController::class, '__invoke'])
                    ->name('restify.logout');
            });
    }
}
