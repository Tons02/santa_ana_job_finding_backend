<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\AvailableJobController;
use App\Http\Controllers\Api\JobApplicationController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('user-registration', [UserController::class, 'user_registration'])->name('user-registration');




Route::middleware(['auth:sanctum'])->group(function () {
    // User Controller
    Route::put('user-archived/{id}', [UserController::class, 'archived']);
    Route::resource("user", UserController::class);

    // auth controller
    Route::patch('changepassword', [AuthController::class, 'changedPassword']);
    Route::patch('change-email/{id}', [AuthController::class, 'changeEmail']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::patch('resetpassword/{id}', [AuthController::class, 'resetPassword']);

    // Skill Controller
    Route::put('skill-archived/{id}', [SkillController::class, 'archived']);
    Route::resource("skill", SkillController::class);

    // Job Controller
    Route::put('job-archived/{id}', [AvailableJobController::class, 'archived']);
    Route::resource("job", AvailableJobController::class);

    // // Job Application Controller
    // Route::put('job-application-archived/{id}', [JobApplicationController::class, 'archived']);
    // Route::patch('job-application-view/{id}', [JobApplicationController::class, 'job_application_view']);
    // Route::resource("job-application", JobApplicationController::class);

    // Route::get('/download-resume/{user}', [UserController::class, 'downloadResume']);
});
