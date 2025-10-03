@extends('layouts.app')

@section('content')
<article style="text-align:center;">
  <h2>Scan to Pay</h2>
  <p class="muted">Amount: RM {{ number_format((float)$amount, 2) }}</p>

  <div id="qrBox" style="margin:1rem 0;">
    <div class="muted">Generating QR…</div>
  </div>

  <div class="muted" id="statusLine">Status: <strong id="status">PENDING</strong></div>
  <div class="muted" id="countdown"></div>

  {{-- Debug / demo controls (safe to remove later) --}}
  <div id="debugControls" style="margin-top:1rem; display:none; gap:.5rem; justify-content:center;">
    <button id="markPaidBtn" class="secondary outline" type="button">Mark as Paid (Demo)</button>
    <button id="regenBtn" class="secondary outline" type="button">Regenerate QR</button>
  </div>

  <footer style="margin-top:1.5rem;">
    <a href="{{ route('calculator') }}" role="button" class="secondary">Cancel</a>
  </footer>
</article>

<script>
(function () {
  const amount = @json($amount);
  const CSRF   = '{{ csrf_token() }}';

  const qrBox       = document.getElementById('qrBox');
  const statusEl    = document.getElementById('status');
  const countdownEl = document.getElementById('countdown');
  const debugWrap   = document.getElementById('debugControls');
  const markPaidBtn = document.getElementById('markPaidBtn');
  const regenBtn    = document.getElementById('regenBtn');

  let intent = null;
  let poll = null;
  let timer = null;

  init();

  function init() {
    // show debug controls for mock mode (always shown in this UI-only version)
    debugWrap.style.display = 'flex';
    createIntent();
    bindDebug();
  }

  function bindDebug() {
    markPaidBtn.addEventListener('click', async () => {
      if (!intent) return;
      markPaidBtn.disabled = true;
      try {
        await fetch(`/api/payment-intents/${intent.id}/mark-paid`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': CSRF
          }
        });
        // Next poll tick will pick up PAID; or force refresh now:
        await refreshStatus();
      } catch (e) {
        console.error(e);
      } finally {
        markPaidBtn.disabled = false;
      }
    });

    regenBtn.addEventListener('click', async () => {
      stopTimers();
      clearQR();
      statusEl.textContent = 'PENDING';
      countdownEl.textContent = '';
      await createIntent();
    });
  }

  function stopTimers() {
    if (poll)  { clearInterval(poll);  poll = null; }
    if (timer) { clearInterval(timer); timer = null; }
  }

  function clearQR() {
    qrBox.innerHTML = '<div class="muted">Generating QR…</div>';
  }

  async function createIntent() {
    try {
      const resp = await fetch('/api/payment-intents', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': CSRF
        },
        body: JSON.stringify({ amount, currency: 'MYR' })
      });

      if (!resp.ok) throw new Error('Failed to create payment intent');
      intent = await resp.json();

      // Show QR
      const img = new Image();
      img.src = intent.qr_url;
      img.alt = 'QR Code';
      img.width = 250; img.height = 250;
      qrBox.innerHTML = '';
      qrBox.appendChild(img);

      // Expiry countdown
      const expiresAt = new Date(intent.expires_at).getTime();
      timer = setInterval(() => {
        const left = Math.max(0, Math.floor((expiresAt - Date.now())/1000));
        const m = String(Math.floor(left/60)).padStart(2,'0');
        const s = String(left%60).padStart(2,'0');
        countdownEl.textContent = left ? `Expires in ${m}:${s}` : 'Expired';
        if (left === 0) {
          clearInterval(timer);
          timer = null;
        }
      }, 1000);

      // Poll status every 2s
      poll = setInterval(refreshStatus, 2000);
    } catch (err) {
      console.error(err);
      qrBox.innerHTML = '<div style="color:#b91c1c;">Error creating payment intent</div>';
    }
  }

  async function refreshStatus() {
    if (!intent) return;
    try {
      const res = await fetch(`/api/payment-intents/${intent.id}`);
      if (!res.ok) return;
      const data = await res.json();
      statusEl.textContent = data.status;

      if (data.status === 'PAID') {
        stopTimers();
        window.location.href = `/paid?amount=${encodeURIComponent(intent.amount)}`;
      }
      if (data.status === 'EXPIRED' || data.status === 'FAILED') {
        stopTimers();
        countdownEl.textContent = 'Not paid (expired/failed)';
      }
    } catch (e) {
      console.error(e);
    }
  }
})();
</script>
@endsection
