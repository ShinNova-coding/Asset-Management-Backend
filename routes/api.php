<?php

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AssetController;
use Illuminate\Support\Facades\Route;

//Login routes
Route::post('/login',[LoginController::class,'login'])->name('login');

//User routes 
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',[LoginController::class,'logout'])->name('logout');

    Route::middleware('permission:manage-users')->group(function () {
        Route::resource('user', UserController::class);
    });

    Route::middleware('permission:manage-roles')->group(function () {
        Route::resource('role', RoleController::class);
    });

    Route::middleware('permission:manage-assets')->group(function () {
        Route::resource('asset', AssetController::class);
    });

    Route::middleware('permission:manage-categories')->group(function () {
        Route::resource('category', CategoryController::class);
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


    