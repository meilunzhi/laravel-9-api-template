<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api as Admin;

Route::prefix('v1')->middleware('cors')->group(function () {
    Route::prefix('admins')->middleware('admin.guard')->group(function () {
        //admin路由
        Route::post('', [Admin\AdminController::class, 'store'])->name('admins.store');
        Route::post('login', [Admin\AdminController::class, 'login'])->name('admins.login');
        Route::middleware('api.refresh')->group(function () {
            Route::get('', [Admin\AdminController::class, 'index'])->name('admins.index');
            Route::get('show/{user}', [Admin\AdminController::class, 'show'])->name('admins.show');
            Route::post('logout', [Admin\AdminController::class, 'logout'])->name('admins.logout');
            //当前用户信息
            Route::get('info', [Admin\AdminController::class, 'info'])->name('admins.info');
        });
    });
});
