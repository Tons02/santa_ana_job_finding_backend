<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\AvailableJobController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('login', [AuthController::class, 'login'])->name('login');




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
});

