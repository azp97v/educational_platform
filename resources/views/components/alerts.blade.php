<!-- Elegant Alert System -->
<div id="alert-container" style="
  position: fixed;
  top: 16%;
  left: 50%;
  transform: translateX(-50%);
  z-index: 9999;
  display: flex;
  flex-direction: column;
  gap: 14px;
  max-width: 440px;
  width: calc(100% - 40px);
"></div>

<style>
  @keyframes slideInRight {
    from {
      transform: translateX(20px);
      opacity: 0;
    }
    to {
      transform: translateX(0);
      opacity: 1;
    }
  }

  @keyframes slideOutRight {
    from {
      transform: translateX(0);
      opacity: 1;
    }
    to {
      transform: translateX(100%);
      opacity: 0;
    }
  }

  @keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
  }

  .alert-box {
    position: relative;
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 18px 22px;
    border-radius: 18px;
    font-size: 14px;
    font-weight: 500;
    backdrop-filter: blur(18px);
    background: rgba(18, 21, 31, 0.94);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.22);
    animation: slideInRight 0.35s cubic-bezier(0.25, 1, 0.5, 1);
    border: 1px solid rgba(255, 255, 255, 0.08);
    font-family: 'Tajawal', sans-serif;
    overflow: hidden;
  }

  .alert-box.alert-closing {
    animation: slideOutRight 0.3s cubic-bezier(0.64, 0, 0.78, 0);
  }

  /* Success Alert */
  .alert-success {
    background: rgba(56, 181, 74, 0.15);
    color: #E7F7E8;
    border-color: rgba(56, 181, 74, 0.35);
  }

  .alert-success .alert-icon {
    font-size: 20px;
    color: #A9FFB8;
    animation: pulse 2.2s ease-in-out infinite;
  }

  /* Error Alert */
  .alert-error {
    background: rgba(255, 64, 64, 0.18);
    color: #FFE6E6;
    border-color: rgba(255, 64, 64, 0.35);
  }

  /* Warning Alert */
  .alert-warning {
    background: rgba(255, 151, 54, 0.16);
    color: #FFF2DA;
    border-color: rgba(255, 151, 54, 0.32);
  }

  /* Info Alert */
  .alert-info {
    background: rgba(32, 178, 170, 0.18);
    color: #E0F7F5;
    border-color: rgba(32, 178, 170, 0.35);
  }

  .alert-icon {
    flex-shrink: 0;
    width: 44px;
    height: 44px;
    font-size: 20px;
    display: grid;
    place-items: center;
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
  }

  .alert-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 4px;
  }

  .alert-title {
    font-weight: 600;
    font-size: 15px;
  }

  .alert-message {
    font-weight: 400;
    font-size: 13px;
    opacity: 0.95;
  }

  .alert-close {
    flex-shrink: 0;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: inherit;
    width: 38px;
    height: 38px;
    border-radius: 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    transition: transform 0.2s ease, background 0.2s ease;
  }

  .alert-close:hover {
    background: rgba(255, 255, 255, 0.16);
    transform: scale(1.05) rotate(90deg);
  }

  .alert-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 3px;
    background: rgba(255, 255, 255, 0.4);
    border-radius: 0 0 12px 0;
    animation: progressBar 5s linear;
  }

  @keyframes progressBar {
    from { width: 100%; }
    to { width: 0%; }
  }

  @media (max-width: 640px) {
    #alert-container {
      left: 50%;
      transform: translateX(-50%);
      width: calc(100% - 28px);
      top: 12%;
      max-width: 100%;
    }

    .alert-box {
      font-size: 13px;
      padding: 14px 16px;
      gap: 10px;
    }
  }

</style>

<script>
  window.__activeAlertKeys = window.__activeAlertKeys || new Set();

  function showAlert(message, type = 'success', title = null) {
    const container = document.getElementById('alert-container');
    if (!container || !message) return;

    const alertKey = `${type}::${String(message).trim()}`;
    if (window.__activeAlertKeys.has(alertKey)) {
      return;
    }
    window.__activeAlertKeys.add(alertKey);

    const alertBox = document.createElement('div');
    alertBox.className = `alert-box alert-${type}`;
    alertBox.style.position = 'relative';
    alertBox.dataset.alertKey = alertKey;
    
    // Default titles based on type
    const titles = {
      'success': '✓ نجح',
      'error': '✕ خطأ',
      'warning': '⚠ تحذير',
      'info': 'ℹ معلومة'
    };
    
    const displayTitle = title || titles[type] || 'رسالة';
    const icons = {
      'success': 'ri-check-line',
      'error': 'ri-close-line',
      'warning': 'ri-alert-line',
      'info': 'ri-information-line'
    };
    
    alertBox.innerHTML = `
      <i class="alert-icon ${icons[type]}"></i>
      <div class="alert-content">
        <div class="alert-title">${displayTitle}</div>
        <div class="alert-message">${message}</div>
      </div>
      <button class="alert-close">
        <i class="ri-close-line"></i>
      </button>
      <div class="alert-progress"></div>
    `;
    
    container.appendChild(alertBox);

    alertBox.querySelector('.alert-close')?.addEventListener('click', function() {
      alertBox.classList.add('alert-closing');
      setTimeout(function() {
        if (alertBox.dataset.alertKey && window.__activeAlertKeys) {
          window.__activeAlertKeys.delete(alertBox.dataset.alertKey);
        }
        alertBox.remove();
      }, 300);
    });

    // Auto-remove after 5 seconds
    setTimeout(() => {
      if (alertBox.parentElement) {
        alertBox.classList.add('alert-closing');
        setTimeout(() => {
          if (alertBox.dataset.alertKey && window.__activeAlertKeys) {
            window.__activeAlertKeys.delete(alertBox.dataset.alertKey);
          }
          alertBox.remove();
        }, 300);
      }
    }, 5000);
  }

  // Display flash messages on page load
  if (!window.__flashAlertsRendered) {
    window.__flashAlertsRendered = true;
    @if(session('success'))
      showAlert("{{ session('success') }}", 'success', 'تم بنجاح ✓');
    @endif

    @if(session('error'))
      showAlert("{{ session('error') }}", 'error', 'حدث خطأ ✕');
    @endif

    @if(session('warning'))
      showAlert("{{ session('warning') }}", 'warning', 'تحذير ⚠');
    @endif

    @if(session('info'))
      showAlert("{{ session('info') }}", 'info', 'معلومة');
    @endif
  }
</script>
