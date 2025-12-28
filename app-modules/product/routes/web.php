<?php

use AppModules\Product\src\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('products')->name('product::')->group(function () {
    Route::resource('/', ProductController::class)->parameters(['' => 'id']);
});
