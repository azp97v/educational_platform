<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
  <meta charset="UTF-8">`r`n  @include('components.account-theme-head')
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>تأكيد البريد الإلكتروني | جمعية إجلال</title>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Josefin+Slab:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
  
  <style>
    * {
      --gold: #C6752E;
      --dark-gold: #97722C;
      --light: #f6f3ee;
      --dark: #1a1a1a;
      --gray: #666666;
      --light-gray: #999999;
      --success: #06a77d;
      --danger: #D32F2F;
      --bg-dark: #0a0e27;
      --bg-darker: #05071a;
      --card-bg: rgba(255, 255, 255, 0.04);
      --card-hover: rgba(255, 255, 255, 0.08);
      --border-color: rgba(198, 117, 46, 0.25);
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      font-family: 'Cairo', sans-serif;
      background: linear-gradient(135deg, var(--bg-dark) 0%, #16213e 100%);
      color: white;
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
    .container {
      width: 100%;
      max-width: 550px;
      background: linear-gradient(135deg, rgba(10, 14, 39, 0.95) 0%, rgba(22, 33, 62, 0.95) 100%);
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

    .header h1 {
      font-size: 2rem;
      color: white;
      margin: 0 0 0.7rem 0;
      font-weight: 800;
      letter-spacing: 0.5px;
    }

    .header p {
      color: rgba(255, 255, 255, 0.75);
      font-size: 0.95rem;
      margin: 1rem 0;
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

    .alert.error {
      background: rgba(211, 47, 47, 0.12);
      border-color: rgba(211, 47, 47, 0.35);
      color: #ff6b6b;
    }

    .alert.error i {
      font-size: 1.5rem;
      flex-shrink: 0;
      margin-top: 0.2rem;
    }

    .alert.success {
      background: rgba(6, 167, 125, 0.12);
      border-color: rgba(6, 167, 125, 0.35);
      color: #81c784;
    }

    .alert.success i {
      font-size: 1.5rem;
      flex-shrink: 0;
      margin-top: 0.2rem;
    }

    .alert.info {
      background: rgba(3, 169, 244, 0.12);
      border-color: rgba(3, 169, 244, 0.35);
      color: #64b5f6;
    }

    .alert.info i {
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
      color: rgba(255, 255, 255, 0.95);
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
      justify-content: space-between;
      gap: 0.8rem;
      direction: ltr;
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
      background: rgba(255, 255, 255, 0.06);
      font-family: 'Courier New', monospace;
      backdrop-filter: blur(10px);
    }

    .otp-input::placeholder {
      color: rgba(255, 255, 255, 0.3);
    }

    .otp-input:focus {
      outline: none;
      border-color: var(--gold);
      background: rgba(198, 117, 46, 0.15);
      box-shadow: 0 0 0 3px rgba(198, 117, 46, 0.2);
      transform: scale(1.05);
    }

    .otp-input.filled {
      border-color: var(--gold);
      background: rgba(198, 117, 46, 0.12);
    }

    .otp-input.error {
      border-color: #ff3b30;
      background: rgba(255, 59, 48, 0.08);
      animation: shake 0.5s ease-in-out;
    }

    /* ===== TIMER SECTION ===== */
    .timer-section {
      text-align: center;
      margin: 2rem 0;
      padding: 1.5rem;
      background: rgba(198, 117, 46, 0.08);
      border: 1.5px solid rgba(198, 117, 46, 0.25);
      border-radius: 12px;
      animation: slideUp 0.7s ease-out 0.3s both;
    }

    .timer-text {
      font-size: 0.95rem;
      color: rgba(255, 255, 255, 0.85);
      margin-bottom: 0.8rem;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.6rem;
    }

    .timer-text i {
      color: var(--gold);
      font-size: 1.2rem;
    }

    .timer-countdown {
      font-size: 1.8rem;
      color: var(--gold);
      font-weight: 700;
      font-family: 'Courier New', monospace;
      letter-spacing: 0.3px;
    }

    .timer.expired .timer-countdown {
      color: #ff3b30;
    }

    /* ===== RESEND SECTION ===== */
    .resend-section {
      text-align: center;
      margin: 1.5rem 0;
      animation: slideUp 0.7s ease-out 0.4s both;
    }

    .resend-text {
      font-size: 0.9rem;
      color: rgba(255, 255, 255, 0.75);
      margin-bottom: 1rem;
    }

    .resend-button {
      background: rgba(198, 117, 46, 0.15);
      color: var(--gold);
      border: 1.5px solid rgba(198, 117, 46, 0.4);
      padding: 0.9rem 1.5rem;
      border-radius: 10px;
      font-size: 0.9rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
      letter-spacing: 0.3px;
      display: inline-block;
    }

    .resend-button:hover:not(:disabled) {
      background: var(--gold);
      color: white;
      border-color: var(--gold);
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(198, 117, 46, 0.3);
    }

    .resend-button:disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }

    .resend-button.cooldown {
      background: transparent;
      color: rgba(255, 255, 255, 0.6);
      border-color: rgba(255, 255, 255, 0.2);
      cursor: wait;
    }

    .resend-cooldown {
      font-size: 0.85rem;
      color: var(--gold);
      margin-top: 0.6rem;
      font-weight: 600;
      letter-spacing: 0.3px;
    }

    /* ===== SUBMIT BUTTON ===== */
    .verification-button {
      width: 100%;
      padding: 1.2rem;
      background: linear-gradient(135deg, var(--gold) 0%, var(--dark-gold) 100%);
      color: white;
      border: none;
      border-radius: 12px;
      font-size: 1rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
      letter-spacing: 0.5px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.8rem;
      position: relative;
      overflow: hidden;
      animation: slideUp 0.7s ease-out 0.5s both;
      box-shadow: 0 12px 30px rgba(198, 117, 46, 0.25);
      margin-top: 1.5rem;
    }

    .verification-button:hover:not(:disabled) {
      transform: translateY(-3px);
      box-shadow: 0 18px 45px rgba(198, 117, 46, 0.4);
    }

    .verification-button:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }

    /* ===== EDIT EMAIL ===== */
    .edit-email {
      text-align: center;
      margin-top: 2rem;
      padding-top: 1.5rem;
      border-top: 1px solid var(--border-color);
      animation: slideUp 0.7s ease-out 0.55s both;
    }

    .edit-email a {
      color: var(--gold);
      text-decoration: none;
      font-size: 0.95rem;
      font-weight: 600;
      transition: all 0.3s ease;
      letter-spacing: 0.3px;
    }

    .edit-email a:hover {
      color: rgba(255, 255, 255, 0.95);
      text-decoration: underline;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 600px) {
      .container {
        padding: 2.5rem 1.8rem;
        border-radius: 16px;
      }

      .header {
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
      }

      .header h1 {
        font-size: 1.6rem;
        margin-bottom: 0.5rem;
      }

      .otp-input {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
      }

      .otp-inputs {
        gap: 0.6rem;
      }

      .timer-section {
        padding: 1.2rem;
      }

      .timer-countdown {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body dir="rtl">
  <div class="container">
    <div class="header">
      <div class="logo">✉️</div>
      <h1>تحقق من بريدك الإلكتروني</h1>
      <p>لقد أرسلنا لك رمز التحقق<br>أدخله أدناه لتأكيد حسابك</p>
      <div class="email-display">
        {{ $email ?? 'your-email@example.com' }}
      </div>
    </div>

    @if ($errors->any())
      <div class="alert error">
        <i class="ri-error-warning-line"></i>
        <div>
          <strong>خطأ!</strong> {{ $errors->first() }}
        </div>
      </div>
    @endif

    @if (session('success'))
      <div class="alert success">
        <i class="ri-check-line"></i>
        <div>{{ session('success') }}</div>
      </div>
    @endif

    <form method="POST" action="{{ route('verification.verify') }}" id="verificationForm">
      @csrf

      <div class="form-group">
        <label for="otp-1"><i class="ri-lock-2-line"></i> أدخل الرمز (6 أرقام):</label>
        <div class="otp-inputs" id="otpInputs">
          @for ($i = 0; $i < 6; $i++)
            <input 
              type="text" 
              class="otp-input" 
              id="otp-{{ $i + 1 }}"
              maxlength="1" 
              inputmode="numeric" 
              data-index="{{ $i }}"
              name="otp[]"
              autocomplete="off"
              placeholder="•"
            >
          @endfor
        </div>
        <input type="hidden" name="email" value="{{ $email ?? '' }}">
      </div>

      <div class="timer-section">
        <div class="timer-text" id="timer">
          <i class="ri-time-line"></i>
          <span>الرمز ينتهي خلال:</span>
          <span class="timer-countdown" id="countdown">10:00</span>
        </div>
      </div>

      <div class="resend-section">
        <p class="resend-text">لم تتلقَ الرمز؟</p>
        <button 
          type="button" 
          class="resend-button" 
          id="resendBtn" 
          disabled
        >
          <i class="ri-refresh-line"></i> إعادة إرسال الرمز
        </button>
        <div class="resend-cooldown" id="resendCooldown"></div>
      </div>

      <button type="submit" class="verification-button" id="verifyBtn" disabled>
        <i class="ri-check-double-line"></i> تأكيد البريد الإلكتروني
      </button>
    </form>

    <div class="edit-email">
      <a href="{{ route('register') }}">← استخدم بريد إلكتروني مختلف</a>
    </div>
  </div>
  <script>
    // OTP Input Handling
    const inputs = document.querySelectorAll('.otp-input');
    const otpInputsContainer = document.getElementById('otpInputs');

    function initializeOTPInputs() {
      inputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
          this.value = this.value.replace(/[^0-9]/g, '');

          if (this.value) {
            this.classList.add('filled');
            if (index < inputs.length - 1) {
              inputs[index + 1].focus();
            }
          } else {
            this.classList.remove('filled');
          }

          checkFormCompletion();
        });

        input.addEventListener('keydown', function(e) {
          if (e.key === 'Backspace' && !this.value && index > 0) {
            inputs[index - 1].focus();
            inputs[index - 1].value = '';
            inputs[index - 1].classList.remove('filled');
          }
        });

        input.addEventListener('paste', function(e) {
          e.preventDefault();
          const paste = (e.clipboardData || window.clipboardData).getData('text');
          const digits = paste.replace(/[^0-9]/g, '').split('');
          digits.forEach((digit, i) => {
            if (inputs[index + i]) {
              inputs[index + i].value = digit;
              inputs[index + i].classList.add('filled');
            }
          });
          checkFormCompletion();
        });
      });
    }

    function checkFormCompletion() {
      const allFilled = Array.from(inputs).every(input => input.value.length === 1);
      document.getElementById('verifyBtn').disabled = !allFilled;
    }

    function getOTPValue() {
      return Array.from(inputs).map(input => input.value).join('');
    }

    // Countdown Timer
    let countdownInterval;

    function startCountdown() {
      let timeRemaining = 600; // 10 minutes

      function updateCountdown() {
        const minutes = Math.floor(timeRemaining / 60);
        const seconds = timeRemaining % 60;
        const display = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        document.getElementById('countdown').textContent = display;

        if (timeRemaining <= 0) {
          clearInterval(countdownInterval);
          document.getElementById('timer').classList.add('expired');
          document.getElementById('countdown').textContent = 'انتهت الصلاحية';
          document.getElementById('verifyBtn').disabled = true;
          otpInputsContainer.style.opacity = '0.5';
        } else {
          timeRemaining--;
        }
      }

      updateCountdown();
      countdownInterval = setInterval(updateCountdown, 1000);
    }

    // Resend Code Cooldown
    let resendCooldownInterval;
    let resendCooldownTime = 180;

    function startResendCooldown() {
      resendCooldownTime = 180;

      function updateCooldown() {
        const minutes = Math.floor(resendCooldownTime / 60);
        const seconds = resendCooldownTime % 60;
        const display = `بعد ${minutes}:${seconds.toString().padStart(2, '0')}`;

        document.getElementById('resendCooldown').textContent = display;
        document.getElementById('resendBtn').classList.add('cooldown');
        document.getElementById('resendBtn').disabled = true;

        if (resendCooldownTime <= 0) {
          clearInterval(resendCooldownInterval);
          document.getElementById('resendCooldown').textContent = '';
          document.getElementById('resendBtn').classList.remove('cooldown');
          document.getElementById('resendBtn').disabled = false;
          document.getElementById('resendBtn').innerHTML = '<i class="ri-refresh-line"></i> إعادة إرسال الرمز';
        } else {
          resendCooldownTime--;
        }
      }

      updateCooldown();
      resendCooldownInterval = setInterval(updateCooldown, 1000);
    }

    function resendCode() {
      const email = document.querySelector('input[name="email"]').value;
      startResendCooldown();
      
      const alert = document.createElement('div');
      alert.className = 'alert info';
      alert.innerHTML = '<i class="ri-check-line"></i><div>✓ تم إرسال الرمز مرة أخرى إلى بريدك الإلكتروني</div>';
      document.querySelector('.header').after(alert);
      
      setTimeout(() => alert.remove(), 4000);
    }

    // Form Submission
    document.getElementById('verificationForm').addEventListener('submit', function(e) {
      const otp = getOTPValue();
      if (otp.length !== 6) {
        e.preventDefault();
        otpInputsContainer.classList.add('error');
        setTimeout(() => otpInputsContainer.classList.remove('error'), 500);
        
        const alert = document.createElement('div');
        alert.className = 'alert error';
        alert.innerHTML = '<i class="ri-alert-line"></i><div>الرجاء إدخال جميع الأرقام الستة</div>';
        document.querySelector('.header').after(alert);
      }
    });

    inputs.forEach((input, index) => {
      input.addEventListener('input', function() {
        const otpInputs = document.querySelectorAll('input[name="otp[]"]');
        otpInputs[index].value = this.value;
      });
    });

    document.getElementById('resendBtn')?.addEventListener('click', resendCode);

    // Initialize on load
    document.addEventListener('DOMContentLoaded', function() {
      initializeOTPInputs();
      startCountdown();
      startResendCooldown();
    });
  </script>
  @include('components.account-theme-foot')
</body>
</html>

