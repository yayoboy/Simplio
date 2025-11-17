<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PageBlockController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\SiteController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\ThemeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Authentication routes (public)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Auth user
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Sites
    Route::apiResource('sites', SiteController::class);
    Route::post('sites/{site}/publish', [SiteController::class, 'publish']);
    Route::post('sites/{site}/unpublish', [SiteController::class, 'unpublish']);
    Route::post('sites/{site}/duplicate', [SiteController::class, 'duplicate']);

    // Pages
    Route::apiResource('sites.pages', PageController::class);
    Route::post('pages/{page}/publish', [PageController::class, 'publish']);
    Route::post('pages/{page}/unpublish', [PageController::class, 'unpublish']);
    Route::post('pages/{page}/duplicate', [PageController::class, 'duplicate']);
    Route::post('pages/{page}/set-home', [PageController::class, 'setAsHome']);

    // Page Blocks
    Route::apiResource('pages.blocks', PageBlockController::class);
    Route::post('blocks/reorder', [PageBlockController::class, 'reorder']);

    // Media
    Route::apiResource('sites.media', MediaController::class);
    Route::post('media/{media}/regenerate', [MediaController::class, 'regenerateVariants']);

    // Themes
    Route::apiResource('themes', ThemeController::class);
    Route::get('themes/global', [ThemeController::class, 'global']);
});
