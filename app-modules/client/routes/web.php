<?php

use AppModules\Client\src\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('clients')->name('client::')->group(function () {
    Route::resource('/', ClientController::class)->parameters(['' => 'id']);
});
