<?php

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AssetController;
use Illuminate\Support\Facades\Route;

//Login routes
Route::post('/login',[LoginController::class,'login'])->name('login');
Route::post('/logout',[LoginController::class,'logout'])->middleware('auth:sanctum');

//User routes 
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class,'index'])->name('user.index');
    Route::post('/user',[UserController::class,'store'])->name('user.store');
    Route::get('/user/{id}',[UserController::class,'show'])->name('user.show');
    Route::patch('/user/{id}',[UserController::class,'update'])->name('user.update');
    Route::delete('/user/{id}',[UserController::class,'destroy'])->name('user.destroy');

    // Category routes
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{id}', [CategoryController::class, 'show'])->name('categories.show');
    Route::patch('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
    Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
    Route::get('/assets/{id}', [AssetController::class, 'show'])->name('assets.show');
    Route::patch('/assets/{id}', [AssetController::class, 'update'])->name('assets.update');
    Route::delete('/assets/{id}', [AssetController::class, 'destroy'])->name('assets.destroy');
});


    