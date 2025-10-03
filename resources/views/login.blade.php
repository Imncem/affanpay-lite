@extends('layouts.app')

@section('content')
<article>
  <header>
    <h2>Merchant Login</h2>
    <p class="muted">UI-only: simulates Affanpay token</p>
  </header>

  <form method="POST" action="{{ route('login.perform') }}">
    @csrf
    <label>
      Email
      <input type="email" name="email" required placeholder="you@store.com" value="{{ old('email') }}">
      @error('email') <small style="color:#ef4444;">{{ $message }}</small> @enderror
    </label>

    <label>
      Password
      <input type="password" name="password" required>
      @error('password') <small style="color:#ef4444;">{{ $message }}</small> @enderror
    </label>

    <button type="submit">Login</button>
    <a href="{{ route('calculator') }}" role="button" class="secondary outline">Cancel</a>
  </form>
</article>
@endsection
