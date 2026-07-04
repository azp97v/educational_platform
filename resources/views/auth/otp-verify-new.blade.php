<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  @include('components.account-theme-head')
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>التحقق برمز OTP | جمعية إجلال</title>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Josefin+Slab:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
  
  <style>
    * {
      --gold: var(--theme-gold);
      --dark-gold: var(--theme-gold-dark);
      --success: var(--theme-success);
      --danger: var(--theme-danger);
      --bg-dark: var(--theme-page-bg);
      --bg-darker: var(--theme-surface);
      --card-bg: var(--theme-surface);
      --card-hover: color-mix(in srgb, var(--theme-surface) 92%, var(--gold));
      --border-color: rgba(198, 166, 117, 0.25);
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      font-family: 'Cairo', sans-serif;
      background: linear-gradient(135deg, var(--theme-page-bg) 0%, var(--theme-surface) 100%);
      color: var(--text-primary);
      min-height: 100vh;
      scroll-behavior: smooth;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    /* ===== ANIMATIONS ===== */
    @keyframes slideInLeft {
      from { opacity: 0; transform: translateX(-40px); }
      to { opacity: 1; transform: translateX(0); }
    }

    @keyframes slideUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      25% { transform: translateX(-10px); }
      75% { transform: translateX(10px); }
    }

    /* ===== VERIFY CONTAINER ===== */
    .verify-box {
      width: 100%;
      max-width: 550px;
      background: linear-gradient(135deg, var(--theme-surface) 0%, var(--theme-surface-2) 100%);
      border: 1.5px solid var(--border-color);
      border-radius: 20px;
      padding: 3.5rem 3rem;
      box-shadow: 0 40px 100px rgba(0, 0, 0, 0.6);
      animation: slideInLeft 0.7s ease-out;
      backdrop-filter: blur(10px);
    }

    .header {
      text-align: center;
      margin-bottom: 3rem;
      padding-bottom: 2rem;
      border-bottom: 1.5px solid var(--border-color);
      animation: slideUp 0.7s ease-out 0.1s both;
    }

    .logo {
      font-size: 4rem;
      margin-bottom: 1.5rem;
      display: inline-block;
    }

    h1 {
      font-size: 2rem;
      color: var(--text-primary);
      margin: 0 0 0.7rem 0;
      font-weight: 800;
      letter-spacing: 0.5px;
    }

    .subtitle {
      color: var(--text-secondary);
      font-size: 0.95rem;
      margin: 1rem 0 0 0;
      line-height: 1.6;
      font-weight: 500;
    }

    .email-display {
      background: rgba(198, 117, 46, 0.12);
      border: 1.5px solid rgba(198, 117, 46, 0.35);
      border-radius: 12px;
      padding: 1.2rem;
      margin: 1.2rem 0 0 0;
      font-size: 0.95rem;
      color: var(--gold);
      word-break: break-all;
      font-weight: 600;
      letter-spacing: 0.3px;
    }

    /* ===== ALERTS ===== */
    .alert {
      padding: 1.2rem 1.5rem;
      border-radius: 12px;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: flex-start;
      gap: 1rem;
      animation: slideUp 0.4s ease-out;
      backdrop-filter: blur(10px);
      border: 1.5px solid;
    }

    .alert-error {
      background: rgba(211, 47, 47, 0.12);
      border-color: rgba(211, 47, 47, 0.35);
      color: var(--theme-danger-soft, #ff6b6b);
    }

    .alert-error i {
      font-size: 1.5rem;
      flex-shrink: 0;
      margin-top: 0.2rem;
    }

    .alert-success {
      background: rgba(6, 167, 125, 0.12);
      border-color: rgba(6, 167, 125, 0.35);
      color: var(--theme-success-soft, #81c784);
    }

    .alert-success i {
      font-size: 1.5rem;
      flex-shrink: 0;
      margin-top: 0.2rem;
    }

    /* ===== FORM GROUP ===== */
    .form-group {
      margin-bottom: 2rem;
      animation: slideUp 0.7s ease-out 0.2s both;
    }

    .form-group label {
      display: block;
      font-weight: 700;
      color: var(--text-primary);
      margin-bottom: 1.2rem;
      font-size: 0.95rem;
      letter-spacing: 0.3px;
      display: flex;
      align-items: center;
      gap: 0.6rem;
    }

    .form-group label i {
      color: var(--gold);
      font-size: 1.2rem;
    }

    /* ===== OTP INPUTS ===== */
    .otp-inputs {
      display: flex;
      justify-content: center;
      gap: 0.8rem;
      flex-wrap: wrap;
    }

    .otp-input {
      width: 70px;
      height: 70px;
      border: 1.5px solid var(--border-color);
      border-radius: 12px;
      font-size: 1.8rem;
      font-weight: 700;
      text-align: center;
      color: var(--gold);
      transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
      background: color-mix(in srgb, var(--theme-surface) 94%, transparent);
      font-family: 'Courier New', monospace;
      backdrop-filter: blur(10px);
      text-transform: uppercase;
    }

    .otp-input::placeholder {
      color: var(--text-muted);
    }

    .otp-input:focus {
      outline: none;
      border-color: var(--gold);
      background: color-mix(in srgb, var(--gold) 15%, var(--theme-surface));
      box-shadow: 0 0 0 3px rgba(198, 166, 117, 0.2);
      transform: scale(1.05);
    }

    .otp-input:disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }

    .otp-input.error {
      border-color: var(--theme-danger);
      background: rgba(255, 59, 48, 0.08);
      animation: shake 0.5s ease-in-out;
    }

    /* ===== TIMERS ===== */
    .timer-group {
      margin: 2rem 0;
      animation: slideUp 0.7s ease-out 0.3s both;
    }

    .timer,
    .resend-timer {
      text-align: center;
      padding: 1.5rem;
      background: rgba(198, 117, 46, 0.08);
      border: 1.5px solid rgba(198, 117, 46, 0.25);
      border-radius: 12px;
      backdrop-filter: blur(10px);
      margin-bottom: 1rem;
    }

    .timer-text,
    .resend-timer-text {
      font-size: 0.9rem;
      color: var(--text-secondary);
      margin-bottom: 0.8rem;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.6rem;
    }

    .timer-text i,
    .resend-timer-text i {
      color: var(--gold);
      font-size: 1.2rem;
    }

    .timer-value,
    .resend-timer-value {
      font-size: 1.8rem;
      color: var(--gold);
      font-weight: 700;
      font-family: 'Courier New', monospace;
      letter-spacing: 0.3px;
    }

    .timer-value.expired {
      color: var(--theme-danger);
    }

    .resend-timer {
      display: none;
    }

    /* ===== BUTTONS ===== */
    .button-group {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
      margin: 2rem 0;
      animation: slideUp 0.7s ease-out 0.4s both;
    }

    button {
      padding: 1.2rem;
      border: none;
      border-radius: 12px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
      font-size: 0.95rem;
      letter-spacing: 0.3px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.8rem;
      position: relative;
      overflow: hidden;
      font-family: 'Cairo', sans-serif;
    }

    .btn-verify {
      background: linear-gradient(135deg, var(--gold) 0%, var(--dark-gold) 100%);
      color: white;
      grid-column: 1 / -1;
      box-shadow: 0 12px 30px rgba(198, 117, 46, 0.25);
    }

    .btn-verify:hover:not(:disabled) {
      transform: translateY(-3px);
      box-shadow: 0 18px 45px rgba(198, 117, 46, 0.4);
    }

    .btn-verify:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }

    .btn-resend {
      background: rgba(198, 117, 46, 0.15);
      color: var(--gold);
      border: 1.5px solid rgba(198, 117, 46, 0.4);
    }

    .btn-resend:hover:not(:disabled) {
      background: var(--gold);
      color: white;
      border-color: var(--gold);
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(198, 117, 46, 0.3);
    }

    .btn-resend:disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }

    .back-btn {
      background: transparent;
      color: var(--text-secondary);
      border: 1.5px solid var(--border-color);
      width: 100%;
      grid-column: 1 / -1;
      margin-top: 1rem;
      animation: slideUp 0.7s ease-out 0.5s both;
    }

    .back-btn:hover {
      background: rgba(198, 117, 46, 0.1);
      border-color: var(--gold);
      color: var(--gold);
    }

    /* ===== NOTIFICATIONS ===== */
    .notification {
      position: fixed;
      top: 20px;
      right: 20px;
      padding: 1.2rem 1.5rem;
      border-radius: 12px;
      font-weight: 600;
      z-index: 9999;
      max-width: 400px;
      animation: slideUp 0.3s ease-out;
      display: flex;
      align-items: flex-start;
      gap: 1rem;
      backdrop-filter: blur(10px);
      border: 1.5px solid;
    }

    .notification.success {
      background: rgba(6, 167, 125, 0.12);
      border-color: rgba(6, 167, 125, 0.35);
      color: var(--theme-success-soft, #81c784);
    }

    .notification.error {
      background: rgba(211, 47, 47, 0.12);
      border-color: rgba(211, 47, 47, 0.35);
      color: var(--theme-danger-soft, #ff6b6b);
    }

    .notification.info {
      background: rgba(3, 169, 244, 0.12);
      border-color: rgba(3, 169, 244, 0.35);
      color: var(--text-secondary);
    }

    .notification i {
      font-size: 1.3rem;
      flex-shrink: 0;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 600px) {
      .verify-box {
        padding: 2.5rem 1.8rem;
        border-radius: 16px;
      }

      h1 {
        font-size: 1.6rem;
      }

      .otp-input {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
      }

      .otp-inputs {
        gap: 0.6rem;
      }

      .button-group {
        grid-template-columns: 1fr;
        gap: 0.8rem;
      }

      .btn-verify {
        grid-column: auto;
      }
    }
  </style>
</head>
<body dir="rtl">
  <div class="verify-box">
    <div class="header">
      <div class="logo">ًں”گ</div>
      <h1>التحقق برمز OTP</h1>
      <p class="subtitle">تم إرسال رمز التحقق إلى بريدك الإلكتروني<br>الرجاء إدخاله أدناه</p>
      <div class="email-display">
        <i class="ri-mail-line"></i> {{ $email }}
      </div>
    </div>

    @if ($errors->any())
      <div class="alert alert-error">
        <i class="ri-error-warning-line"></i>
        <div>
          <strong>خطأ!</strong> {{ $errors->first() }}
        </div>
      </div>
    @endif

    @if (session('error'))
      <div class="alert alert-error">
        <i class="ri-error-warning-line"></i>
        <div>{{ session('error') }}</div>
      </div>
    @endif

    @if (session('success'))
      <div class="alert alert-success">
        <i class="ri-check-line"></i>
        <div>{{ session('success') }}</div>
      </div>
    @endif

    <form action="{{ route('otp.verify') }}" method="POST" id="otpForm">
      @csrf
      <input type="hidden" name="email" value="{{ $email }}">
      <input type="hidden" id="otpFull" name="otp" value="">

      <div class="form-group">
        <label for="otp-1"><i class="ri-lock-2-line"></i> أدخل الرمز (6 أحرف)</label>
        <div class="otp-inputs" id="otpContainer">
          <input type="text" class="otp-input" id="otp-1" maxlength="1" inputmode="text" placeholder="•" autocomplete="off">
          <input type="text" class="otp-input" id="otp-2" maxlength="1" inputmode="text" placeholder="•" autocomplete="off">
          <input type="text" class="otp-input" id="otp-3" maxlength="1" inputmode="text" placeholder="•" autocomplete="off">
          <input type="text" class="otp-input" id="otp-4" maxlength="1" inputmode="text" placeholder="•" autocomplete="off">
          <input type="text" class="otp-input" id="otp-5" maxlength="1" inputmode="text" placeholder="•" autocomplete="off">
          <input type="text" class="otp-input" id="otp-6" maxlength="1" inputmode="text" placeholder="•" autocomplete="off">
        </div>
      </div>

      <div class="timer-group">
        <div class="timer">
          <div class="timer-text">
            <i class="ri-time-line"></i>
            <span>الرمز ينتهي خلال:</span>
          </div>
          <div class="timer-value" id="timerValue">10:00</div>
        </div>

        <div class="resend-timer">
          <div class="resend-timer-text">
            <i class="ri-timer-line"></i>
            <span>وقت الانتظار قبل طلب رمز جديد:</span>
          </div>
          <div class="resend-timer-value" id="resendTimerValue">01:00</div>
        </div>
      </div>

      <div class="button-group">
        <button type="submit" class="btn-verify" id="verifyBtn" disabled>
          <i class="ri-check-double-line"></i> تأكيد الرمز
        </button>
        <button type="button" class="btn-resend" id="resendBtn" disabled>
          <i class="ri-refresh-line"></i> إعادة الإرسال
        </button>
        <button type="button" class="back-btn" id="backBtn">
          <i class="ri-arrow-right-line"></i> رجوع
        </button>
      </div>
    </form>
  </div>

  <script>
    const otpInputs = document.querySelectorAll('.otp-input');
    const otpFull = document.getElementById('otpFull');
    const verifyBtn = document.getElementById('verifyBtn');
    const resendBtn = document.getElementById('resendBtn');
    const timerDisplay = document.getElementById('timerValue');
    const resendTimerDisplay = document.getElementById('resendTimerValue');
    const resendTimerContainer = document.querySelector('.resend-timer');
    
    let timeLeft = 600;
    let resendCooldown = 0;
    let timerInterval = null;
    let resendInterval = null;

    function updateOtpFull() {
      const otp = Array.from(otpInputs).map(input => input.value).join('');
      otpFull.value = otp;
      verifyBtn.disabled = otp.length !== 6;
    }

    function showNotification(message, type = 'info', duration = 4000) {
      const notification = document.createElement('div');
      notification.className = `notification ${type}`;
      notification.innerHTML = `
        <i class="ri-${type === 'success' ? 'check-line' : type === 'error' ? 'error-warning-line' : 'info-line'}"></i>
        <span>${message}</span>
      `;
      document.body.appendChild(notification);
      
      setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out forwards';
        setTimeout(() => notification.remove(), 300);
      }, duration);
    }

    otpInputs.forEach((input, index) => {
      input.addEventListener('input', (e) => {
        e.target.value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        if (e.target.value.length === 1 && index < otpInputs.length - 1) {
          otpInputs[index + 1].focus();
        }
        updateOtpFull();
      });

      input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !input.value && index > 0) {
          otpInputs[index - 1].focus();
        } else if (e.key === 'ArrowLeft' && index > 0) {
          otpInputs[index - 1].focus();
        } else if (e.key === 'ArrowRight' && index < otpInputs.length - 1) {
          otpInputs[index + 1].focus();
        }
      });

      input.addEventListener('paste', (e) => {
        e.preventDefault();
        const paste = (e.clipboardData || window.clipboardData).getData('text');
        const chars = paste.toUpperCase().replace(/[^A-Z0-9]/g, '');
        for (let i = 0; i < chars.length && (index + i) < otpInputs.length; i++) {
          otpInputs[index + i].value = chars[i];
        }
        updateOtpFull();
        otpInputs[Math.min(index + chars.length, otpInputs.length - 1)].focus();
      });
    });

    function updateTimerDisplay() {
      const mins = Math.floor(timeLeft / 60);
      const secs = timeLeft % 60;
      timerDisplay.textContent = `${mins}:${secs.toString().padStart(2, '0')}`;
      
      if (timeLeft <= 0) {
        timerDisplay.classList.add('expired');
        timerDisplay.textContent = 'انتهت الصلاحية';
        otpInputs.forEach(input => input.setAttribute('disabled', 'disabled'));
        verifyBtn.disabled = true;
        resendBtn.disabled = false;
        if (timerInterval) clearInterval(timerInterval);
      }
    }

    function updateResendTimerDisplay() {
      if (resendCooldown <= 0) {
        resendTimerContainer.style.display = 'none';
        resendBtn.disabled = false;
        if (resendInterval) clearInterval(resendInterval);
        return;
      }
      
      resendTimerContainer.style.display = 'block';
      const mins = Math.floor(resendCooldown / 60);
      const secs = resendCooldown % 60;
      resendTimerDisplay.textContent = `${mins}:${secs.toString().padStart(2, '0')}`;
      resendBtn.disabled = true;
    }

    function startTimer() {
      if (timerInterval) clearInterval(timerInterval);
      timerInterval = setInterval(() => {
        if (timeLeft > 0) {
          timeLeft--;
          updateTimerDisplay();
        }
      }, 1000);
    }

    function startResendCooldown() {
      if (resendInterval) clearInterval(resendInterval);
      resendInterval = setInterval(() => {
        if (resendCooldown > 0) {
          resendCooldown--;
          updateResendTimerDisplay();
        }
      }, 1000);
    }

    resendBtn.addEventListener('click', () => {
      if (resendBtn.disabled && resendCooldown > 0) return;
      
      resendBtn.disabled = true;
      const originalText = resendBtn.innerHTML;
      resendBtn.innerHTML = '<i class="ri-loader-4-line" style="animation: spin 1s linear infinite;"></i> جاري الإرسال...';
      
      showNotification('جاري إرسال رمز جديد...');
      
      fetch('{{ route("otp.resend") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
        body: JSON.stringify({ email: '{{ $email }}'.trim().toLowerCase() })
      })
      .then(r => r.json().then(data => ({status: r.status, data: data})))
      .then(({status, data}) => {
        if (status === 429) {
          resendCooldown = data.wait_time || 180;
          startResendCooldown();
          throw new Error(data.message || 'يرجى الانتظار قبل طلب رمز جديد');
        } else if (status === 404) {
          throw new Error(data.message || 'انتهت صلاحية طلب التحقق');
        } else if (data.success) {
          timeLeft = 600;
          resendCooldown = 60;
          
          otpInputs.forEach(input => {
            input.value = '';
            input.removeAttribute('disabled');
          });
          updateOtpFull();
          
          timerDisplay.classList.remove('expired');
          timerDisplay.textContent = '10:00';
          otpInputs[0].focus();
          updateTimerDisplay();
          updateResendTimerDisplay();
          
          if (timerInterval) clearInterval(timerInterval);
          if (resendInterval) clearInterval(resendInterval);
          
          verifyBtn.disabled = true;
          startTimer();
          startResendCooldown();
          
          showNotification('تم إرسال رمز التحقق الجديد ✓', 'success', 5000);
        } else {
          throw new Error(data.message || 'فشل إرسال الرمز');
        }
      })
      .catch(err => {
        showNotification('خطأ: ' + (err.message || 'فشل إرسال الرمز'), 'error', 5000);
        resendBtn.disabled = false;
        resendBtn.innerHTML = originalText;
      });
    });

    // Initialize
    updateTimerDisplay();
    updateResendTimerDisplay();
    startTimer();
    startResendCooldown();
    otpInputs[0].focus();

    document.getElementById('backBtn')?.addEventListener('click', function() {
        window.location.href = '{{ route('register') }}';
    });

    // Add spin animation
    const style = document.createElement('style');
    style.textContent = '@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }';
    document.head.appendChild(style);
  </script>

  <!-- Theme Manager Script -->
  @include('components.account-theme-foot')
</body>
</html>

