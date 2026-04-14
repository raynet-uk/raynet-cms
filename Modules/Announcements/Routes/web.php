<?php

use Modules\Announcements\Http\Controllers\AnnouncementsController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::prefix('news')->name('announcements.')->group(function () {
    Route::get('/',           [AnnouncementsController::class, 'index'])   ->name('index');
    Route::get('/{slug}',     [AnnouncementsController::class, 'show'])    ->name('show');
});

// Admin routes — protected by your existing auth + admin middleware
Route::prefix('admin/news')->name('admin.announcements.')->middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/',           [AnnouncementsController::class, 'adminIndex'])  ->name('index');
    Route::get('/create',     [AnnouncementsController::class, 'create'])      ->name('create');
    Route::post('/',          [AnnouncementsController::class, 'store'])       ->name('store');
    Route::get('/{id}/edit',  [AnnouncementsController::class, 'edit'])        ->name('edit');
    Route::put('/{id}',       [AnnouncementsController::class, 'update'])      ->name('update');
    Route::delete('/{id}',    [AnnouncementsController::class, 'destroy'])     ->name('destroy');
});
