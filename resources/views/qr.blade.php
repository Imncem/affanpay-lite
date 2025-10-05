@extends('layouts.app')

@section('content')
<article style="text-align:center;">
  <h2>Scan to Pay</h2>
  <p class="muted">RM {{ number_format((float)$amount, 2) }}</p>

  <div id="qrBox" style="margin:1rem 0;">
    <div class="muted">Generating QR…</div>
  </div>

  <div class="muted" id="countdown"></div>

  <footer style="margin-top:1.5rem;">
    <a href="{{ route('calculator') }}" role="button" class="secondary">Cancel</a>
  </footer>
</article>

<script>
(function () {
  const amount = @json($amount);
  const CSRF   = '{{ csrf_token() }}';
  const qrBox = document.getElementById('qrBox');
  const countdownEl = document.getElementById('countdown');

  let intent = null;
  let poll = null;
  let timer = null;

  init();

  async function init() {
    await createIntent();              // get qr_url, id, expires_at
    poll = setInterval(refreshStatus, 2000); // start polling
  }

  function stopTimers() {
    if (poll)  { clearInterval(poll);  poll = null; }
    if (timer) { clearInterval(timer); timer = null; }
  }

  async function createIntent() {
    try {
      const resp = await fetch('/api/payment-intents', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ amount, currency: 'MYR' })
      });
      if (!resp.ok) throw new Error('Failed to create payment intent');
      intent = await resp.json();

      // show QR
      const img = new Image();
      img.src = intent.qr_url;
      img.alt = 'QR Code';
      img.width = 250; img.height = 250;
      qrBox.innerHTML = '';
      qrBox.appendChild(img);

      // countdown
      const expiresAt = new Date(intent.expires_at).getTime();
      timer = setInterval(() => {
        const left = Math.max(0, Math.floor((expiresAt - Date.now())/1000));
        const m = String(Math.floor(left/60)).padStart(2,'0');
        const s = String(left%60).padStart(2,'0');
        countdownEl.textContent = left ? `Expires in ${m}:${s}` : 'Expired';

        if (left === 0) {
          stopTimers();
          // go to failure page
          window.location.href = `{{ route('qr.expired') }}?amount=${encodeURIComponent(intent.amount)}`;
        }
      }, 1000);
    } catch (e) {
      console.error(e);
      qrBox.innerHTML = '<div style="color:#b91c1c;">Error creating payment</div>';
    }
  }

  async function refreshStatus() {
    if (!intent) return;
    try {
      const res = await fetch(`/api/payment-intents/${intent.id}`);
      if (!res.ok) return;
      const data = await res.json();

      if (data.status === 'PAID') {
        stopTimers();
        window.location.href = `/paid?amount=${encodeURIComponent(intent.amount)}`;
      }
      if (data.status === 'EXPIRED' || data.status === 'FAILED') {
        stopTimers();
        window.location.href = `{{ route('qr.expired') }}?amount=${encodeURIComponent(intent.amount)}`;
      }
    } catch (e) {
      console.error(e);
    }
  }
})();
</script>
@endsection
