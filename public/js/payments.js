(function() {
  const accordion = document.getElementById('payment-accordion');
  const services = Array.from(accordion.querySelectorAll('.service'));

  function closeAll() {
    services.forEach(s => {
      s.querySelector('.form-wrap').style.display = 'none';
      s.querySelector('.chevron').classList.remove('open');
    });
  }

  services.forEach(service => {
    const header = service.querySelector('.service-header');
    const formWrap = service.querySelector('.form-wrap');
    const chevron = service.querySelector('.chevron');

    header.addEventListener('click', () => {
      const isOpen = formWrap.style.display === 'block';
      closeAll();
      if (!isOpen) {
        formWrap.style.display = 'block';
        chevron.classList.add('open');
        const first = formWrap.querySelector('input, select, textarea');
        if (first) first.focus();
      }
    });
  });

  // Modal helpers
  const modal = document.getElementById('pay-modal');
  const stepInit = document.getElementById('pay-modal-step-init');
  const stepWait = document.getElementById('pay-modal-step-wait');
  const stepSuccess = document.getElementById('pay-modal-step-success');
  const stepFailed = document.getElementById('pay-modal-step-failed');
  const btnClose = document.getElementById('pay-modal-close');

  function show(el) { el.style.display = 'block'; }
  function hide(el) { el.style.display = 'none'; }
  function showStep(which) {
    [stepInit, stepWait, stepSuccess, stepFailed].forEach(hide);
    if (which === 'init') show(stepInit);
    else if (which === 'wait') show(stepWait);
    else if (which === 'success') show(stepSuccess);
    else if (which === 'failed') show(stepFailed);
  }

  function openModal() { modal.style.display = 'block'; }
  function closeModal() { modal.style.display = 'none'; }

  btnClose && btnClose.addEventListener('click', () => {
    closeModal();
  });

  // Intercept all payment forms
  const forms = Array.from(document.querySelectorAll('#payment-accordion form'));
  forms.forEach(form => {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      openModal();
      hide(btnClose);
      showStep('init');

      const action = form.getAttribute('action');
      const formData = new FormData(form);
      const tokenInput = form.querySelector('input[name="_token"]');
      const csrf = tokenInput ? tokenInput.value : '';

      try {
        const resp = await fetch(action, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrf,
            'Accept': 'application/json'
          },
          body: formData
        });

        const data = await resp.json().catch(() => ({}));

        if (!resp.ok || data.response?.error) {
          showStep('failed');
          show(btnClose);
          return;
        }

        // Move to wait step, start polling for status changes
        showStep('wait');
        const paymentId = data.payment_id;
        if (!paymentId) {
          showStep('failed');
          show(btnClose);
          return;
        }

        const pollUrl = `/pay/status/${paymentId}`;
        const completeUrl = `/pay/complete/${paymentId}`;

        let attempts = 0;
        const maxAttempts = 60; // up to ~3 minutes at 3s interval
        const delay = (ms) => new Promise(r => setTimeout(r, ms));

        while (attempts < maxAttempts) {
          attempts++;
          try {
            const sresp = await fetch(pollUrl, { headers: { 'Accept': 'application/json' } });
            const sdata = await sresp.json();
            const status = (sdata.status || '').toLowerCase();
            if (status === 'success') {
              showStep('success');
              // Small delay for UX, then complete to post callback and redirect
              await delay(600);
              window.location.href = completeUrl;
              return;
            }
            if (status === 'failed') {
              showStep('failed');
              show(btnClose);
              return;
            }
          } catch (_) {
            // ignore transient errors
          }
          await delay(3000);
        }

        // Timeout
        showStep('failed');
        show(btnClose);

      } catch (err) {
        showStep('failed');
        show(btnClose);
      }
    });
  });
})();
