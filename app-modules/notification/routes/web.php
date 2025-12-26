<?php

use AppModules\Notification\src\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::prefix('notifications')->name('notification::')->middleware('auth')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::get('/{id}', [NotificationController::class, 'show'])->name('show');
    Route::patch('/{id}/read', [NotificationController::class, 'markAsRead'])->name('markAsRead');
    Route::patch('/read-all', [NotificationController::class, 'markAllAsRead'])->name('markAllAsRead');
    Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
});
