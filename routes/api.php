<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UsersController;
use App\Http\Controllers\API\MsDriverController;
use App\Http\Controllers\API\MsTrackingTruckController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
// TrsTrackingDriver


Route::prefix('v1')->group(function () {
    // Public routes
    // Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);

    // Protected routes
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/users', [UsersController::class, 'index']);
        Route::get('/users/{id}', [UsersController::class, 'show']);
        Route::post('/users', [UsersController::class, 'store']);
        Route::put('/users/{id}', [UsersController::class, 'update']);
        Route::delete('/users/{id}', [UsersController::class, 'destroy']);

        Route::get('/ms_driver', [MsDriverController::class, 'index']);
        Route::get('/ms_driver/{id}', [MsDriverController::class, 'show']);
        Route::post('/ms_driver', [MsDriverController::class, 'store']);
        Route::put('/ms_driver/{id}', [MsDriverController::class, 'update']);
        Route::delete('/ms_driver/{id}', [MsDriverController::class, 'destroy']);

        Route::get('/ms_tracking', [MsTrackingTruckController::class, 'index']);
        Route::get('/ms_tracking/{id}', [MsTrackingTruckController::class, 'show']);
        Route::post('/ms_tracking', [MsTrackingTruckController::class, 'store']);
        Route::put('/ms_tracking/{id}', [MsTrackingTruckController::class, 'update']);
        Route::delete('/ms_tracking/{id}', [MsTrackingTruckController::class, 'destroy']);
    });

});


