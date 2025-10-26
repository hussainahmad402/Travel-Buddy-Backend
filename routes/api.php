<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('send-otp', [AuthController::class, 'sendOtp']);
Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('forgot-password', [AuthController::class, 'forgotpassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);
Route::middleware('auth:api')->group(
    function () {
        Route::get('/profile', [UserController::class, 'getProfile']);
        Route::put('/profile', [UserController::class, 'updateProfile']);
        Route::delete('/profile', [UserController::class, 'deleteProfile']);
    }
);
Route::middleware('auth:api')->group(
    function () {
        Route::post('/trips', [TripController::class, 'store']);
        Route::get('/trips', [TripController::class, 'showAllTrips']);
        Route::get('/trips/getfavourites', [TripController::class, 'listFavouriteTrips']);
        Route::get('/trips/{id}', [TripController::class, 'viewTrip']);
        Route::put('/trips/{id}', [TripController::class, 'updateTrip']);
        Route::delete('/trips/{id}', [TripController::class, 'destroyTrip']);
        Route::post('/trips/{id}/documents', [TripController::class, 'uploadDocument']);
        Route::get('/trips/{id}/documents', [TripController::class, 'listDocuments']);
        Route::delete('/trips/{id}/documents', [TripController::class, 'deleteDocuments']);
        Route::post('/trips/{id}/favourite', [TripController::class, 'markAsFavourite']);
        Route::delete('/trips/{id}/favourite', [TripController::class, 'unmarkAsFavourite']);
    }
);
// ->middleware('auth:sanctum');
