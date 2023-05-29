<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;

Route::prefix('v1')->middleware('cors')->group(function () {
    Route::prefix('users')->middleware('user.guard')->group(function () {
        //user路由
        Route::post('', [Api\UserController::class, 'store'])->name('users.store');
        Route::post('login', [Api\UserController::class, 'login'])->name('users.login');
        Route::middleware('api.refresh')->group(function () {
            Route::get('', [Api\UserController::class, 'index'])->name('users.index');
            Route::get('show/{user}', [Api\UserController::class, 'show'])->name('users.show');
            Route::post('logout', [Api\UserController::class, 'logout'])->name('users.logout');
            //当前用户信息
            Route::get('info', [Api\UserController::class, 'info'])->name('users.info');
        });
    });
});
