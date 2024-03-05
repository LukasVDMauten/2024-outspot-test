<?php

use App\Http\Controllers\MolliePaymentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::get('/', [PaymentController::class, 'index'])->name('home');

Route::post('/create-payment', [MolliePaymentController::class, 'createPayment'])->name('create.payment');
Route::get('/payment-success', [MolliePaymentController::class, 'paymentSuccess'])->name('payment.success');
Route::get('/webhook/mollie', [MolliePaymentController::class, 'handleWebhookMollie'])->name("webhook.mollie");
