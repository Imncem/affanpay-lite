@extends('layouts.app')

@section('content')
<article>
  <header>
    <h2>Enter Total</h2>
  </header>

  <form method="POST" action="{{ route('pay') }}">
    @csrf
    <input type="hidden" name="amount" id="amountHidden" />

    <div style="border:1px solid #eee; border-radius:12px; padding:1rem; margin-bottom:1rem;">
      <div class="muted">Amount (MYR)</div>
      <div id="amountDisplay" class="amount-display" style="text-align:right;">RM 0.00</div>
      <div class="muted"><span id="amountRaw">0.00</span></div>
    </div>

    @error('amount')
      <small style="color:#ef4444;">{{ $message }}</small>
    @enderror

    <div class="keypad">
      <button type="button" class="secondary" data-key="7">7</button>
      <button type="button" class="secondary" data-key="8">8</button>
      <button type="button" class="secondary" data-key="9">9</button>

      <button type="button" class="secondary" data-key="4">4</button>
      <button type="button" class="secondary" data-key="5">5</button>
      <button type="button" class="secondary" data-key="6">6</button>

      <button type="button" class="secondary" data-key="1">1</button>
      <button type="button" class="secondary" data-key="2">2</button>
      <button type="button" class="secondary" data-key="3">3</button>

      <button type="button" class="secondary" data-key="0">00</button>
      <button type="button" class="secondary" data-key="0">0</button>
      <button type="button" class="contrast" data-key="DEL">DEL</button>

      <button type="button" class="outline" data-key="CLR" style="grid-column:1/4;">Clear</button>
    </div>

    <footer style="margin-top:1rem;">
      <button id="payBtn" type="submit" disabled>
        {{ session('affanpay_token') ? 'Pay' : 'Login to Accept Payment' }}
      </button>
    </footer>
  </form>
</article>
@endsection
