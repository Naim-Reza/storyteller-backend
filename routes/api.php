<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

//stories routes
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/stories', StoryController::class);
});
Route::get('/web/stories', [StoryController::class, 'index']);
Route::get('/web/stories/{story}', [StoryController::class, 'show']);
Route::get('/web/recent_stories', [StoryController::class, 'recentstories']);

//user routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'index']);
});

//auth routes
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
