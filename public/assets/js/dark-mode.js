const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
const mainApp = document.getElementById('main-app');

// Change the icons inside the button based on previous settings
if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    themeToggleLightIcon.classList.remove('hidden');
    mainApp.classList.remove('dx-swatch-umd-fluent-scheme-light');
    mainApp.classList.add('dx-swatch-umd-fluent-scheme-dark');
} else {
    themeToggleDarkIcon.classList.remove('hidden');
}

const themeToggleBtn = document.getElementById('theme-toggle');

let event = new Event('dark-mode');

themeToggleBtn.addEventListener('click', function () {

    // toggle icons
    themeToggleDarkIcon.classList.toggle('hidden');
    themeToggleLightIcon.classList.toggle('hidden');

    // if set via local storage previously
    if (localStorage.getItem('color-theme')) {
        if (localStorage.getItem('color-theme') === 'light') {
            document.documentElement.classList.add('dark');
            localStorage.setItem('color-theme', 'dark');
            $("#main-app").addClass('dx-swatch-umd-fluent-scheme-dark').removeClass('dx-swatch-umd-fluent-scheme-light')
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('color-theme', 'light');
            localStorage.setItem('prefers-color-scheme', 'light');
            $("#main-app").addClass('dx-swatch-umd-fluent-scheme-light').removeClass('dx-swatch-umd-fluent-scheme-dark')
        }

        // if NOT set via local storage previously
    } else {
        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('color-theme', 'light');
            localStorage.setItem('prefers-color-scheme', 'light');
            $("#main-app").addClass('dx-swatch-umd-fluent-scheme-light').removeClass('dx-swatch-umd-fluent-scheme-dark')
        } else {
            document.documentElement.classList.add('dark');
            localStorage.setItem('color-theme', 'dark');
            $("#main-app").addClass('dx-swatch-umd-fluent-scheme-dark').removeClass('dx-swatch-umd-fluent-scheme-light')

        }
    }

    document.dispatchEvent(event);

});