(function () {
    const root = document.documentElement;
    const savedTheme = localStorage.getItem('mwira-theme');
    const preferredTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    root.dataset.theme = savedTheme || preferredTheme;

    function updateButton(button) {
        const dark = root.dataset.theme === 'dark';
        button.innerHTML = `<i class="bi bi-${dark ? 'sun' : 'moon-stars'}"></i>`;
        button.title = dark ? 'Passer au mode clair' : 'Passer au mode sombre';
        button.setAttribute('aria-label', button.title);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const topbar = document.querySelector('.dashboard-topbar') || document.querySelector('.navbar');
        if (!topbar || topbar.querySelector('.theme-toggle')) return;
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'theme-toggle';
        updateButton(button);
        button.addEventListener('click', function () {
            root.dataset.theme = root.dataset.theme === 'dark' ? 'light' : 'dark';
            localStorage.setItem('mwira-theme', root.dataset.theme);
            updateButton(button);
        });
        topbar.appendChild(button);
    });
})();
