(function () {
  const display = document.getElementById('amountDisplay');
  const raw = document.getElementById('amountRaw');
  const hidden = document.getElementById('amountHidden');
  const payBtn = document.getElementById('payBtn');
  if (!display || !raw || !hidden) return;

  // Store value as integer cents (e.g., 1234 => RM 12.34)
  let cents = 0;
  const MAX_DIGITS = 9; // up to 9999999.99 (adjust if you need more)

  function setCents(nextCents) {
    // clamp to non-negative and within range
    nextCents = Math.max(0, Math.min(nextCents, 10 ** MAX_DIGITS - 1));
    cents = nextCents;

    const amount = cents / 100;
    display.textContent = formatMYR(amount);
    raw.textContent = amount.toFixed(2);     // show raw decimal (for debugging)
    hidden.value = amount.toFixed(2);        // submit as "12.34"
    if (payBtn) payBtn.disabled = cents <= 0;
  }

  function pushDigit(d) {
    // prevent overflow of digits
    const currentDigits = String(cents);
    if (currentDigits.length >= MAX_DIGITS) return;
    setCents(cents * 10 + d);
  }

  function backspace() {
    setCents(Math.floor(cents / 10));
  }

  function clearAll() {
    setCents(0);
  }

  function formatMYR(n) {
    try {
      return new Intl.NumberFormat('ms-MY', { style: 'currency', currency: 'MYR', minimumFractionDigits: 2 }).format(n || 0);
    } catch {
      return 'RM ' + (n || 0).toFixed(2);
    }
  }

  // Hook up keypad buttons
  document.querySelectorAll('[data-key]').forEach(btn => {
    btn.addEventListener('click', () => {
      const k = btn.getAttribute('data-key');

      if (k === 'CLR') return clearAll();
      if (k === 'DEL') return backspace();

      // Ignore decimal dot in this mode (not needed)
      if (k === '.') return;

      // digits 0-9
      if (/^\d$/.test(k)) return pushDigit(Number(k));
    });
  });

  // initialize
  setCents(0);
})();
