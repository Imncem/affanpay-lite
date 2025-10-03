<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required','email'],
            'password' => ['required','min:4'],
        ]);

        // UI-only: simulate getting a token from Affanpay
        $fakeToken = 'demo-token-'.bin2hex(random_bytes(8));
        $request->session()->put('affanpay_token', $fakeToken);
        $request->session()->put('affanpay_email', $request->email);

        // go to intended page or calculator
        $intended = session('intended_url') ?? route('calculator');
        session()->forget('intended_url');

        return redirect($intended)->with('status', 'Logged in.');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['affanpay_token','affanpay_email']);
        return redirect()->route('calculator')->with('status', 'Logged out.');
    }
}
