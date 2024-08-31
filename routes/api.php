<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\PostController;
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
Route::controller(AuthController::class)->group( function () {
    Route::post('login', 'login')->name('login');
    Route::post('register', 'register');
    Route::post('verify', 'verify');
});
Route::middleware(['auth:sanctum', 'isVerified'])->group( function () {
    Route::any('logout', [AuthController::class, 'logout'])->name('logout');
    Route::prefix('tags')->controller(TagController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/{tag}', 'show');
        Route::post('store/', 'store');
        Route::delete('/{tag}', 'destroy');
        Route::put('update/{tag}', 'update');
    });
    Route::prefix('posts')->controller(PostController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/deleted', 'getDeletedPosts');
        Route::get('/{post}', 'show');
        Route::post('store/', 'store');
        Route::delete('/{post}', 'destroy');
        Route::put('update/{post}', 'update');
        Route::get('/restore/{post_id}', 'restoreDeletedPost');
    });
});
