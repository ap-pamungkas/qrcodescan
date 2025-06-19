<?php

use App\Http\Controllers\LogPerangkatController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/log-perangkat', [LogPerangkatController::class, 'create'])->name('log_perangkat.create');
Route::post('/log-perangkat', [LogPerangkatController::class, 'store'])->name('log_perangkat.store');
