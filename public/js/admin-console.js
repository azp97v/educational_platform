(function () {
  var SCROLL_KEY_PREFIX = 'admin-scroll:';

  function currentScrollKey() {
    return SCROLL_KEY_PREFIX + window.location.pathname;
  }

  function restoreScrollPosition() {
    var content = document.querySelector('.admin-content');
    if (!content) return;
    try {
      var value = sessionStorage.getItem(currentScrollKey());
      var top = value ? parseInt(value, 10) : 0;
      if (!Number.isNaN(top) && top > 0) content.scrollTop = top;
    } catch (_) {}
  }

  function persistScrollPosition() {
    var content = document.querySelector('.admin-content');
    if (!content) return;
    try {
      sessionStorage.setItem(currentScrollKey(), String(content.scrollTop || 0));
    } catch (_) {}
  }

  function wireScrollPersistence() {
    var content = document.querySelector('.admin-content');
    if (!content) return;
    content.addEventListener('scroll', persistScrollPosition, { passive: true });
    window.addEventListener('beforeunload', persistScrollPosition);
  }

  function revealActiveNavItem() {
    var active = document.querySelector('.admin-nav .nav-btn.active');
    if (!active) return;
    try {
      active.scrollIntoView({ block: 'center', inline: 'nearest' });
    } catch (_) {
      active.scrollIntoView();
    }
  }

  function updateLiveTime() {
    var el = document.querySelector('#admin-live-time span');
    if (!el) return;
    var now = new Date();
    el.textContent = now.toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
  }

  function animateMetrics() {
    var metrics = document.querySelectorAll('.metric .v');
    metrics.forEach(function (node) {
      var raw = (node.textContent || '').replace(/[^0-9]/g, '');
      var target = parseInt(raw, 10);
      if (Number.isNaN(target) || target <= 0 || target > 10000000) return;
      var frame = 0;
      var steps = 24;
      var timer = setInterval(function () {
        frame += 1;
        var value = Math.round((target * frame) / steps);
        node.textContent = value.toLocaleString('en-US');
        if (frame >= steps) {
          clearInterval(timer);
          node.textContent = target.toLocaleString('en-US');
        }
      }, 26);
    });
  }

  function syncThemeSwitch() {
    var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    var text = document.querySelector('.admin-theme-switch .switch-text');
    var icon = document.querySelector('.admin-theme-switch .switch-icon i');
    if (text) text.textContent = isDark ? 'ليلي' : 'نهاري';
    if (icon) {
      icon.classList.remove('ri-sun-line', 'ri-moon-line');
      icon.classList.add(isDark ? 'ri-moon-line' : 'ri-sun-line');
    }
  }

  function wireSidebarToggle() {
    var btn     = document.getElementById('adminSidebarToggle');
    var sidebar = document.querySelector('.admin-sidebar');
    if (!btn || !sidebar) return;

    // Inject overlay element once
    var overlay = document.getElementById('adminSidebarOverlay');
    if (!overlay) {
      overlay = document.createElement('div');
      overlay.id = 'adminSidebarOverlay';
      overlay.className = 'admin-sidebar-overlay';
      document.body.appendChild(overlay);
    }

    function openSidebar() {
      sidebar.classList.add('sidebar-open');
      overlay.classList.add('overlay-open');
      document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
      sidebar.classList.remove('sidebar-open');
      overlay.classList.remove('overlay-open');
      document.body.style.overflow = '';
    }

    btn.addEventListener('click', function () {
      sidebar.classList.contains('sidebar-open') ? closeSidebar() : openSidebar();
    });
    overlay.addEventListener('click', closeSidebar);

    // Close on nav link click (page navigation)
    sidebar.querySelectorAll('.nav-btn').forEach(function (link) {
      link.addEventListener('click', closeSidebar);
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    updateLiveTime();
    var liveTimeTimer = setInterval(updateLiveTime, 1000);
    animateMetrics();

    document.addEventListener('visibilitychange', function () {
      if (document.hidden) {
        clearInterval(liveTimeTimer);
        liveTimeTimer = null;
        document.body.classList.add('tab-hidden');
      } else {
        if (!liveTimeTimer) {
          updateLiveTime();
          liveTimeTimer = setInterval(updateLiveTime, 1000);
        }
        document.body.classList.remove('tab-hidden');
      }
    });
    syncThemeSwitch();

    restoreScrollPosition();
    wireScrollPersistence();
    revealActiveNavItem();
    wireSidebarToggle();

    document.addEventListener('click', function (e) {
      var btn = e.target.closest('.admin-theme-switch');
      if (!btn) return;
      setTimeout(syncThemeSwitch, 50);
    });

    document.addEventListener('click', function (e) {
      var navLink = e.target.closest('.admin-nav .nav-btn');
      if (!navLink) return;
      persistScrollPosition();
    });

    window.addEventListener('storage', syncThemeSwitch);
  });
})();
