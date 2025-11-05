<?php

use App\Http\Controllers\API\Auth\AuthenticateController;
use App\Http\Controllers\API\Auth\ForgetPasswordController;
use App\Http\Controllers\API\Auth\SignUpController;
use App\Http\Controllers\API\CertificationController;
use App\Http\Controllers\API\EducationController;
use App\Http\Controllers\API\ExperienceController;
use App\Http\Controllers\API\LicenseController;
use App\Http\Controllers\API\ListController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\ServiceController;
use App\Http\Controllers\API\SkillController;
use App\Http\Controllers\API\UserAvailabilityController;
use App\Http\Controllers\API\UserPetController;
use App\Http\Controllers\API\UserServiceController;
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

    Route::post("update-profile", [ProfileController::class, "update"]);
    Route::get('user', [ProfileController::class, 'getUser']);
    Route::post("add-location", [ProfileController::class, "add_location"]);


    // **************** Pet Routes ****************
    Route::post('add-pet', [UserPetController::class, 'storeOrUpdate']);
    Route::get('get-pets', [UserPetController::class, 'index']);
    Route::get('pet/show/{pet_id}', [UserPetController::class, 'show']);
    Route::delete('pet/delete/{pet}', [UserPetController::class, 'destroy']);

    // **************** Eduction ****************
    Route::get('/educations', [EducationController::class, 'index']);
    Route::post('/educations/save', [EducationController::class, 'save']);
    Route::post('/educations/{id}', [EducationController::class, 'destroy']);

    // **************** License ****************
    Route::get('/licenses', [LicenseController::class, 'index']);
    Route::post('/licenses', [LicenseController::class, 'save']);
    Route::post('/licenses/{id}', [LicenseController::class, 'destroy']);


    // **************** List ****************
    Route::get('/lists/{type}', [ListController::class, 'getSingle']);


    // **************** Certifications ****************
    Route::get('certifications', [CertificationController::class, 'index']);
    Route::post('certification/save', [CertificationController::class, 'save']);
    Route::post('certification/{id}', [CertificationController::class, 'destroy']);


    // **************** Skills ****************
    Route::get('/skills', [SkillController::class, 'index']);
    Route::post('/skills/save', [SkillController::class, 'save']);
    Route::post('/skills/{id}', [SkillController::class, 'destroy']);

    // **************** experiences ****************
    Route::get('/experiences', [ExperienceController::class, 'index']);
    Route::post('/experiences/save', [ExperienceController::class, 'save']);
    Route::delete('/experiences/{id}', [ExperienceController::class, 'destroy']);


    // **************** Services ****************
    Route::get('/services', [ServiceController::class, 'index']);
    Route::post('/services/store', [ServiceController::class, 'save']);
    Route::post('/services/{id}', [ServiceController::class, 'destroy']);

    // **************** Get User By Service ****************
    Route::get('services/{service_id}/users', [UserServiceController::class, 'getUsersByService']);


    // **************** User Availability ****************
    Route::get('/availability', [UserAvailabilityController::class, 'index']);
    Route::post('/availability/store', [UserAvailabilityController::class, 'save']);
    Route::delete('/availability/{id}', [UserAvailabilityController::class, 'destroy']);
});
