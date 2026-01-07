<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/download-resume/{user}', [UserController::class, 'downloadResume'])
    ->middleware('auth');
