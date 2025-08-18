<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| These routes are automatically loaded by the RouteServiceProvider
| and will be assigned to the "api" middleware group.
|
*/

// Example public route
Route::get('/hello', function () {
    return response()->json(['message' => 'Hello from API!']);
});

// Example protected route (requires auth:sanctum if you enable it later)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('send-otp',[AuthController::class,'sendOtp']); 
Route::post('verify-otp',[AuthController::class,'verifyOtp']);