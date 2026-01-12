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
Route::get("skill", [SkillController::class, 'index']);




Route::middleware(['auth:sanctum'])->group(function () {
    // User Controller
    Route::get('applicant-resume/{user}', [UserController::class, 'viewResume']);
    Route::post('update-user/{user}', [UserController::class, 'update_user']);
    Route::post('update-resume/{user}', [UserController::class, 'update_resume']);
    Route::put('user-archived/{id}', [UserController::class, 'archived']);
    Route::resource("user", UserController::class);

    // auth controller
    Route::patch('changepassword', [AuthController::class, 'changedPassword']);
    Route::patch('change-email/{id}', [AuthController::class, 'changeEmail']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::patch('resetpassword/{id}', [AuthController::class, 'resetPassword']);

    // Skill Controller
    Route::put('skill-archived/{id}', [SkillController::class, 'archived']);
    Route::post("skill", [SkillController::class, 'store']);
    Route::patch("skill/{id}", [SkillController::class, 'update']);

    // Job Controller
    Route::put('job-archived/{id}', [AvailableJobController::class, 'archived']);
    Route::resource("job", AvailableJobController::class);

    // Job Application Controller
    Route::put('job-application-archived/{id}', [JobApplicationController::class, 'archived']);
    Route::patch('job-application-view/{id}', [JobApplicationController::class, 'job_application_view']);
    Route::resource("job-application", JobApplicationController::class);
});
