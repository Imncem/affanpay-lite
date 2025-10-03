@extends('layouts.app')

@section('content')
<article style="text-align:center;">
  <header>
    <h2 style="color:#065f46;">Payment Received</h2>
    <p class="muted">This is a confirmation screen for demo purposes</p>
  </header>

  <div style="font-size:4rem; line-height:1;">✅</div>
  <div class="muted">Amount</div>
  <div style="font-size:2.2rem; font-weight:800; margin:.2rem 0;">
    RM {{ number_format((float)request('amount', 0), 2) }}
  </div>

  <footer style="margin-top:1rem;">
    <a href="{{ route('calculator') }}" role="button">New Sale</a>
  </footer>
</article>
@endsection
