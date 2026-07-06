<div class="search-dropdown" id="searchDropdown" style="display:none;">
    <div class="search-dropdown-list" id="searchResults"></div>
</div>

@once
<style>
.search-dropdown {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    right: 0;
    background: var(--card-bg, var(--theme-surface));
    border: 1px solid rgba(255,214,122,0.18);
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.28);
    backdrop-filter: blur(24px);
    z-index: 9999;
    max-height: 380px;
    overflow-y: auto;
    animation: searchFadeIn 0.2s ease;
}
@keyframes searchFadeIn {
    from { opacity: 0; transform: translateY(-6px); }
    to { opacity: 1; transform: translateY(0); }
}
.search-dropdown-list {
    padding: 6px;
}
.search-result-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    border-radius: 12px;
    cursor: pointer;
    text-decoration: none;
    color: var(--text-primary, #F4F4F7);
    transition: all 0.2s ease;
    font-family: 'Tajawal', sans-serif;
    font-size: 14px;
    font-weight: 600;
}
.search-result-item:hover {
    background: rgba(255,214,122,0.12);
    transform: translateX(-4px);
}
.search-result-item i {
    font-size: 18px;
    color: var(--gold, #C4963A);
    width: 24px;
    text-align: center;
    flex-shrink: 0;
}
.search-result-item .result-label {
    flex: 1;
}
.search-result-item .result-badge {
    font-size: 11px;
    padding: 2px 10px;
    border-radius: 20px;
    background: rgba(255,214,122,0.14);
    color: var(--gold, #C4963A);
    font-weight: 700;
}
.search-no-results {
    padding: 24px 16px;
    text-align: center;
    color: var(--text-muted, #8C92A2);
    font-size: 13px;
    font-weight: 600;
}
.search-dropdown::-webkit-scrollbar { width: 4px; }
.search-dropdown::-webkit-scrollbar-thumb { background: var(--gold, #C4963A); border-radius: 4px; }

html[data-theme="light"] .search-dropdown {
    background: #ffffff;
    border-color: #DFE5EC;
    box-shadow: 0 20px 60px rgba(34,43,61,0.12);
}
html[data-theme="light"] .search-result-item {
    color: #222B3D;
}
html[data-theme="light"] .search-result-item:hover {
    background: rgba(196,150,58,0.08);
}
html[data-theme="light"] .search-no-results {
    color: #8C92A2;
}
</style>
@endonce

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ADMIN_SEARCH_ITEMS = [
        { label: 'لوحة التحكم', icon: 'ri-dashboard-line', url: '/admin/users' },
        { label: 'المستخدمون', icon: 'ri-team-line', url: '/admin/users' },
        { label: 'إضافة مستخدم', icon: 'ri-user-add-line', url: '/admin/users/create' },
        { label: 'طلبات التسجيل', icon: 'ri-user-add-line', url: '/admin/enrollments' },
        { label: 'الإحصاءات', icon: 'ri-bar-chart-box-line', url: '/admin/analytics' },
        { label: 'المالية', icon: 'ri-money-dollar-circle-line', url: '/admin/finance' },
        { label: 'الصلاحيات', icon: 'ri-shield-line', url: '/admin/rbac' },
        { label: 'العمليات الحية', icon: 'ri-live-line', url: '/admin/liveops' },
        { label: 'سجل النشاط', icon: 'ri-history-line', url: '/admin/activity' },
        { label: 'الإعلانات', icon: 'ri-megaphone-line', url: '/admin/announcements' },
        { label: 'الإعدادات', icon: 'ri-settings-3-line', url: '/admin/settings' },
        { label: 'المراسلة', icon: 'ri-message-2-line', url: '/messaging' },
        { label: 'الملف الشخصي', icon: 'ri-user-settings-line', url: '/profile' },
    ];

    const TEACHER_SEARCH_ITEMS = [
        { label: 'لوحة التحكم', icon: 'ri-dashboard-line', url: '/teacher' },
        { label: 'المسارات', icon: 'ri-book-open-line', url: '/teacher/courses' },
        { label: 'إنشاء مسار', icon: 'ri-add-circle-line', url: '/teacher/courses/create' },
        { label: 'طلبات الالتحاق', icon: 'ri-user-add-line', url: '/teacher/enrollment-requests' },
        { label: 'الاختبارات', icon: 'ri-file-list-line', url: '/teacher/exams' },
        { label: 'إنشاء اختبار', icon: 'ri-add-circle-line', url: '/teacher/exams/new' },
        { label: 'الطلاب', icon: 'ri-team-line', url: '/teacher/students' },
        { label: 'الشهادات', icon: 'ri-award-line', url: '/teacher/certificates' },
        { label: 'الأسئلة والاستفسارات', icon: 'ri-question-answer-line', url: '/teacher/questions-manage' },
        { label: 'المراسلة', icon: 'ri-message-2-line', url: '/teacher/messaging' },
        { label: 'الإحصاءات', icon: 'ri-bar-chart-box-line', url: '/teacher/analytics' },
        { label: 'الملف الشخصي', icon: 'ri-user-settings-line', url: '/profile' },
    ];

    const STUDENT_SEARCH_ITEMS = [
        { label: 'لوحة التحكم', icon: 'ri-dashboard-line', url: '/student/dashboard' },
        { label: 'الأكاديمية والمسارات', icon: 'ri-book-open-line', url: '/student/academy' },
        { label: 'الاختبارات', icon: 'ri-file-list-line', url: '/student/exams' },
        { label: 'الإنجازات', icon: 'ri-award-line', url: '/student/achievements' },
        { label: 'الاستفسارات', icon: 'ri-question-answer-line', url: '/student/inquiries' },
        { label: 'الملف الشخصي', icon: 'ri-user-settings-line', url: '/profile' },
    ];

    const searchInputs = document.querySelectorAll('.search-wrap input, .search-wrapper input, .search-input');

    searchInputs.forEach(function(input) {
        let dropdown = input.closest('.search-wrap, .search-wrapper');
        if (!dropdown) dropdown = input.parentElement;
        if (!dropdown) return;

        if (getComputedStyle(dropdown).position === 'static') {
            dropdown.style.position = 'relative';
        }

        let existingDropdown = dropdown.querySelector('.search-dropdown');
        if (!existingDropdown) {
            const dd = document.createElement('div');
            dd.className = 'search-dropdown';
            dd.id = 'searchDropdown_' + Math.random().toString(36).substr(2, 9);
            dd.style.display = 'none';
            const list = document.createElement('div');
            list.className = 'search-dropdown-list';
            dd.appendChild(list);
            dropdown.appendChild(dd);
            existingDropdown = dd;
        }

        const listEl = existingDropdown.querySelector('.search-dropdown-list');
        const role = document.body.dataset.role || '';
        const items = role === 'admin' ? ADMIN_SEARCH_ITEMS
                    : role === 'student' ? STUDENT_SEARCH_ITEMS
                    : TEACHER_SEARCH_ITEMS;

        let hideTimeout = null;

        function filterResults(query) {
            if (!query || query.trim().length < 1) {
                existingDropdown.style.display = 'none';
                return;
            }
            const q = query.trim().toLowerCase();
            const filtered = items.filter(function(item) {
                return item.label.toLowerCase().includes(q);
            });

            if (filtered.length === 0) {
                listEl.innerHTML = '<div class="search-no-results">— لا توجد نتائج —</div>';
            } else {
                listEl.innerHTML = filtered.map(function(item) {
                    return '<a class="search-result-item" href="' + item.url + '">\
                        <i class="' + item.icon + '"></i>\
                        <span class="result-label">' + item.label + '</span>\
                    </a>';
                }).join('');
            }
            existingDropdown.style.display = 'block';
        }

        input.addEventListener('input', function(e) {
            if (hideTimeout) clearTimeout(hideTimeout);
            filterResults(e.target.value);
        });

        input.addEventListener('focus', function(e) {
            if (hideTimeout) clearTimeout(hideTimeout);
            if (e.target.value.trim().length >= 1) {
                filterResults(e.target.value);
            }
        });

        input.addEventListener('blur', function() {
            hideTimeout = setTimeout(function() {
                existingDropdown.style.display = 'none';
            }, 200);
        });

        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target)) {
                existingDropdown.style.display = 'none';
            }
        });
    });
});
</script>
