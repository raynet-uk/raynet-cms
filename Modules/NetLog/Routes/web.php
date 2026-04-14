<?php

use Modules\NetLog\Http\Controllers\NetLogController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/net-log')->name('admin.netlog.')->middleware(['web', 'admin'])->group(function () {
    Route::get('/',                        [NetLogController::class, 'index'])        ->name('index');
    Route::post('/sessions',               [NetLogController::class, 'openSession'])  ->name('sessions.open');
    Route::post('/sessions/{id}/close',    [NetLogController::class, 'closeSession']) ->name('sessions.close');
    Route::delete('/sessions/{id}',        [NetLogController::class, 'deleteSession'])->name('sessions.delete');
    Route::post('/sessions/{id}/checkins', [NetLogController::class, 'addCheckin'])   ->name('checkins.add');
    Route::delete('/checkins/{id}',        [NetLogController::class, 'deleteCheckin'])->name('checkins.delete');
    Route::get('/sessions/{id}/export',    [NetLogController::class, 'export'])       ->name('sessions.export');
});
