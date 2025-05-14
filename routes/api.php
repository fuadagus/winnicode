<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\LocationController;
use App\Http\Controllers\API\NewsController;

Route::apiResource('users', UserController::class);
Route::apiResource('locations', LocationController::class);
Route::apiResource('news', NewsController::class);
