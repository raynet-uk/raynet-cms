<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlertStatusApiController;

// Placeholder API routes - your app doesn't really use these yet.
Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

// Alert status API (GET or POST)
Route::match(['get', 'post'], '/alerts/level', [AlertStatusApiController::class, 'handle'])
    ->name('api.alert-status.handle');