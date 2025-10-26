(function() {
  // Modal helpers
  const modal = document.getElementById('pay-modal');
  const stepInit = document.getElementById('pay-modal-step-init');
  const stepWait = document.getElementById('pay-modal-step-wait');
  const stepSuccess = document.getElementById('pay-modal-step-success');
  const stepFailed = document.getElementById('pay-modal-step-failed');
  const btnClose = document.getElementById('pay-modal-close');

  function show(el) { 
    if (el) el.style.display = 'flex'; 
  }
  
  function hide(el) { 
    if (el) el.style.display = 'none'; 
  }
  
  function showStep(which) {
    [stepInit, stepWait, stepSuccess, stepFailed].forEach(hide);
    if (which === 'init') show(stepInit);
    else if (which === 'wait') show(stepWait);
    else if (which === 'success') show(stepSuccess);
    else if (which === 'failed') show(stepFailed);
  }

  function openModal() { 
    if (modal) modal.style.display = 'flex'; 
  }
  
  function closeModal() { 
    if (modal) modal.style.display = 'none'; 
  }

  // Close modal button
  if (btnClose) {
    btnClose.addEventListener('click', () => {
      closeModal();
    });
  }

  // Intercept all payment forms in the payment methods
  const forms = Array.from(document.querySelectorAll('.payment-method form'));
  forms.forEach(form => {
    form.addEventListener('submit', async (e) => {
      e.preventDefault(); // CRITICAL: Prevent default form submission
      e.stopPropagation(); // Stop event bubbling

      // Open modal and show initiating step
      openModal();
      hide(btnClose);
      showStep('init');

      const action = form.getAttribute('action');
      const formData = new FormData(form);
      const tokenInput = form.querySelector('input[name="_token"]');
      const csrf = tokenInput ? tokenInput.value : '';

      try {
        // Initiate payment
        const resp = await fetch(action, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrf,
            'Accept': 'application/json'
          },
          body: formData
        });

        const data = await resp.json().catch(() => ({}));

        // Check for errors in response
        if (!resp.ok || data.error || data.response?.error) {
          showStep('failed');
          show(btnClose);
          return;
        }

        // Move to wait step - STK push sent
        showStep('wait');
        
        const paymentId = data.payment_id;
        if (!paymentId) {
          showStep('failed');
          show(btnClose);
          return;
        }

        // Poll for payment status
        const pollUrl = `/pay/status/${paymentId}`;
        const completeUrl = `/pay/complete/${paymentId}`;

        let attempts = 0;
        const maxAttempts = 60; // up to ~3 minutes at 3s interval
        const delay = (ms) => new Promise(r => setTimeout(r, ms));

        while (attempts < maxAttempts) {
          attempts++;
          
          try {
            const sresp = await fetch(pollUrl, { 
              headers: { 'Accept': 'application/json' } 
            });
            const sdata = await sresp.json();
            const status = (sdata.status || '').toLowerCase();
            
            if (status === 'success') {
              // Payment successful
              showStep('success');
              // Brief delay for user to see success message
              await delay(1500);
              // Redirect to complete endpoint
              window.location.href = completeUrl;
              return;
            }
            
            if (status === 'failed' || status === 'cancelled') {
              // Payment failed
              showStep('failed');
              show(btnClose);
              return;
            }
          } catch (err) {
            // Ignore transient network errors, continue polling
            console.log('Polling error:', err);
          }
          
          // Wait before next poll
          await delay(3000);
        }

        // Timeout - no response after max attempts
        showStep('failed');
        show(btnClose);

      } catch (err) {
        console.error('Payment error:', err);
        showStep('failed');
        show(btnClose);
      }
    });
  });

  // Make the function globally accessible for the "Complete Checkout" button
  window.triggerPaymentSubmit = function() {
    const activeMethod = document.querySelector('.payment-method.active');
    if (!activeMethod) {
      alert('Please select a payment method');
      return;
    }
    
    const form = activeMethod.querySelector('form');
    if (!form) {
      alert('Payment form not found');
      return;
    }
    
    // Check if payment method is disabled (coming soon)
    const methodName = activeMethod.querySelector('.method-name');
    const comingSoonBadge = methodName?.querySelector('.badge.soon');
    if (comingSoonBadge) {
      alert('This payment method is coming soon. Please select M-Pesa.');
      return;
    }
    
    // Validate form before submission
    if (form.checkValidity()) {
      // Create and dispatch a submit event that will be caught by our listener above
      const submitEvent = new Event('submit', { cancelable: true, bubbles: true });
      form.dispatchEvent(submitEvent);
    } else {
      form.reportValidity();
    }
  };
})();