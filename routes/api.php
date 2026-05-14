<?php

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AssetController;
use Illuminate\Support\Facades\Route;

//Login routes
Route::post('/login',[LoginController::class,'login'])->name('login');

//User routes 
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',[LoginController::class,'logout'])->name('logout');


    Route::get('/user', [UserController::class,'index'])->name('user.index');
    Route::post('/user',[UserController::class,'store'])->name('user.store');
    Route::get('/user/{id}',[UserController::class,'show'])->name('user.show');
    Route::patch('/user/{id}',[UserController::class,'update'])->name('user.update');
    Route::delete('/user/{id}',[UserController::class,'destroy'])->name('user.destroy');

    // Category routes
    Route::get('/category', [CategoryController::class, 'index'])->name('category.index');
    Route::post('/category', [CategoryController::class, 'store'])->name('category.store');
    Route::get('/category/{id}', [CategoryController::class, 'show'])->name('category.show');
    Route::patch('/category/{id}', [CategoryController::class, 'update'])->name('category.update');
    Route::delete('/category/{id}', [CategoryController::class, 'destroy'])->name('category.destroy');

    //Asset routes
    Route::get('/asset', [AssetController::class, 'index'])->name('asset.index');
    Route::post('/asset', [AssetController::class, 'store'])->name('asset.store');
    Route::get('/asset/{id}', [AssetController::class, 'show'])->name('asset.show');
    Route::patch('/asset/{id}', [AssetController::class, 'update'])->name('asset.update');
    Route::delete('/asset/{id}', [AssetController::class, 'destroy'])->name('asset.destroy');
});


    