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

use HoomanMirghasemi\Sms\Http\Controllers\SmsController;
use Illuminate\Support\Facades\Route;

Route::prefix('laravel/sms')->group(function () {
    Route::get('get-sms-list', [SmsController::class, 'getList'])->name('sms.getList');
});
