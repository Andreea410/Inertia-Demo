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

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ✅ Define a macro for Restify authentication routes
        Route::macro('restifyAuth', function () {
            Route::prefix('restify')->group(function () {
                Route::post('/login', [LoginController::class, '__invoke'])->name('restify.login');
                Route::post('/register', [RegisterController::class, '__invoke'])->name('restify.register');
                Route::post('/forgotPassword', [ForgotPasswordController::class, '__invoke'])->name('restify.forgotPassword');
                Route::post('/resetPassword', [ResetPasswordController::class, '__invoke'])->name('restify.resetPassword');
                Route::post('/verify/{id}/{hash}', [VerifyController::class, '__invoke'])->name('restify.verify');
                Route::middleware('auth:sanctum')->post('/logout', [LogoutController::class, '__invoke'])->name('restify.logout');
            });
        });
    }
}
