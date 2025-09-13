<?php

use App\Http\Controllers\API\Auth\AuthenticateController;
use App\Http\Controllers\API\Auth\ForgetPasswordController;
use App\Http\Controllers\API\Auth\SignUpController;
use App\Http\Controllers\API\UserPetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Pet name type breed gender castration true false age photo

Route::post('signup', [SignUpController::class, 'signUp']);
Route::post("email/change", [SignupController::class, "changeEmail"]);
Route::post("verify-email", [SignupController::class, "verifyEmail"]);
Route::post("login", [AuthenticateController::class, "login"]);


Route::middleware('throttle:5,1')->group(function () {
    Route::post('forget-password', [ForgetPasswordController::class, 'forget_password']);
    Route::post('otp-verification', [ForgetPasswordController::class, 'otp_verification']);
    Route::post('reset-password', [ForgetPasswordController::class, 'reset_password']);
});
Route::middleware("auth:api")->group(function () {
    Route::post("change-password", [AuthenticateController::class, "change_password"]);
    Route::post("logout", [AuthenticateController::class, "logout"]);
    Route::post("add-location", [SignupController::class, "add_location"]);


    // **************** Pet Routes ****************
    Route::post('add-pet', [UserPetController::class, 'storeOrUpdate']);
    Route::get('get-pets', [UserPetController::class, 'index']);
    Route::get('pet/show/{pet_id}', [UserPetController::class, 'show']);
    Route::delete('pet/delete/{pet}', [UserPetController::class, 'destroy']);
});
