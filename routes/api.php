<?php

use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function() {    
    Route::resource('funds', \App\Http\Controllers\FundController::class);
    Route::get('funds/duplicate', [\App\Http\Controllers\DuplicateFundController::class, 'index']);
});
