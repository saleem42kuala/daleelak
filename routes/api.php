<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CityController;
use App\Http\Controllers\Api\V1\CountryController;
use App\Http\Controllers\Api\V1\CriteriaController;
use App\Http\Controllers\Api\V1\FavoriteController;
use App\Http\Controllers\Api\V1\ListingController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\ReviewVoteController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // --- Public ---
    Route::get('countries', [CountryController::class, 'index']);
    Route::get('cities', [CityController::class, 'index']);
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('criteria', [CriteriaController::class, 'index']);

    Route::get('listings', [ListingController::class, 'index']);
    Route::get('listings/{listing}', [ListingController::class, 'show']);

    Route::get('reviews', [ReviewController::class, 'index']);

    Route::post('auth/social', [AuthController::class, 'social']);

    // REMOVE BEFORE PRODUCTION: dev-only test login (local env only, 403 elsewhere).
    Route::post('auth/dev-login', [AuthController::class, 'devLogin']);

    // --- Authenticated ---
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('user', [AuthController::class, 'me']);

        Route::get('reviews/mine', [ReviewController::class, 'mine']);
        Route::post('reviews', [ReviewController::class, 'store']);
        Route::post('review-votes', [ReviewVoteController::class, 'store']);

        Route::get('favorites', [FavoriteController::class, 'index']);
        Route::post('favorites', [FavoriteController::class, 'store']);
        Route::delete('favorites/{listing}', [FavoriteController::class, 'destroy']);
    });
});
