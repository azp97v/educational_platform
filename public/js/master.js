/**
 * ═══════════════════════════════════════════════════════════════════════════
 * MASTER.JS - نظام المنطق الموحد لمنصة إجلال التعليمية
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * نظام شامل يتضمن:
 * ✓ إدارة المواضيع (Dark/Light Mode)
 * ✓ دوال مساعدة عامة
 * ✓ إدارة التنبيهات والرسائل
 * ✓ معالجة النماذج
 * ✓ الرسوم المتحركة والتحولات
 * ✓ معالجة الأحداث المشتركة
 * ✓ أدوات التصحيح والسجلات
 *
 * استخدام: أضف هذا الملف في نهاية </body>
 * <script src="{{ asset('js/master.js') }}"></script>
 */

// ─────────────────────────────────────────────────────────────────────────
// 🎯 1. GLOBAL APP NAMESPACE
// ─────────────────────────────────────────────────────────────────────────

const AppSystem = {
    version: '1.0.0',
    debug: true,
    theme: localStorage.getItem('app-theme') || localStorage.getItem('theme') || 'light',

    // 📋 Initialization
    init() {
        console.log('🚀 تهيئة نظام إجلال الموحد...');
        this.initTheme();
        this.initDOMReady();
        this.attachGlobalListeners();
        console.log('✅ تم التهيئة بنجاح!');
    },

    // 🔍 Logger Function
    log(message, type = 'info') {
        if (!this.debug) return;
        const timestamp = new Date().toLocaleTimeString('ar-SA');
        const prefix = {
            'info': '📌',
            'success': '✅',
            'warning': '⚠️',
            'error': '❌',
            'debug': '🐛'
        }[type] || '📌';
        console.log(`${prefix} [${timestamp}] ${message}`);
    }
};

// ─────────────────────────────────────────────────────────────────────────
// 🌙 2. THEME MANAGEMENT
// ─────────────────────────────────────────────────────────────────────────

const ThemeManager = {
    getSavedTheme() {
        return localStorage.getItem('app-theme') ||
               localStorage.getItem('theme') ||
               sessionStorage.getItem('app-theme') ||
               sessionStorage.getItem('theme') ||
               'light';
    },

    persistTheme(theme) {
        localStorage.setItem('app-theme', theme);
        localStorage.setItem('theme', theme);
        sessionStorage.setItem('app-theme', theme);
        sessionStorage.setItem('theme', theme);
    },

    applyThemeToDOM(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        if (document.body) {
            document.body.setAttribute('data-theme', theme);
            document.body.classList.toggle('dark-mode', theme === 'dark');
        }
    },

    initTheme() {
        const savedTheme = this.getSavedTheme();
        this.applyThemeToDOM(savedTheme);
        this.persistTheme(savedTheme);
        AppSystem.theme = savedTheme;
        AppSystem.log(`Theme loaded: ${savedTheme}`);
    },

    toggle() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        this.applyThemeToDOM(newTheme);
        this.persistTheme(newTheme);
        AppSystem.theme = newTheme;
        AppSystem.log(`Theme switched to: ${newTheme}`);
        document.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: newTheme } }));
    },

    setTheme(theme) {
        if (!['light', 'dark'].includes(theme)) {
            AppSystem.log(`Invalid theme: ${theme}`, 'error');
            return;
        }
        this.applyThemeToDOM(theme);
        this.persistTheme(theme);
        AppSystem.theme = theme;
        AppSystem.log(`Theme set to: ${theme}`);
    },

    getCurrentTheme() {
        return document.documentElement.getAttribute('data-theme') || 'light';
    }
};

// ─────────────────────────────────────────────────────────────────────────
// 🔔 3. NOTIFICATION SYSTEM
// ─────────────────────────────────────────────────────────────────────────

