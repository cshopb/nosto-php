<?php

use App\Http\Controllers\CurrencyExchange\CurrencyExchangerController;
use Illuminate\Support\Facades\Route;

Route::get(
    '/',
    CurrencyExchangerController::class,
)->name('exchange');
