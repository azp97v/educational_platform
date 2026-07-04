<style>
.notification-wrapper {
  position: relative;
  display: inline-flex;
  align-items: center;
  overflow: visible !important;
  z-index: 2147483640;
}
.notification-btn {
  position: relative;
  overflow: visible !important;
  z-index: 1210;
}
.notification-badge {
  position: absolute;
  top: -8px;
  right: -8px;
  left: auto;
  min-width: 24px;
  height: 24px;
  padding: 0 6px;
  border-radius: 50%;
  background: #FF3B30;
  color: #fff;
  display: flex !important;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: 800;
  line-height: 1;
  pointer-events: none;
  box-shadow: 0 3px 10px rgba(255, 59, 48, 0.5);
  border: 2px solid var(--theme-surface, rgba(18,22,34,0.96));
  z-index: 1220 !important;
}
.notification-dropdown {
  position: fixed !important;
  top: 0;
  left: 0;
  width: min(380px, calc(100vw - 24px));
  max-height: min(70vh, 420px);
  background: color-mix(in srgb, var(--theme-surface, #101827) 96%, transparent);
  border: 1px solid var(--theme-border, rgba(255, 255, 255, 0.12));
  border-radius: 18px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.45);
  display: none;
  flex-direction: column;
  overflow: hidden;
  z-index: 2147483646 !important;
  opacity: 0;
  transform: translateY(-12px) scale(0.98);
  transform-origin: top right;
  transition: opacity 0.25s ease, transform 0.25s ease;
  isolation: isolate;
  pointer-events: auto;
}
.notification-dropdown.active {
  display: flex !important;
  opacity: 1;
  transform: translateY(0) scale(1);
  pointer-events: auto;
}
.notification-dropdown .dropdown-header,
.notification-dropdown .dropdown-footer {
  padding: 14px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}
.notification-dropdown .dropdown-header h4 {
  margin: 0;
  font-size: 13px;
  font-weight: 700;
  color: var(--theme-text, #F4F4F7);
}
.notification-dropdown .btn-close {
  background: transparent;
  border: none;
  color: var(--theme-text, #F4F4F7);
  font-size: 20px;
  cursor: pointer;
  line-height: 1;
}
.notification-dropdown .notification-list {
  max-height: 320px;
  overflow-y: auto;
  overscroll-behavior: contain;
  -webkit-overflow-scrolling: touch;
  background: color-mix(in srgb, var(--theme-surface, #101827) 96%, transparent);
  scrollbar-width: thin;
  scrollbar-color: rgba(198, 166, 117, 0.9) rgba(255, 255, 255, 0.08);
}
.notification-dropdown .notification-list::-webkit-scrollbar {
  width: 8px;
}
.notification-dropdown .notification-list::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.08);
  border-radius: 999px;
}
.notification-dropdown .notification-list::-webkit-scrollbar-thumb {
  background: linear-gradient(180deg, rgba(198, 166, 117, 0.98), rgba(151, 114, 62, 0.95));
  border-radius: 999px;
}
.notification-dropdown .notification-list::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(180deg, rgba(205, 175, 125, 0.98), rgba(151, 114, 62, 0.98));
}
.notification-dropdown .notification-item {
  display: flex;
  gap: 12px;
  padding: 14px 16px;
  border-bottom: 1px solid var(--theme-border, rgba(255, 255, 255, 0.1));
  text-decoration: none;
  color: inherit;
  align-items: flex-start;
  pointer-events: auto;
}
.notification-dropdown .notification-item:last-child {
  border-bottom: none;
}
.notification-dropdown .notification-icon {
  width: 36px;
  height: 36px;
  border-radius: 12px;
  background: var(--gold-light, rgba(198, 166, 117, 0.16));
  display: grid;
  place-items: center;
  color: var(--gold, #C6A675);
  font-size: 18px;
  flex-shrink: 0;
}
.notification-dropdown .notification-details {
  min-width: 0;
  flex: 1;
}
.notification-dropdown .notification-title {
  margin-bottom: 4px;
  font-size: 13px;
  font-weight: 700;
  color: var(--theme-text, #F4F4F7);
}
.notification-dropdown .notification-text {
  font-size: 13px;
  color: var(--theme-text-soft, #B8B9C5);
  margin-bottom: 6px;
}
.notification-dropdown .notification-time {
  font-size: 11px;
  color: var(--theme-text-muted, #7F8395);
}
.notification-dropdown .empty-message {
  padding: 16px;
  text-align: center;
  color: var(--theme-text-soft, #B8B9C5);
  font-size: 13px;
}
.notification-dropdown .btn-secondary {
  width: 100%;
  text-align: center;
  position: relative;
  z-index: 1;
}

.notification-backdrop {
  position: fixed;
  inset: 0;
  background: transparent;
  z-index: 2147483645 !important;
  display: none;
  pointer-events: none;
}

.notification-backdrop.active {
  display: block;
  pointer-events: auto;
}

/* ── Teacher-account: always dark notification dropdown ── */
html.teacher-account .notification-dropdown {
  background: color-mix(in srgb, var(--theme-surface, #0E121E) 97%, transparent) !important;
  border-color: var(--theme-border, rgba(255,255,255,0.12)) !important;
  box-shadow: 0 24px 70px rgba(0,0,0,0.55) !important;
}
html.teacher-account .notification-dropdown .dropdown-header h4,
html.teacher-account .notification-dropdown .notification-title {
  color: var(--text-primary, #F4F4F7) !important;
}
html.teacher-account .notification-dropdown .btn-close {
  background: var(--theme-soft, rgba(255,255,255,0.1)) !important;
  color: var(--text-primary, #F4F4F7) !important;
}
html.teacher-account .notification-dropdown .btn-close:hover {
  background: var(--theme-soft-2, rgba(255,255,255,0.18)) !important;
}
html.teacher-account .notification-dropdown .notification-list {
  background: transparent !important;
}
html.teacher-account .notification-dropdown .notification-item {
  border-bottom-color: var(--theme-border-light, rgba(255,255,255,0.07)) !important;
  color: var(--text-primary, #F4F4F7);
}
html.teacher-account .notification-dropdown .notification-item:hover {
  background: var(--gold-light, rgba(198,166,117,0.06)) !important;
}
html.teacher-account .notification-dropdown .notification-item.unread {
  background: var(--gold-light, rgba(198,166,117,0.1)) !important;
  border-right: 3px solid var(--gold, #C6A675) !important;
}
html.teacher-account .notification-dropdown .notification-text {
  color: var(--text-secondary, rgba(244,244,247,0.72)) !important;
}
html.teacher-account .notification-dropdown .notification-time {
  color: var(--text-tertiary, rgba(244,244,247,0.48)) !important;
}
html.teacher-account .notification-dropdown .empty-message {
  color: var(--text-secondary, rgba(244,244,247,0.55)) !important;
}
html.teacher-account .notification-dropdown .dropdown-footer {
  border-top-color: var(--theme-border-light, rgba(255,255,255,0.08)) !important;
}
html.teacher-account .notification-dropdown .btn-secondary {
  border: 1px solid var(--theme-border-strong, rgba(198,166,117,0.35));
  color: var(--gold, #C6A675);
  background: var(--gold-light, rgba(198,166,117,0.08));
  border-radius: 10px;
  padding: 8px 14px;
  transition: background 0.15s;
}
html.teacher-account .notification-dropdown .btn-secondary:hover {
  background: var(--theme-gold-aura, rgba(198,166,117,0.16)) !important;
}

body.theme-light .notification-dropdown,
body[data-theme="light"] .notification-dropdown,
[data-theme="light"] .notification-dropdown {
  background: color-mix(in srgb, var(--theme-surface, #ffffff) 98%, transparent);
  border: 1px solid var(--theme-border, rgba(34, 43, 58, 0.14));
  box-shadow: 0 20px 50px color-mix(in srgb, var(--theme-page-bg, #cfd8e4) 22%, transparent);
}

body.theme-light .notification-dropdown .dropdown-header h4,
body[data-theme="light"] .notification-dropdown .dropdown-header h4,
[data-theme="light"] .notification-dropdown .dropdown-header h4,
body.theme-light .notification-dropdown .btn-close,
body[data-theme="light"] .notification-dropdown .btn-close,
[data-theme="light"] .notification-dropdown .btn-close,
body.theme-light .notification-dropdown .notification-title,
body[data-theme="light"] .notification-dropdown .notification-title,
[data-theme="light"] .notification-dropdown .notification-title {
  color: var(--theme-text, #222b3a);
}

body.theme-light .notification-dropdown .notification-list,
body[data-theme="light"] .notification-dropdown .notification-list,
[data-theme="light"] .notification-dropdown .notification-list {
  background: color-mix(in srgb, var(--theme-surface, #ffffff) 98%, transparent);
}

body.theme-light .notification-dropdown .notification-item,
body[data-theme="light"] .notification-dropdown .notification-item,
[data-theme="light"] .notification-dropdown .notification-item {
  border-bottom-color: var(--theme-border, rgba(34, 43, 58, 0.08));
}

body.theme-light .notification-dropdown .notification-text,
body[data-theme="light"] .notification-dropdown .notification-text,
[data-theme="light"] .notification-dropdown .notification-text {
  color: var(--theme-text-soft, rgba(34, 43, 58, 0.78));
}

body.theme-light .notification-dropdown .notification-time,
body[data-theme="light"] .notification-dropdown .notification-time,
[data-theme="light"] .notification-dropdown .notification-time {
  color: var(--theme-text-muted, rgba(34, 43, 58, 0.56));
}
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const notificationFetchRoute = '{{ route('notifications.fetch') }}';
    let notificationBtn = document.getElementById('notificationBtn');

    if (!notificationBtn) {
      const icon = document.querySelector('.topbar .icon-btn i.ri-notification-3-line, .header-actions .icon-btn i.ri-notification-3-line, .icon-btn i.ri-notification-3-line');
      notificationBtn = icon ? icon.closest('.icon-btn') : null;
    }

    if (!notificationBtn) {
      return;
    }

    let wrapper = notificationBtn.closest('.notification-wrapper');
    let dropdown = wrapper ? wrapper.querySelector('.notification-dropdown') : null;
    let badge = wrapper ? wrapper.querySelector('.notification-badge') : null;
    let backdrop = document.getElementById('notificationBackdrop');

    if (!backdrop) {
      backdrop = document.createElement('div');
      backdrop.className = 'notification-backdrop';
      backdrop.id = 'notificationBackdrop';
      document.body.appendChild(backdrop);
    }

    if (!wrapper) {
      wrapper = document.createElement('div');
      wrapper.className = 'notification-wrapper';
      notificationBtn.parentNode.insertBefore(wrapper, notificationBtn);
      wrapper.appendChild(notificationBtn);

      badge = document.createElement('span');
      badge.className = 'notification-badge';
      badge.id = 'notificationBadge';
      badge.setAttribute('style', 'display: none !important');
      wrapper.appendChild(badge);

      dropdown = document.createElement('div');
      dropdown.className = 'notification-dropdown';
      dropdown.id = 'notificationDropdown';
      dropdown.innerHTML = `
        <div class="dropdown-header">
          <h4>الإشعارات</h4>
          <button class="btn-close" type="button" aria-label="إغلاق الإشعارات">×</button>
        </div>
        <div class="notification-list">
          <p class="empty-message">جارِ تحميل الإشعارات...</p>
        </div>
        <div class="dropdown-footer">
          <a href="{{ route('notifications.index') }}" class="btn btn-secondary btn-sm w-100">عرض الكل</a>
        </div>
      `;
      wrapper.appendChild(dropdown);
    } else if (!badge) {
      badge = wrapper.querySelector('.notification-badge');
    }
    if (!dropdown) {
      dropdown = wrapper.querySelector('.notification-dropdown');
    }
    if (dropdown && dropdown.parentElement !== document.body) {
      document.body.appendChild(dropdown);
    }

    if (!badge || !dropdown) {
      return;
    }

    const closeBtn = dropdown.querySelector('.btn-close');
    const list = dropdown.querySelector('.notification-list');
    let notificationsLoaded = false;

    dropdown.addEventListener('click', function (event) {
      event.stopPropagation();
    });

    if (!closeBtn) {
      return;
    }

    const iconMap = {
      enrollment: 'ri-user-add-line',
      inquiry: 'ri-question-line',
      message: 'ri-message-2-line',
      chat: 'ri-message-2-line',
      question: 'ri-chat-3-line',
      answer: 'ri-chat-3-line',
      support: 'ri-headset-line',
      announcement: 'ri-notification-3-line',
      alert: 'ri-alert-line',
      system: 'ri-notification-3-line',
      course: 'ri-book-open-line',
      lesson: 'ri-book-2-line',
      exam: 'ri-survey-line',
      achievement: 'ri-medal-line',
      attendance: 'ri-checkbox-circle-line',
      payment: 'ri-wallet-line',
      general: 'ri-notification-3-line'
    };

    if (notificationBtn) {
      notificationBtn.setAttribute('aria-haspopup', 'true');
      notificationBtn.setAttribute('aria-expanded', 'false');
    }
    if (dropdown) {
      dropdown.setAttribute('role', 'menu');
      dropdown.setAttribute('aria-label', 'قائمة الإشعارات');
    }

    function escapeHtml(value) {
      const div = document.createElement('div');
      div.textContent = value;
      return div.innerHTML;
    }

    function getNotificationIcon(item) {
      const icon = String(item.icon || '').trim();
      if (icon && icon.startsWith('ri-')) {
        return icon;
      }
      return iconMap[item.category] || iconMap[item.type] || iconMap.general;
    }

    function renderNotificationItems(items) {
      if (!list) return;
      if (!items.length) {
        list.innerHTML = '<p class="empty-message">لا توجد إشعارات جديدة</p>';
        return;
      }
      list.innerHTML = items.map(item => `
        <a href="${item.url}" class="notification-item ${item.read_at ? '' : 'unread'}">
          <div class="notification-icon"><i class="${getNotificationIcon(item)}"></i></div>
          <div class="notification-details">
            <div class="notification-title">${escapeHtml(item.title || 'إشعار جديد')}</div>
            <div class="notification-text">${escapeHtml(item.message || '')}</div>
            <div class="notification-time">${escapeHtml(item.created_at || '')}</div>
          </div>
        </a>
      `).join('');
    }

    function refreshNotifications() {
      fetch(notificationFetchRoute, {
        headers: { 'Accept': 'application/json' },
        credentials: 'same-origin'
      })
        .then(response => response.json())
        .then(data => {
          if (!data.success) {
            if (list) list.innerHTML = '<p class="empty-message">تعذر تحميل الإشعارات</p>';
            return;
          }
          badge.textContent = data.unread_count || '';
          badge.setAttribute('style', 'display: ' + (data.unread_count ? 'flex !important' : 'none !important'));
          renderNotificationItems(data.notifications || []);
          notificationsLoaded = true;
        })
        .catch(() => {
          if (list) list.innerHTML = '<p class="empty-message">فشل تحميل الإشعارات</p>';
        });
    }

    function updateDropdownPosition() {
      const rect = notificationBtn.getBoundingClientRect();
      const dropdownWidth = Math.min(380, window.innerWidth - 24);
      const dropdownHeight = dropdown.offsetHeight || 420;
      const top = rect.bottom + 10;
      const left = Math.min(Math.max(rect.right - dropdownWidth, 12), window.innerWidth - dropdownWidth - 12);
      dropdown.style.top = Math.min(top, window.innerHeight - dropdownHeight - 12) + 'px';
      dropdown.style.left = left + 'px';
    }

    function openDropdown() {
      dropdown.classList.add('active');
      backdrop.classList.add('active');
      updateDropdownPosition();
      requestAnimationFrame(updateDropdownPosition);
      if (notificationBtn) {
        notificationBtn.setAttribute('aria-expanded', 'true');
      }
      if (!notificationsLoaded) {
        refreshNotifications();
      }
    }

    function closeDropdown() {
      dropdown.classList.remove('active');
      backdrop.classList.remove('active');
      if (notificationBtn) {
        notificationBtn.setAttribute('aria-expanded', 'false');
      }
    }

    notificationBtn.addEventListener('click', function (event) {
      event.stopPropagation();
      if (dropdown.classList.contains('active')) {
        closeDropdown();
        return;
      }
      openDropdown();
    });

    closeBtn.addEventListener('click', function (event) {
      event.stopPropagation();
      closeDropdown();
    });

    document.addEventListener('click', function (event) {
      if (!event.target.closest('.notification-wrapper') && !event.target.closest('.notification-dropdown')) {
        closeDropdown();
      }
    });

    backdrop.addEventListener('click', closeDropdown);
    window.addEventListener('resize', function () {
      if (dropdown.classList.contains('active')) updateDropdownPosition();
    });
    window.addEventListener('scroll', function () {
      if (dropdown.classList.contains('active')) updateDropdownPosition();
    }, true);

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        closeDropdown();
      }
    });

    refreshNotifications();
    setInterval(refreshNotifications, 15000);

    document.addEventListener('visibilitychange', function() {
      if (document.hidden === false) {
        refreshNotifications();
      }
    });

    window.addEventListener('focus', function() {
      refreshNotifications();
    });
  });
</script>
