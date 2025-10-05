<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function calculator(Request $request)
    {
        return view('calculator');
    }

    /**
     * Handle Pay from calculator:
     * - Validate amount
     * - If not logged in → send to login and remember intended QR URL
     * - If logged in → go to QR page (UI will create intent & poll)
     */
    public function pay(Request $request)
    {
        $request->validate([
            'amount' => ['required','regex:/^\d+(\.\d{1,2})?$/','gt:0'],
        ]);

        $amount = $this->normalizeAmount($request->input('amount'));

        if (!$request->session()->has('affanpay_token')) {
            // After login, continue to QR page with the same amount
            session(['intended_url' => route('qr', ['amount' => $amount])]);
            return redirect()->route('login.show')->with('error', 'Please login to proceed.');
        }

        // UI-only: go to QR page (QR page will create intent and poll status)
        return redirect()->route('qr', ['amount' => $amount]);
    }

    /**
     * QR page: shows only QR + countdown + cancel.
     * JS polls status; on PAID -> /paid, on expiry -> /qr/expired.
     */
    public function qr(Request $request)
    {
        $amount = $this->normalizeAmount($request->query('amount', '0.00'));
        return view('qr', compact('amount'));
    }

    /**
     * Paid confirmation page.
     */
    public function paid(Request $request)
    {
        $amount = $this->normalizeAmount($request->query('amount', '0.00'));
        return view('paid', compact('amount'));
    }

    /**
     * QR expired / payment failed page.
     * Shows ❌ and a "Regenerate QR" button back to /qr?amount=...
     */
    public function qrExpired(Request $request)
    {
        $amount = $this->normalizeAmount($request->query('amount', '0.00'));
        return view('qr_failed', compact('amount'));
    }

    /**
     * Ensure amount is a numeric string with 2 decimals.
     */
    private function normalizeAmount($value): string
    {
        $n = (float) ($value ?? 0);
        return number_format($n, 2, '.', '');
    }
}
