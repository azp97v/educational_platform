<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  @include('components.account-theme-head')
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>إعادة تعيين كلمة المرور | جمعية إجلال</title>
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

    /* ===== RESET PASSWORD CONTAINER ===== */
    .reset-container {
      width: 100%;
      max-width: 700px;
      background: linear-gradient(135deg, rgba(10, 14, 39, 0.95) 0%, rgba(22, 33, 62, 0.95) 100%);
      border: 2px solid var(--border-color);
      border-radius: 24px;
      padding: 4.5rem 4rem;
      box-shadow: 0 50px 120px rgba(0, 0, 0, 0.7);
      animation: slideInLeft 0.7s ease-out;
      backdrop-filter: blur(10px);
    }

    .reset-header {
      text-align: center;
      margin-bottom: 3.5rem;
      padding-bottom: 2.5rem;
      border-bottom: 2px solid var(--border-color);
      animation: slideUp 0.7s ease-out 0.1s both;
    }

    .reset-icon {
      width: 140px;
      height: 140px;
      background: linear-gradient(135deg, #10b981 0%, #047857 100%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 5rem;
      margin: 0 auto 2rem;
      box-shadow: 0 25px 60px rgba(16, 185, 129, 0.3);
      border: 4px solid rgba(16, 185, 129, 0.3);
    }

    .reset-header h1 {
      font-size: 2.3rem;
      color: white;
      margin-bottom: 1rem;
      font-weight: 800;
      letter-spacing: 0.8px;
    }

    .reset-header p {
      color: rgba(255, 255, 255, 0.8);
      font-size: 1.05rem;
      margin: 0;
      font-weight: 500;
      letter-spacing: 0.3px;
      line-height: 1.6;
    }

    /* ===== PASSWORD STRENGTH ===== */
    .password-strength {
      margin-bottom: 2rem;
      animation: slideUp 0.7s ease-out 0.2s both;
    }

    .strength-meter {
      width: 100%;
      height: 6px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 10px;
      overflow: hidden;
      margin-top: 1rem;
    }

    .strength-meter-fill {
      height: 100%;
      width: 0%;
      border-radius: 10px;
      transition: all 0.3s ease;
      background: linear-gradient(90deg, #ef4444 0%, #eab308 50%, #22c55e 100%);
    }

    .strength-text {
      font-size: 0.85rem;
      margin-top: 0.5rem;
      color: rgba(255, 255, 255, 0.7);
    }

    /* ===== ALERTS ===== */
    .alert {
      padding: 1.2rem 1.5rem;
      border-radius: 14px;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: flex-start;
      gap: 1rem;
      animation: slideUp 0.4s ease-out;
      backdrop-filter: blur(10px);
      border: 2px solid;
    }

    .alert-error {
      background: rgba(211, 47, 47, 0.12);
      border-color: rgba(211, 47, 47, 0.35);
      color: #ff6b6b;
    }

    .alert-error i {
      font-size: 1.5rem;
      flex-shrink: 0;
    }

    .alert-success {
      background: rgba(6, 167, 125, 0.12);
      border-color: rgba(6, 167, 125, 0.35);
      color: #81c784;
    }

    .alert-success i {
      font-size: 1.5rem;
      flex-shrink: 0;
    }

    /* ===== FORM STYLES ===== */
    .form-group {
      margin-bottom: 2rem;
      animation: slideUp 0.7s ease-out both;
    }

    .form-group:nth-child(1) { animation-delay: 0.15s; }
    .form-group:nth-child(2) { animation-delay: 0.25s; }
    .form-group:nth-child(3) { animation-delay: 0.35s; }

    .form-group label {
      display: block;
      font-weight: 700;
      color: rgba(255, 255, 255, 0.95);
      margin-bottom: 1rem;
      font-size: 1rem;
      letter-spacing: 0.3px;
      display: flex;
      align-items: center;
      gap: 0.8rem;
    }

    .form-group label i {
      color: #10b981;
      font-size: 1.2rem;
    }

    .form-control {
      width: 100%;
      padding: 1.2rem 1.5rem;
      background: rgba(255, 255, 255, 0.06);
      border: 2px solid var(--border-color);
      border-radius: 14px;
      font-size: 1rem;
      font-family: 'Cairo', sans-serif;
      color: white;
      transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
      backdrop-filter: blur(10px);
    }

    .form-control::placeholder {
      color: rgba(255, 255, 255, 0.5);
    }

    .form-control:focus {
      outline: none;
      border-color: #10b981;
      background: rgba(16, 185, 129, 0.1);
      box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
    }

    .form-control.is-invalid {
      border-color: #ff3b30;
      background: rgba(255, 59, 48, 0.08);
    }

    .form-control.is-invalid:focus {
      box-shadow: 0 0 0 3px rgba(255, 59, 48, 0.2);
    }

    .error-text {
      color: #ff6b6b;
      font-size: 0.85rem;
      margin-top: 0.6rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    /* ===== PASSWORD VISIBILITY TOGGLE ===== */
    .password-wrapper {
      position: relative;
    }

    .password-toggle {
      position: absolute;
      left: 1.5rem;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: rgba(255, 255, 255, 0.6);
      cursor: pointer;
      font-size: 1.2rem;
      transition: color 0.3s ease;
    }

    .password-toggle:hover {
      color: #10b981;
    }

    .form-control.has-toggle {
      padding-left: 3.5rem;
    }

    /* ===== REQUIREMENTS ===== */
    .requirements {
      background: rgba(16, 185, 129, 0.08);
      border: 2px solid rgba(16, 185, 129, 0.2);
      border-radius: 12px;
      padding: 1.5rem;
      margin: 2rem 0;
      animation: slideUp 0.7s ease-out 0.4s both;
    }

    .requirements-title {
      font-weight: 700;
      color: #10b981;
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
      gap: 0.6rem;
      font-size: 0.95rem;
    }

    .requirements-list {
      list-style: none;
      margin: 0;
      padding: 0;
    }

    .requirements-list li {
      color: rgba(255, 255, 255, 0.75);
      font-size: 0.9rem;
      margin-bottom: 0.8rem;
      display: flex;
      align-items: center;
      gap: 0.8rem;
      padding-left: 0;
    }

    .requirements-list li:last-child {
      margin-bottom: 0;
    }

    .requirements-list i {
      font-size: 1rem;
      color: rgba(16, 185, 129, 0.5);
      flex-shrink: 0;
    }

    .requirements-list li.met i {
      color: #10b981;
    }

    /* ===== BUTTONS ===== */
    .button-group {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1.5rem;
      margin-top: 3rem;
      animation: slideUp 0.7s ease-out 0.5s both;
    }

    .btn-reset {
      width: 100%;
      padding: 1.4rem;
      background: linear-gradient(135deg, #10b981 0%, #047857 100%);
      color: white;
      border: none;
      border-radius: 14px;
      font-size: 1.05rem;
      font-weight: 800;
      cursor: pointer;
      transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
      letter-spacing: 0.5px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 1rem;
      position: relative;
      overflow: hidden;
      box-shadow: 0 15px 40px rgba(16, 185, 129, 0.3);
    }

    .btn-reset:hover:not(:disabled) {
      transform: translateY(-3px);
      box-shadow: 0 22px 55px rgba(16, 185, 129, 0.5);
    }

    .btn-reset:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }

    .btn-back {
      padding: 1.4rem;
      background: rgba(255, 255, 255, 0.06);
      color: rgba(255, 255, 255, 0.9);
      border: 2px solid var(--border-color);
      border-radius: 14px;
      font-size: 1.05rem;
      font-weight: 800;
      cursor: pointer;
      transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
      letter-spacing: 0.5px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 1rem;
      font-family: 'Cairo', sans-serif;
      text-decoration: none;
    }

    .btn-back:hover {
      background: var(--border-color);
      border-color: #10b981;
      color: #10b981;
      transform: translateY(-2px);
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 600px) {
      .reset-container {
        padding: 2.5rem 1.8rem;
        border-radius: 18px;
      }

      .reset-header {
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
      }

      .reset-icon {
        width: 95px;
        height: 95px;
        font-size: 3.5rem;
        margin-bottom: 1.5rem;
      }

      .reset-header h1 {
        font-size: 1.6rem;
        margin-bottom: 0.6rem;
      }

      .reset-header p {
        font-size: 0.9rem;
      }

      .form-group {
        margin-bottom: 1.2rem;
      }

      .form-group label {
        font-size: 0.95rem;
        margin-bottom: 0.6rem;
        gap: 0.6rem;
      }

      .form-group label i {
        font-size: 1rem;
      }

      .form-control {
        padding: 0.85rem 1rem;
        font-size: 0.9rem;
        border-radius: 12px;
      }

      .form-control.has-toggle {
        padding-left: 3rem;
      }

      .password-toggle {
        left: 1rem;
        font-size: 1rem;
      }

      .requirements {
        padding: 1.2rem;
        margin: 1.5rem 0;
        border-radius: 12px;
      }

      .requirements-title {
        font-size: 0.9rem;
        margin-bottom: 0.8rem;
      }

      .requirements-list li {
        font-size: 0.85rem;
        margin-bottom: 0.6rem;
        gap: 0.6rem;
      }

      .requirements-list i {
        font-size: 0.95rem;
      }

      .button-group {
        grid-template-columns: 1fr;
        gap: 0.8rem;
        margin-top: 2rem;
      }

      .btn-reset,
      .btn-back {
        padding: 0.95rem;
        font-size: 0.95rem;
        border-radius: 12px;
      }
    }
  </style>
</head>
<body dir="rtl">
  <div class="reset-container">
    <!-- Header -->
    <div class="reset-header">
      <div class="reset-icon">🔐</div>
      <h1>إنشاء كلمة مرور جديدة</h1>
      <p>يرجى إدخال كلمة مرور جديدة قوية لحماية حسابك</p>
    </div>

    <!-- Alerts -->
    @if ($errors->any())
      <div class="alert alert-error">
        <i class="ri-error-warning-line"></i>
        <div>
          <strong>خطأ!</strong> {{ $errors->first() }}
        </div>
      </div>
    @endif

    <!-- Reset Form -->
    <form action="{{ route('password.update') }}" method="POST" novalidate>
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">

      <!-- Email Field -->
      <div class="form-group @error('email') has-error @enderror">
        <label for="email">
          <i class="ri-mail-line"></i> البريد الإلكتروني
        </label>
        <input
            type="email"
            id="email"
            name="email"
            class="form-control @error('email') is-invalid @enderror"
            placeholder="أدخل بريدك الإلكتروني"
            value="{{ $email ?? old('email') }}"
            required
            readonly
        >
        @error('email')
          <div class="error-text">
            <i class="ri-alert-line"></i> {{ $message }}
          </div>
        @enderror
      </div>

      <!-- Password Field -->
      <div class="form-group @error('password') has-error @enderror">
        <label for="password">
          <i class="ri-lock-line"></i> كلمة المرور الجديدة
        </label>
        <div class="password-wrapper">
          <input
              type="password"
              id="password"
              name="password"
              class="form-control has-toggle @error('password') is-invalid @enderror"
              placeholder="أدخل كلمة مرور قوية"
              required
          >
          <button type="button" class="password-toggle" data-target="password">
            <i class="ri-eye-line"></i>
          </button>
        </div>
        @error('password')
          <div class="error-text">
            <i class="ri-alert-line"></i> {{ $message }}
          </div>
        @enderror
        <div class="password-strength">
          <div class="strength-meter">
            <div class="strength-meter-fill" id="strengthMeter"></div>
          </div>
          <div class="strength-text" id="strengthText">أدخل كلمة مرور قوية</div>
        </div>
      </div>

      <!-- Password Confirmation Field -->
      <div class="form-group @error('password_confirmation') has-error @enderror">
        <label for="password_confirmation">
          <i class="ri-lock-check-line"></i> تأكيد كلمة المرور
        </label>
        <div class="password-wrapper">
          <input
              type="password"
              id="password_confirmation"
              name="password_confirmation"
              class="form-control has-toggle @error('password_confirmation') is-invalid @enderror"
              placeholder="أعد إدخال كلمة المرور"
              required
          >
          <button type="button" class="password-toggle" data-target="password_confirmation">
            <i class="ri-eye-line"></i>
          </button>
        </div>
        @error('password_confirmation')
          <div class="error-text">
            <i class="ri-alert-line"></i> {{ $message }}
          </div>
        @enderror
      </div>

      <!-- Requirements -->
      <div class="requirements">
        <div class="requirements-title">
          <i class="ri-checkbox-circle-line"></i> متطلبات كلمة المرور
        </div>
        <ul class="requirements-list">
          <li id="req-length">
            <i class="ri-close-line"></i>
            <span>8 أحرف على الأقل</span>
          </li>
          <li id="req-uppercase">
            <i class="ri-close-line"></i>
            <span>رسالة كبيرة واحدة على الأقل (A-Z)</span>
          </li>
          <li id="req-lowercase">
            <i class="ri-close-line"></i>
            <span>رسالة صغيرة واحدة على الأقل (a-z)</span>
          </li>
          <li id="req-number">
            <i class="ri-close-line"></i>
            <span>رقم واحد على الأقل (0-9)</span>
          </li>
          <li id="req-special">
            <i class="ri-close-line"></i>
            <span>حرف خاص واحد على الأقل (!@#$%^&*)</span>
          </li>
          <li id="req-match">
            <i class="ri-close-line"></i>
            <span>تطابق كلا كلمات المرور</span>
          </li>
        </ul>
      </div>

      <!-- Buttons -->
      <div class="button-group">
        <button type="submit" class="btn-reset" id="submitBtn" disabled>
          <i class="ri-check-line"></i> إعادة تعيين كلمة المرور
        </button>
        <a href="{{ route('login') }}" class="btn-back">
          <i class="ri-arrow-right-line"></i> رجوع للدخول
        </a>
      </div>
    </form>
  </div>

  @include('components.account-theme-foot')

  <script>
    function togglePassword(e) {
      const button = e.currentTarget;
      const fieldId = button.getAttribute('data-target');
      const field = document.getElementById(fieldId);
      const icon = button.querySelector('i');

      if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('ri-eye-line');
        icon.classList.add('ri-eye-off-line');
      } else {
        field.type = 'password';
        icon.classList.remove('ri-eye-off-line');
        icon.classList.add('ri-eye-line');
      }
    }

    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    const strengthMeter = document.getElementById('strengthMeter');
    const strengthText = document.getElementById('strengthText');
    const submitBtn = document.getElementById('submitBtn');

    function checkPasswordStrength(password) {
      let strength = 0;
      const requirements = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /\d/.test(password),
        special: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password),
      };

      // Update requirements display
      updateRequirement('req-length', requirements.length);
      updateRequirement('req-uppercase', requirements.uppercase);
      updateRequirement('req-lowercase', requirements.lowercase);
      updateRequirement('req-number', requirements.number);
      updateRequirement('req-special', requirements.special);

      for (let key in requirements) {
        if (requirements[key]) strength++;
      }

      const passwordMatch = password && password === passwordConfirmInput.value;
      updateRequirement('req-match', passwordMatch);

      // Update strength meter
      const strengthPercent = (strength / 5) * 100;
      strengthMeter.style.width = strengthPercent + '%';

      if (strengthPercent === 0) {
        strengthText.textContent = 'أدخل كلمة مرور قوية';
        strengthText.style.color = 'rgba(255, 255, 255, 0.7)';
      } else if (strengthPercent < 40) {
        strengthText.textContent = 'كلمة مرور ضعيفة';
        strengthText.style.color = '#ef4444';
      } else if (strengthPercent < 70) {
        strengthText.textContent = 'كلمة مرور متوسطة';
        strengthText.style.color = '#eab308';
      } else {
        strengthText.textContent = 'كلمة مرور قوية';
        strengthText.style.color = '#22c55e';
      }

      checkFormValid();
    }

    function updateRequirement(id, met) {
      const elem = document.getElementById(id);
      if (met) {
        elem.classList.add('met');
        elem.querySelector('i').classList.remove('ri-close-line');
        elem.querySelector('i').classList.add('ri-check-line');
      } else {
        elem.classList.remove('met');
        elem.querySelector('i').classList.remove('ri-check-line');
        elem.querySelector('i').classList.add('ri-close-line');
      }
    }

    function checkFormValid() {
      const password = passwordInput.value;
      const passwordMatch = password && password === passwordConfirmInput.value;
      const hasStrength = /[A-Z]/.test(password) && /[a-z]/.test(password) && /\d/.test(password) && password.length >= 8;

      submitBtn.disabled = !(hasStrength && passwordMatch);
    }

    passwordInput.addEventListener('input', () => checkPasswordStrength(passwordInput.value));
    passwordConfirmInput.addEventListener('input', () => checkFormValid());

    document.querySelectorAll('.password-toggle').forEach(function(btn) {
      btn.addEventListener('click', togglePassword);
    });
  </script>
</body>
</html>

