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

use HoomanMirghasemi\Sms\Http\Controllers\Web\SmsController;
use Illuminate\Support\Facades\Route;

Route::get('sms/get-sms-list', [SmsController::class, 'index'])
    ->name('sms.index');
