@extends('layouts.app')

@section('content')
<article style="text-align:center;">
  <h2 style="color:#b91c1c;">Payment Failed</h2>
  <p class="muted">QR code expired</p>

  <div style="font-size:4rem; line-height:1; margin:1rem 0;">❌</div>

  <div class="muted">Amount</div>
  <div style="font-size:2rem; font-weight:800; margin:.2rem 0;">
    RM {{ number_format((float)$amount, 2) }}
  </div>

  <footer style="margin-top:1.25rem; display:flex; gap:.5rem; justify-content:center;">
    <a href="{{ route('qr', ['amount' => $amount]) }}" role="button">Regenerate QR Code</a>
    <a href="{{ route('calculator') }}" role="button" class="secondary">Back to Calculator</a>
  </footer>
</article>
@endsection

