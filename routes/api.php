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

    Route::resource('user', UserController::class);
    Route::resource('category', CategoryController::class);
    Route::resource('asset', AssetController::class);
    Route::resource('role', RoleController::class);
});


    