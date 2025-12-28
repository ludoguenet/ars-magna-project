<?php

use AppModules\Invoice\src\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('invoices')->name('invoice::')->group(function () {
    Route::resource('/', InvoiceController::class)->parameters(['' => 'id']);
    Route::post('/{id}/finalize', [InvoiceController::class, 'finalize'])->name('finalize');
    Route::post('/{id}/mark-as-paid', [InvoiceController::class, 'markAsPaid'])->name('mark-as-paid');
});
