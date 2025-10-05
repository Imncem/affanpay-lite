<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\MockPaymentApiController;

// ---------- UI pages ----------
Route::get('/', [PaymentController::class, 'calculator'])->name('calculator');
Route::post('/pay', [PaymentController::class, 'pay'])->name('pay');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login.show');
Route::post('/login', [AuthController::class, 'login'])->name('login.perform');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// QR + Paid + Expired (require token)
Route::middleware('affanpay.token')->group(function () {
    Route::get('/qr', [PaymentController::class, 'qr'])->name('qr');
    Route::get('/qr/expired', [PaymentController::class, 'qrExpired'])->name('qr.expired');   // ✅ add this
    Route::get('/paid', [PaymentController::class, 'paid'])->name('paid');
});

// ---------- TEMP: Mock API for dynamic QR ----------
Route::prefix('api')->middleware('affanpay.token')->group(function () {
    Route::post('/payment-intents', [MockPaymentApiController::class, 'create']);
    Route::get('/payment-intents/{id}', [MockPaymentApiController::class, 'show']);
    Route::post('/payment-intents/{id}/mark-paid', function ($id) {
        $intents = session('mock_intents', []);
        if (isset($intents[$id])) {
            $intents[$id]['status']  = 'PAID';
            $intents[$id]['paid_at'] = now()->toIso8601String();
            session(['mock_intents' => $intents]);
        }
        return response()->json(['ok' => true]);
    });
});
