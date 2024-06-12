<?php

use App\Http\Controllers\LeavesController;
use App\Http\Controllers\TeamsController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('users/login', [UsersController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    //Admin routes
    Route::middleware('isRole:admin')->group(function () {
        //Users
        Route::post('users', [UsersController::class, 'store']);
        Route::patch('users/{user}', [UsersController::class, 'update']);
        Route::delete('users/{user}', [UsersController::class, 'destroy']);

        //Teams
        Route::post('teams', [TeamsController::class, 'store']);
        Route::patch('teams/{team}', [TeamsController::class, 'update']);
    });

    //Manager, regular
    Route::middleware('isRole:manager,regular')->group(function () {
        Route::get('leaves', [LeavesController::class, 'getActiveLeavesData']);
        Route::post('leaves/{leave}/change-status', [LeavesController::class, 'changeStatus']);
    });

    //Regular
    Route::middleware('isRole:regular')->group(function () {
        Route::get('leaves/history', [LeavesController::class, 'getLeavesHistory']);
        Route::post('leaves', [LeavesController::class, 'store']);
    });
});


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
