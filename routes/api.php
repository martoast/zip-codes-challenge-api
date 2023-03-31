<?php

use App\Http\Controllers\ZipCodeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('zip-codes')->group(function () {
    Route::get('/', [ZipCodeController::class, 'index'])->name('zip-codes.index');
    Route::get('/{zip_code}', [ZipCodeController::class, 'show'])->name('zip-codes.show');
});

Route::get('/live', function (Request $request) {
    return ['success' => true];
});