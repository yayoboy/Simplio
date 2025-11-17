<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;

// Public site routes (must be before SPA catch-all)
Route::get('/site/{site}', [PublicController::class, 'showSite'])->name('public.site');
Route::get('/site/{site}/{page}', [PublicController::class, 'showPage'])->name('public.page');

// SPA - Serve Vue app for all routes
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
