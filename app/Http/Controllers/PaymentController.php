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
     * - If logged in → go to QR page (UI will call /api/payment-intents and poll status)
     */
    public function pay(Request $request)
    {
        $request->validate([
            'amount' => ['required','regex:/^\d+(\.\d{1,2})?$/','gt:0'],
        ]);

        $amount = $request->input('amount');

        if (!$request->session()->has('affanpay_token')) {
            // after login, continue to QR page with the same amount
            session(['intended_url' => route('qr', ['amount' => $amount])]);
            return redirect()->route('login.show')->with('error', 'Please login to proceed.');
        }

        // UI-only: go to QR page (QR page will create intent and poll status)
        return redirect()->route('qr', ['amount' => $amount]);
    }

    /**
     * QR page: shows the QR image (from mock/real API) and polls status until PAID.
     */
    public function qr(Request $request)
    {
        $amount = $request->query('amount', '0.00');
        return view('qr', compact('amount'));
    }

    /**
     * Paid confirmation page.
     */
    public function paid(Request $request)
    {
        $amount = $request->query('amount', '0.00');
        return view('paid', compact('amount'));
    }
}
