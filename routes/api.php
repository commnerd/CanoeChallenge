<?php

use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function() {    
    Route::get('funds/duplicate', [\App\Http\Controllers\DuplicateFundController::class, 'index'])->name('duplicate_funds.index');
    Route::resource('funds', \App\Http\Controllers\FundController::class);
});
