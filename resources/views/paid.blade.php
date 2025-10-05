@extends('layouts.app')

@section('content')
<article style="text-align:center;">
  <h2 style="color:#16a34a;">Payment Received</h2>

  <div style="margin:1.5rem 0;">
    <img src="https://img.icons8.com/emoji/96/check-mark-emoji.png" alt="Paid" width="96" height="96">
    <p class="muted">Amount</p>
    <h3>RM {{ number_format((float)$amount, 2) }}</h3>
  </div>

  <footer style="margin-top:1.5rem;">
    <a href="{{ route('calculator') }}" role="button" class="primary">New Sale</a>
  </footer>
</article>
@endsection