const NotificationSystem = {
    container: null,

    /**
     * Initialize notification container
     */
    init() {
        if (document.getElementById('notification-container')) return;
        this.container = document.createElement('div');
        this.container.id = 'notification-container';
        this.container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 400px;
        `;
        document.body.appendChild(this.container);
        AppSystem.log('🔔 تم تهيئة نظام التنبيهات');
    },

    /**
     * Show notification
     */
    show(message, type = 'info', duration = 4000) {
        if (!this.container) this.init();

        const icon = {
            'success': '✅',
            'error': '❌',
            'warning': '⚠️',
            'info': 'ℹ️'
        }[type] || 'ℹ️';

        const notification = document.createElement('div');
        notification.className = `alert alert-${type}-custom`;
        notification.style.cssText = `
            padding: 12px 16px;
            background: var(--bg-secondary);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideInRight 0.3s ease-out;
            font-size: 13px;
        `;

        const colors = {
            'success': '#229966',
            'error': '#D64545',
            'warning': '#F2B705',
            'info': '#997722'
        };

        notification.innerHTML = `
            <span style="color: ${colors[type] || '#997722'}; font-size: 16px;">${icon}</span>
            <span>${message}</span>
            <button style="
                background: none;
                border: none;
                cursor: pointer;
                color: var(--text-tertiary);
                font-size: 16px;
                padding: 0;
                margin-right: -5px;
            ">×</button>
        `;

        const closeBtn = notification.querySelector('button');
        const remove = () => {
            notification.style.animation = 'slideInLeft 0.3s ease-in';
            setTimeout(() => notification.remove(), 300);
        };

        closeBtn.addEventListener('click', remove);

        this.container.appendChild(notification);

        if (duration > 0) {
            setTimeout(remove, duration);
        }

        AppSystem.log(`${icon} ${message}`, type);
    },

    success(message, duration = 3000) { this.show(message, 'success', duration); },
    error(message, duration = 5000) { this.show(message, 'error', duration); },
    warning(message, duration = 4000) { this.show(message, 'warning', duration); },
    info(message, duration = 3000) { this.show(message, 'info', duration); }
};

// ─────────────────────────────────────────────────────────────────────────
// 📝 4. FORM UTILITIES
// ─────────────────────────────────────────────────────────────────────────

const FormUtils = {
    /**
     * Validate required fields
     */
    validateRequired(form) {
        const inputs = form.querySelectorAll('[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!input.value.trim()) {
                this.showFieldError(input, 'هذا الحقل مطلوب');
                isValid = false;
            } else {
                this.clearFieldError(input);
            }
        });

        return isValid;
    },

    /**
     * Validate email field
     */
    validateEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    },

    /**
     * Show field error
     */
    showFieldError(input, message) {
        input.classList.add('error-field');
        let errorEl = input.nextElementSibling;

        if (!errorEl || !errorEl.classList.contains('form-error')) {
            errorEl = document.createElement('div');
            errorEl.className = 'form-error';
            input.parentNode.insertBefore(errorEl, input.nextSibling);
        }

        errorEl.textContent = message;
    },

    /**
     * Clear field error
     */
    clearFieldError(input) {
        input.classList.remove('error-field');
        const errorEl = input.nextElementSibling;
        if (errorEl && errorEl.classList.contains('form-error')) {
            errorEl.remove();
        }
    },

    /**
     * Get form data as object
     */
    getFormData(form) {
        const formData = new FormData(form);
        return Object.fromEntries(formData.entries());
    },

    /**
     * Set form data from object
     */
    setFormData(form, data) {
        Object.keys(data).forEach(key => {
            const input = form.querySelector(`[name="${key}"]`);
            if (input) {
                if (input.type === 'checkbox' || input.type === 'radio') {
                    input.checked = data[key];
                } else {
                    input.value = data[key];
                }
            }
        });
    }
};

// ─────────────────────────────────────────────────────────────────────────
// 🎬 5. ANIMATION UTILITIES
// ─────────────────────────────────────────────────────────────────────────

const AnimationUtils = {
    /**
     * Fade in element
     */
    fadeIn(element, duration = 300) {
        element.style.opacity = '0';
        element.style.transition = `opacity ${duration}ms ease-in`;
        setTimeout(() => element.style.opacity = '1', 10);
    },

    /**
     * Fade out element
     */
    fadeOut(element, duration = 300) {
        element.style.transition = `opacity ${duration}ms ease-out`;
        element.style.opacity = '0';
        return new Promise(resolve => {
            setTimeout(resolve, duration);
        });
    },

    /**
     * Slide in from right
     */
    slideInRight(element, duration = 400) {
        element.classList.add('animate-slideInRight');
    },

    /**
     * Slide out to right
     */
    slideOutRight(element, duration = 400) {
        return new Promise(resolve => {
            element.style.animation = `slideInRight ${duration}ms ease-out reverse`;
            setTimeout(resolve, duration);
        });
    },

    /**
     * Scale animation
     */
    scale(element, scale = 1.05, duration = 300) {
        element.style.transition = `transform ${duration}ms ease-out`;
        element.style.transform = `scale(${scale})`;
    },

    /**
     * Shake animation
     */
    shake(element) {
        element.style.animation = 'none';
        setTimeout(() => {
            element.style.animation = 'shake 0.4s ease-in-out';
        }, 10);
    }
};

// CSS for shake animation
const styleSheet = document.createElement('style');
styleSheet.textContent = `
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-10px); }
        75% { transform: translateX(10px); }
    }
