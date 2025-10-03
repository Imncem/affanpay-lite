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

// QR + Paid (require token)
Route::get('/qr', [PaymentController::class, 'qr'])->middleware('affanpay.token')->name('qr');
Route::get('/paid', [PaymentController::class, 'paid'])->middleware('affanpay.token')->name('paid');

// ---------- TEMP: Mock API for dynamic QR ----------
Route::prefix('api')->middleware('affanpay.token')->group(function () {
    // Create a payment intent -> { id, qr_url, status, expires_at }
    Route::post('/payment-intents', [MockPaymentApiController::class, 'create']);

    // Get intent status -> { id, status, paid_at? }
    Route::get('/payment-intents/{id}', [MockPaymentApiController::class, 'show']);

    // ✅ Mark as paid (demo) — NOTE: no extra /api here
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
