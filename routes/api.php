<?php

use App\Http\Controllers\Api\BookController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->namespace('App\Http\Controllers\Api')->group(function () {
    Route::prefix('/books/')->group(function () {
       Route::get('/best-sellers/history', [BookController::class, 'getBestSellersHistory']);
    });
});
