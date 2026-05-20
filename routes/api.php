<?php

use App\Http\Controllers\Admin\AssetRequestController;
use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\MaintenanceController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AssetController;
use Illuminate\Support\Facades\Route;

//Login routes
Route::post('/login',[LoginController::class,'login'])->name('login');

Route::post('/forgot-password', [PasswordResetController::class, 'submitForgetPasswordForm'])->name('password.email');
Route::post('/reset-password', [PasswordResetController::class, 'submitResetPasswordForm'])->name('password.update');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',[LoginController::class,'logout'])->name('logout');

    Route::post('/assetrequest',[AssetRequestController::class,'store']);
    Route::post('/admin/requests/{id}/approve',[AssetRequestController::class,'approve']);
    
    Route::resource('assignment', AssignmentController::class);
    Route::post('/assignment/{id}/release', [AssignmentController::class, 'release'])->name('assignment.release');

    Route::resource('maintenance', MaintenanceController::class);
    Route::post('/maintenance/{id}/complete', [MaintenanceController::class, 'complete'])->name('maintenance.complete');
    Route::post('/maintenance/{id}/cancel', [MaintenanceController::class, 'cancel'])->name('maintenance.cancel');

    Route::post('/user/notification',[UserController::class,'getNotification'])->name('user.getNotification');
    
    Route::middleware('permission:manage-users')->group(function () {
        Route::resource('user', UserController::class);
        Route::get('/dashboard', [DashboardController::class, 'dashboardview'])->name('dashboard.view');
    });

    Route::middleware('permission:manage-roles')->group(function () {
        Route::resource('role', RoleController::class);
    });

    Route::middleware('permission:manage-assets')->group(function () {
        Route::resource('asset', AssetController::class);
        Route::get('/dashboard', [DashboardController::class, 'dashboardview'])->name('dashboard.view');
    });

    Route::middleware('permission:manage-categories')->group(function () {
        Route::resource('category', CategoryController::class);
        Route::get('/dashboard', [DashboardController::class, 'dashboardview'])->name('dashboard.view');
    });

    Route::middleware('permission:view-assets')->group(function () {
        Route::get('/asset', [AssetController::class, 'index'])->name('assets.index');
    });

    Route::middleware('permission:view-categories')->group(function () {
        Route::get('/category', [CategoryController::class, 'index'])->name('categories.index');
    });
    
    Route::middleware('permission:view-users')->group(function () {
        Route::get('/user', [UserController::class, 'index'])->name('users.index');
    });

});


    