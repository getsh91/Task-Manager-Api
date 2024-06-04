<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\Payment\PaymentController;



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
Route::get('/payments', [PaymentController::class, 'index'])->middleware('auth:sanctum');
Route::post('/payment/store', [PaymentController::class, 'paymentStore'])->middleware('auth:sanctum');



Route::get('/test', function () {
    return response(['message' => 'Hello World!'], 200);
});
Route::post('/register', [AuthenticationController::class, 'register']);
Route::post('/login', [AuthenticationController::class, 'login']);