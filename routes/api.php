<?php

use App\Http\Controllers\TeamsController;
use App\Http\Controllers\UsersController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('users/login', [UsersController::class, 'login'])->name('login');

Route::middleware(['auth:sanctum', IsAdmin::class])->group(function () {
    //Admin routes

    //Users
    Route::post('users', [UsersController::class, 'store']);
    Route::patch('users/{user}', [UsersController::class, 'update']);
    Route::delete('users/{user}', [UsersController::class, 'destroy']);

    //Teams
    Route::post('teams', [TeamsController::class, 'store']);
    Route::patch('teams/{team}', [TeamsController::class, 'update']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
