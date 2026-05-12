if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {
            // Intentionally ignore service worker registration failures.
        });
    });
}

document.addEventListener('alpine:init', () => {
    Alpine.data('cookingTimer', (config) => ({
        seconds: 0,
        interval: null,

        init() {
            this.seconds = Math.floor(Date.now() / 1000) - config.startedAt;
            this.interval = setInterval(() => {
                this.seconds++;
            }, 1000);
        },

        stop() {
            clearInterval(this.interval);
            this.interval = null;
        },

        get formatted() {
            const total = Math.max(0, Math.floor(this.seconds));
            const h = Math.floor(total / 3600);
            const m = Math.floor((total % 3600) / 60);
            const s = total % 60;
            return [h, m, s].map(v => String(v).padStart(2, '0')).join(':');
        }
    }));
});
