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
                // Focus first input for quick entry
                const first = formWrap.querySelector('input, select, textarea');
                if (first) first.focus();
            }
        });
    });
})();
