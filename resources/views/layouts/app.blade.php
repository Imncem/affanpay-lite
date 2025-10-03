<!doctype html>
<html lang="en" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Affanpay Lite</title>
  <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@2/css/pico.min.css">
  <style>
    .keypad { display: grid; grid-template-columns: repeat(3, 1fr); gap: .6rem; }
    .amount-display { font-size: 2.2rem; font-weight: 700; letter-spacing: .5px; }
    .muted { color:#6b7280; font-size:.85rem; }
  </style>
</head>
<body class="h-full">

<header class="container-fluid" style="border-bottom:1px solid #eee;">
  <nav class="container">
    <ul>
      <li><strong>Affanpay Lite</strong></li>
    </ul>
    <ul>
      @if(session('affanpay_token'))
        <li class="muted">Logged in as {{ session('affanpay_email') }}</li>
        <li>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="secondary outline">Logout</button>
          </form>
        </li>
      @else
        <li><a href="{{ route('login.show') }}" role="button">Login</a></li>
      @endif
    </ul>
  </nav>
</header>

<main class="container">
  @if(session('status'))
    <article class="contrast" style="padding:.6rem 1rem;">{{ session('status') }}</article>
  @endif
  @if(session('error'))
    <article class="contrast" style="padding:.6rem 1rem; border-left:4px solid #ef4444; background:#fff5f5;">
      {{ session('error') }}
    </article>
  @endif

  @yield('content')
</main>

<script src="/js/keypad.js"></script>
</body>
</html>
