<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Routing\Controller;

class MockPaymentApiController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'amount' => ['required','regex:/^\d+(\.\d{1,2})?$/','gt:0'],
            'currency' => ['nullable','in:MYR'],
        ]);

        $id = 'pi_' . Str::random(10);
        $amount = $request->amount;
        $qrText = urlencode("AFFANPAY|AMT=$amount|REF=$id");
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={$qrText}";
        $expiresAt = now()->addMinutes(5)->toIso8601String();

        $intents = session('mock_intents', []);
        $intents[$id] = [
            'id' => $id,
            'amount' => $amount,
            'currency' => 'MYR',
            'status' => 'PENDING',
            'qr_url' => $qrUrl,
            'expires_at' => $expiresAt,
            'paid_at' => null,
        ];
        session(['mock_intents' => $intents]);

        return response()->json($intents[$id]);
    }

    public function show($id)
    {
        $intents = session('mock_intents', []);
        if (!isset($intents[$id])) {
            return response()->json(['message' => 'Not found'], 404);
        }

        // Auto-expire after 5 minutes
        if (now()->gt($intents[$id]['expires_at'])) {
            $intents[$id]['status'] = $intents[$id]['status'] === 'PAID' ? 'PAID' : 'EXPIRED';
            session(['mock_intents' => $intents]);
        }

        return response()->json($intents[$id]);
    }
}
