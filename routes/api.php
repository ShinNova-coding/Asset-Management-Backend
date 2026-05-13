<?php

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\UserController;
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
});
    