`;
document.head.appendChild(styleSheet);

// ─────────────────────────────────────────────────────────────────────────
// 🔗 6. DOM UTILITIES
// ─────────────────────────────────────────────────────────────────────────

const DOMUtils = {
    /**
     * Get element or create error
     */
    getElement(selector) {
        const element = document.querySelector(selector);
        if (!element) {
            AppSystem.log(`❌ العنصر غير موجود: ${selector}`, 'error');
        }
        return element;
    },

    /**
     * Get all elements
     */
    getElements(selector) {
        return document.querySelectorAll(selector);
    },

    /**
     * Add event listener with error handling
     */
    addEventListener(selector, event, callback) {
        const element = this.getElement(selector);
        if (element) {
            element.addEventListener(event, callback);
        }
    },

    /**
     * Add multiple event listeners
     */
    addEventListeners(selector, events, callback) {
        const element = this.getElement(selector);
        if (element) {
            events.forEach(event => {
                element.addEventListener(event, callback);
            });
        }
    },

    /**
     * Remove class from all elements
     */
    removeClassAll(selector, className) {
        this.getElements(selector).forEach(el => {
            el.classList.remove(className);
        });
    },

    /**
     * Scroll to element
     */
    scrollToElement(selector, offset = 0) {
        const element = this.getElement(selector);
        if (element) {
            const top = element.offsetTop - offset;
            window.scrollTo({ top, behavior: 'smooth' });
        }
    },

    /**
     * Check if element is visible in viewport
     */
    isElementVisible(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= window.innerHeight &&
            rect.right <= window.innerWidth
        );
    }
};

// ─────────────────────────────────────────────────────────────────────────
// 📊 7. DATA UTILITIES
// ─────────────────────────────────────────────────────────────────────────

const DataUtils = {
    /**
     * Local storage with auto-serialize
     */
    setLocal(key, value) {
        try {
            localStorage.setItem(key, JSON.stringify(value));
            AppSystem.log(`💾 تم حفظ: ${key}`);
        } catch (error) {
            AppSystem.log(`❌ خطأ في الحفظ: ${error.message}`, 'error');
        }
    },

    /**
     * Get from local storage
     */
    getLocal(key, defaultValue = null) {
        try {
            const item = localStorage.getItem(key);
            return item ? JSON.parse(item) : defaultValue;
        } catch (error) {
            AppSystem.log(`❌ خطأ في القراءة: ${error.message}`, 'error');
            return defaultValue;
        }
    },

    /**
     * Remove from local storage
     */
    removeLocal(key) {
        localStorage.removeItem(key);
        AppSystem.log(`🗑️ تم حذف: ${key}`);
    },

    /**
     * Clear all local storage
     */
    clearLocal() {
        localStorage.clear();
        AppSystem.log('🗑️ تم مسح جميع البيانات المحلية');
    }
};

// ─────────────────────────────────────────────────────────────────────────
// 🌐 8. API UTILITIES
// ─────────────────────────────────────────────────────────────────────────

const APIUtils = {
    /**
     * Make API request
     */
    async request(url, options = {}) {
        try {
            AppSystem.log(`🌐 طلب API: ${options.method || 'GET'} ${url}`);
            const response = await fetch(url, {
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    ...options.headers
                },
                ...options
            });

            if (!response.ok) {
                throw new Error(`HTTP Error: ${response.status}`);
            }

            const data = await response.json();
            AppSystem.log(`✅ استجابة من: ${url}`, 'success');
            return { ok: true, data };
        } catch (error) {
            AppSystem.log(`❌ خطأ في الطلب: ${error.message}`, 'error');
            return { ok: false, error: error.message };
        }
    },

    /**
     * GET request
     */
    get(url, options = {}) {
        return this.request(url, { ...options, method: 'GET' });
    },

    /**
     * POST request
     */
    post(url, data, options = {}) {
        return this.request(url, {
            ...options,
            method: 'POST',
            body: JSON.stringify(data)
        });
    },

    /**
     * PUT request
     */
    put(url, data, options = {}) {
        return this.request(url, {
            ...options,
            method: 'PUT',
            body: JSON.stringify(data)
        });
    },

    /**
     * DELETE request
     */
    delete(url, options = {}) {
        return this.request(url, { ...options, method: 'DELETE' });
    }
};

// ─────────────────────────────────────────────────────────────────────────
// ⏱️ 9. UTILITY FUNCTIONS
// ─────────────────────────────────────────────────────────────────────────

const Utils = {
    /**
     * Debounce function
     */
    debounce(func, wait = 300) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    /**
     * Throttle function
     */
    throttle(func, limit = 300) {
        let inThrottle;
        return function(...args) {
            if (!inThrottle) {
                func(...args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    },

    /**
     * Format date in Arabic
     */
    formatDate(date, format = 'dd/mm/yyyy') {
        const d = new Date(date);
        const day = String(d.getDate()).padStart(2, '0');
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const year = d.getFullYear();

        return format
            .replace('dd', day)
            .replace('mm', month)
            .replace('yyyy', year);
    },

    /**
     * Format currency
     */
    formatCurrency(amount, currency = 'SAR') {
        return new Intl.NumberFormat('ar-SA', {
            style: 'currency',
            currency: currency
        }).format(amount);
    },

    /**
     * Copy to clipboard
     */
    copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            NotificationSystem.success('تم النسخ بنجاح ✨');
        }).catch(() => {
            NotificationSystem.error('فشل النسخ');
        });
    },

    /**
     * Generate unique ID
     */
    generateId() {
        return '_' + Math.random().toString(36).substr(2, 9);
    },

    /**
     * Check if device is mobile
     */
    isMobile() {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    },

    /**
     * Sleep (wait) for duration
     */
    sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
};

// ─────────────────────────────────────────────────────────────────────────
// 🚀 10. INITIALIZATION
// ─────────────────────────────────────────────────────────────────────────

AppSystem.initDOMReady = function() {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', this.setupApp);
    } else {
        this.setupApp();
    }
};

AppSystem.attachGlobalListeners = function() {
    // Close notifications on click
    document.addEventListener('click', (e) => {
        if (e.target.closest('.notification-close')) {
            e.target.closest('.alert-custom').remove();
        }
    });

    // Handle page errors
    window.addEventListener('error', (event) => {
        AppSystem.log(`⚠️ خطأ غير متوقع: ${event.message}`, 'error');
    });

    // Track page visibility
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            AppSystem.log('👀 المستخدم غير نشط');
        } else {
            AppSystem.log('👀 المستخدم نشط');
        }
    });
};

AppSystem.setupApp = function() {
    ThemeManager.initTheme();
    NotificationSystem.init();
    AppSystem.log('✅ تم تجهيز التطبيق بنجاح!', 'success');
};

// Initialize when script loads
AppSystem.init();

// Export for use in other scripts
window.AppSystem = AppSystem;
window.ThemeManager = ThemeManager;
window.NotificationSystem = NotificationSystem;
window.FormUtils = FormUtils;
window.AnimationUtils = AnimationUtils;
window.DOMUtils = DOMUtils;
window.DataUtils = DataUtils;
window.APIUtils = APIUtils;
window.Utils = Utils;

/**
 * ✨ نهاية ملف master.js
 * استخدم هذا الملف كمرجع موحد لجميع المنطق المشترك في المشروع
 *
 * أمثلة الاستخدام:
 *
 * // تبديل المظهر
 * ThemeManager.toggle();
 *
 * // عرض إشعار
 * NotificationSystem.success('تم بنجاح!');
 *
 * // التحقق من النموذج
 * FormUtils.validateRequired(form);
 *
 * // طلب API
 * const result = await APIUtils.post('/api/endpoint', { data });
 */




