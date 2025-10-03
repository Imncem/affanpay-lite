<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAffanpayToken
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->has('affanpay_token')) {
            // remember where user wanted to go
            session(['intended_url' => $request->fullUrl()]);
            return redirect()->route('login.show')->with('error', 'Please login first.');
        }
        return $next($request);
    }
}
