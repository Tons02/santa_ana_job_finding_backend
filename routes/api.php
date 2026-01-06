<?php

use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// User Controller
Route::put('user-archived/{id}', [UserController::class, 'archived']);
Route::resource("user", UserController::class);

// Skill Controller
Route::put('skill-archived/{id}', [SkillController::class, 'archived']);
Route::resource("skill", SkillController::class);
