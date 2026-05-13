<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/user', [UserController::class,'index'])->name('user.index');

Route::get('/test', function () {

    return response()->json([
        'message' => 'API working'
    ]);

});

Route::post('/user',[UserController::class,'store'])->name('user.store');

Route::get('/user/{id}',[UserController::class,'show'])->name('user.show');

Route::patch('/user/{id}',[UserController::class,'update'])->name('user.update');

Route::delete('/user/{id}',[UserController::class,'destroy'])->name('user.destroy');