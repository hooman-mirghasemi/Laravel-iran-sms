<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;
use HoomanMirghasemi\Sms\Http\Controllers\SmsController;

Route::prefix('laravel/sms')->group(function () {
    Route::get('get-sms-list', [SmsController::class, 'getList'])->name('sms.getList');
});
