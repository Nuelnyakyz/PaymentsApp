import './bootstrap';
import './payments.js';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Global UI store (shared across all components/pages)
Alpine.store('ui', {
  mobileSidebar: false,
});

Alpine.start();
