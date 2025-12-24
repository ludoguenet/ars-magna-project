<?php

use AppModules\Client\src\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('clients')->name('client::')->group(function () {
    Route::get('/', [ClientController::class, 'index'])->name('index');
    Route::get('/create', [ClientController::class, 'create'])->name('create');
    Route::post('/', [ClientController::class, 'store'])->name('store');
    Route::get('/{id}', [ClientController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [ClientController::class, 'edit'])->name('edit');
    Route::put('/{id}', [ClientController::class, 'update'])->name('update');
    Route::delete('/{id}', [ClientController::class, 'destroy'])->name('destroy');
});
