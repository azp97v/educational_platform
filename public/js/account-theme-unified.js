(function () {
    var KEY = 'app-theme';
    var LEGACY_KEY = 'theme';
    var THEME_ATTR = 'data-theme';
    var DARK_CLASS = 'dark-mode';
    var TOGGLE_SELECTOR = [
        '#darkBtn',
        '#themeToggle',
        '#messagingThemeBtn',
        '.nav-toggle-theme',
        '[data-theme-toggle]',
        '[onclick*="toggleDark"]',
        '[onclick*="toggleTheme"]',
        '[onclick*="toggleDarkMode"]'
    ].join(',');

    var toggleLock = false;
    var LEGACY_THEME_UI_SELECTORS = [
        '.global-theme-toggle',
        '#globalThemeToggle',
        '.floating-theme-toggle',
        '#floatingThemeToggle',
        '.theme-floating-toggle',
        '.legacy-theme-toggle'
    ];

    function safeGet(storage, key) {
        try { return storage.getItem(key); } catch (_) { return null; }
    }

    function safeSet(storage, key, value) {
        try { storage.setItem(key, value); } catch (_) {}
    }

    function normalizeTheme(theme) {
        return theme === 'dark' ? 'dark' : 'light';
    }

    function getStoredTheme() {
        return normalizeTheme(
            safeGet(localStorage, KEY) ||
            safeGet(localStorage, LEGACY_KEY) ||
            safeGet(sessionStorage, KEY) ||
            safeGet(sessionStorage, LEGACY_KEY) ||
            document.documentElement.getAttribute(THEME_ATTR) ||
            'light'
        );
    }

    function persistTheme(theme) {
        safeSet(localStorage, KEY, theme);
        safeSet(localStorage, LEGACY_KEY, theme);
        safeSet(sessionStorage, KEY, theme);
        safeSet(sessionStorage, LEGACY_KEY, theme);
    }

    function updateToggleIcons(theme) {
        var dark = theme === 'dark';
        var selectors = ['#darkIcon', '#theme-icon', '#themeToggle i', '.theme-icon', '#messagingThemeBtn i'];
        var iconClass = dark ? 'ri-sun-line' : 'ri-moon-line';

        selectors.forEach(function (selector) {
            document.querySelectorAll(selector).forEach(function (icon) {
                icon.classList.remove('ri-sun-line', 'ri-moon-line');
                icon.classList.add(iconClass);
            });
        });

        // Legacy pages sometimes render toggle buttons without a stable icon id.
        document.querySelectorAll(TOGGLE_SELECTOR).forEach(function (btn) {
            if (!btn) return;
            var icon = btn.querySelector('i');
            if (!icon && btn.tagName === 'I') icon = btn;
            if (!icon) return;
            icon.classList.remove('ri-sun-line', 'ri-moon-line');
            icon.classList.add(iconClass);
        });
    }

    function cleanupLegacyThemeUi() {
        LEGACY_THEME_UI_SELECTORS.forEach(function (selector) {
            document.querySelectorAll(selector).forEach(function (node) {
                if (node && node.parentNode) {
                    node.parentNode.removeChild(node);
                }
            });
        });
    }

    function setThemeOnRoots(theme) {
        document.documentElement.setAttribute(THEME_ATTR, theme);
        document.documentElement.classList.toggle(DARK_CLASS, theme === 'dark');

        if (document.body) {
            document.body.setAttribute(THEME_ATTR, theme);
            document.body.classList.toggle(DARK_CLASS, theme === 'dark');
        }
    }

    function applyTheme(theme, persist) {
        var nextTheme = normalizeTheme(theme);
        setThemeOnRoots(nextTheme);

        if (persist !== false) {
            persistTheme(nextTheme);
        }

        updateToggleIcons(nextTheme);
    }

    function toggleThemeUniversal() {
        if (toggleLock) return false;

        toggleLock = true;
        try {
            var current = getStoredTheme();
            var next = current === 'dark' ? 'light' : 'dark';
            applyTheme(next, true);
        } finally {
            setTimeout(function () {
                toggleLock = false;
            }, 120);
        }

        return false;
    }

    function interceptLegacyThemeButtons() {
        document.addEventListener('click', function (event) {
            var target = event.target && event.target.closest ? event.target.closest(TOGGLE_SELECTOR) : null;
            if (!target) return;

            event.preventDefault();
            event.stopPropagation();
            if (typeof event.stopImmediatePropagation === 'function') {
                event.stopImmediatePropagation();
            }

            toggleThemeUniversal();
        }, true);
    }

    function init() {
        applyTheme(getStoredTheme(), true);
        cleanupLegacyThemeUi();
        interceptLegacyThemeButtons();
        setTimeout(cleanupLegacyThemeUi, 250);
        setTimeout(cleanupLegacyThemeUi, 1000);

        window.addEventListener('storage', function (event) {
            if (event.key === KEY || event.key === LEGACY_KEY) {
                applyTheme(getStoredTheme(), false);
            }
        });
    }

    window.toggleThemeUniversal = toggleThemeUniversal;
    window.applyThemeUniversal = function (theme) {
        applyTheme(theme, true);
    };

    // Aliases for legacy inline handlers spread across old pages.
    window.toggleTheme = toggleThemeUniversal;
    window.toggleDark = toggleThemeUniversal;
    window.toggleDarkMode = toggleThemeUniversal;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
