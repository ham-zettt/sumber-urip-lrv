import Alpine from 'alpinejs';
import ApexCharts from 'apexcharts';

window.Alpine = Alpine;
window.ApexCharts = ApexCharts;

Alpine.store('theme', {
    mode: localStorage.getItem('sumber_urip_theme') || 'dark',

    init() {
        if (this.mode === 'light') {
            document.documentElement.classList.add('light');
        } else {
            document.documentElement.classList.remove('light');
        }
    },

    toggle() {
        this.mode = this.mode === 'light' ? 'dark' : 'light';
        localStorage.setItem('sumber_urip_theme', this.mode);
        if (this.mode === 'light') {
            document.documentElement.classList.add('light');
        } else {
            document.documentElement.classList.remove('light');
        }
        window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: this.mode } }));
    }
});

Alpine.start();