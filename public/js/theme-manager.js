/**
 * Theme Manager System
 * يدير الثيم الليلي والنهاري بناءً على LocalStorage
 * بدون الحاجة لأزرار تبديل
 */

class ThemeManager {
    constructor() {
        this.STORAGE_KEY = 'iglal-platform-theme';
        this.PRIMARY_STORAGE_KEY = 'app-theme';
        this.LEGACY_STORAGE_KEY = 'theme';
        this.DARK_MODE_CLASS = 'dark-mode';
        this.LIGHT_MODE = 'light';
        this.DARK_MODE = 'dark';
        
        // تهيئة الثيم عند تحميل الصفحة
        this.initTheme();
    }

    /**
     * الحصول على الثيم المحفوظ من LocalStorage
     */
    getSavedTheme() {
        const saved = localStorage.getItem(this.PRIMARY_STORAGE_KEY) ||
            localStorage.getItem(this.LEGACY_STORAGE_KEY) ||
            localStorage.getItem(this.STORAGE_KEY) ||
            sessionStorage.getItem(this.PRIMARY_STORAGE_KEY) ||
            sessionStorage.getItem(this.LEGACY_STORAGE_KEY);
        if (saved) {
            return saved;
        }
        
        // إذا لم يكن هناك ثيم محفوظ، استخدم تفضيلات النظام
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            return this.DARK_MODE;
        }
        
        return this.LIGHT_MODE;
    }

    /**
     * تهيئة الثيم عند تحميل الصفحة
     */
    initTheme() {
        const theme = this.getSavedTheme();
        this.applyTheme(theme);
    }

    /**
     * تطبيق الثيم على الصفحة
     */
    applyTheme(theme) {
        const isDarkMode = theme === this.DARK_MODE;
        document.documentElement.setAttribute('data-theme', theme);
        document.body.setAttribute('data-theme', theme);
        
        if (isDarkMode) {
            document.documentElement.classList.add(this.DARK_MODE_CLASS);
            document.body.classList.add(this.DARK_MODE_CLASS);
        } else {
            document.documentElement.classList.remove(this.DARK_MODE_CLASS);
            document.body.classList.remove(this.DARK_MODE_CLASS);
        }
        
        // تحديث localStorage
        localStorage.setItem(this.STORAGE_KEY, theme);
        localStorage.setItem(this.PRIMARY_STORAGE_KEY, theme);
        localStorage.setItem(this.LEGACY_STORAGE_KEY, theme);
        sessionStorage.setItem(this.PRIMARY_STORAGE_KEY, theme);
        sessionStorage.setItem(this.LEGACY_STORAGE_KEY, theme);
    }

    /**
     * تبديل الثيم (يمكن استخدامه من صفحات أخرى)
     */
    toggleTheme() {
        const current = this.getSavedTheme();
        const newTheme = current === this.DARK_MODE ? this.LIGHT_MODE : this.DARK_MODE;
        this.applyTheme(newTheme);
    }

    /**
     * الحصول على الثيم الحالي
     */
    getCurrentTheme() {
        return this.getSavedTheme();
    }

    /**
     * تعيين ثيم محدد
     */
    setTheme(theme) {
        if (theme === this.DARK_MODE || theme === this.LIGHT_MODE) {
            this.applyTheme(theme);
        }
    }

    /**
     * إعادة تحميل الثيم (مفيد بعد تحديث الصفحة)
     */
    refresh() {
        this.initTheme();
    }
}

// تهيئة مدير الثيم عند تحميل الـ DOM
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.themeManager = new ThemeManager();
    });
} else {
    window.themeManager = new ThemeManager();
}
