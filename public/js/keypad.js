(function () {
  const display = document.getElementById('amountDisplay');
  const raw = document.getElementById('amountRaw');
  const hidden = document.getElementById('amountHidden');
  const payBtn = document.getElementById('payBtn');
  if (!display || !raw || !hidden) return;

  let amount = '';

  function setAmount(next) {
    // allow only 2 decimals
    if (!/^\d*(?:\.\d{0,2})?$/.test(next)) return;
    amount = next;
    const num = Number(amount || 0);
    display.textContent = formatMYR(num);
    raw.textContent = amount || '0';
    hidden.value = amount || '';
    payBtn && (payBtn.disabled = !(num > 0));
  }

  function formatMYR(n) {
    try {
      return new Intl.NumberFormat('ms-MY', { style: 'currency', currency: 'MYR', minimumFractionDigits: 2 }).format(n || 0);
    } catch {
      return 'RM ' + (n || 0).toFixed(2);
    }
  }

  document.querySelectorAll('[data-key]').forEach(btn => {
    btn.addEventListener('click', () => {
      const k = btn.getAttribute('data-key');
      if (k === 'CLR') return setAmount('');
      if (k === 'DEL') return setAmount(amount.slice(0, -1));
      if (k === '.') {
        if (!amount) return setAmount('0.');
        if (amount.includes('.')) return;
        return setAmount(amount + '.');
      }
      // digits
      return setAmount(amount + k);
    });
  });

  // initialize
  setAmount('');
})();
