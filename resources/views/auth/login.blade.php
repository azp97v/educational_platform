<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  @include('components.account-theme-head')
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>تسجيل الدخول | جمعية إجلال</title>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Josefin+Slab:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

  <style>
    * {
      --gold: var(--theme-gold);
      --dark-gold: var(--theme-gold-dark);
      --light: #f6f3ee;
      --dark: #1a1a1a;
      --gray: #666666;
      --light-gray: #999999;
      --success: var(--theme-success);
      --danger: var(--theme-danger);
      --bg-dark: var(--theme-page-bg);
      --bg-darker: var(--theme-surface);
      --card-bg: var(--theme-soft);
      --card-hover: var(--theme-soft-2);
      --border-color: var(--theme-border-strong);
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      font-family: 'Cairo', sans-serif;
      background:
      radial-gradient(circle at 20% 20%, var(--theme-gold-soft), transparent 34%),
      linear-gradient(135deg, var(--theme-page-bg) 0%, var(--theme-surface) 50%, var(--theme-surface-2) 100%);
      color: var(--theme-text);
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

    @keyframes glow {
      0%, 100% { box-shadow: 0 0 10px rgba(198, 166, 117, 0.2); }
      50% { box-shadow: 0 0 20px rgba(198, 166, 117, 0.4); }
    }

    /* ===== LOGIN CONTAINER ===== */
    .login-container {
      width: min(92vw, 860px) !important;
      max-width: 600px !important;
      /* min-height: 780px; */
      background: linear-gradient(145deg, var(--theme-surface) 0%, var(--theme-surface-2) 100%);
      border: 1.5px solid var(--border-color);
      border-radius: 20px;
      padding: 3.5rem 3rem;
      box-shadow: 0 40px 100px rgba(0, 0, 0, 0.6);
      animation: slideInLeft 0.7s ease-out;
      backdrop-filter: blur(10px);
    }

    .login-header {
      text-align: center;
      margin-bottom: 1.3rem;
      padding-bottom: 0.95rem;
      border-bottom: 1.5px solid var(--border-color);
      animation: slideUp 0.7s ease-out 0.1s both;
    }

    .login-logo {
      width: 102px;
      height: 102px;
      background: linear-gradient(135deg, var(--gold) 0%, var(--dark-gold) 100%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 3.2rem;
      margin: 0 auto 0.9rem;
      box-shadow: 0 20px 50px rgba(198, 166, 117, 0.35);
      border: 3px solid rgba(198, 166, 117, 0.3);
    }

    .login-header h1 {
      font-size: 2rem;
      color: var(--theme-text);
      margin-bottom: 0.7rem;
      font-weight: 800;
      letter-spacing: 0.5px;
    }

    .login-header p {
      color: rgba(255, 255, 255, 0.75);
      font-size: 0.95rem;
      margin: 0;
      font-weight: 500;
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
      margin-bottom: 2.2rem;
      animation: slideUp 0.7s ease-out both;
      position: relative;
    }

    .form-group:nth-child(1) { animation-delay: 0.2s; }
    .form-group:nth-child(2) { animation-delay: 0.35s; }
    .form-group:nth-child(3) { animation-delay: 0.5s; }

    .form-group label {
      display: block;
      font-weight: 800;
      color: rgba(255, 255, 255, 0.95);
      margin-bottom: 1rem;
      font-size: 1rem;
      letter-spacing: 0.4px;
      display: flex;
      align-items: center;
      gap: 0.8rem;
      transition: all 0.3s ease;
    }

    .form-group:hover label {
      color: var(--gold);
    }

    .form-wrapper {
      display: flex;
      flex-direction: column;
      gap: 1.3rem;
      min-height: 430px;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      animation: slideUp 0.7s ease-out both;
      margin-bottom: 0;
    }

    .form-group label i {
      color: var(--gold);
      font-size: 1.3rem;
      transition: all 0.3s ease;
    }

    .form-group:hover label i {
      transform: scale(1.15);
    }

    .form-control {
      width: 100%;
      padding: 1.2rem 1.5rem;
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.04) 0%, rgba(255, 255, 255, 0.02) 100%);
      border: 2px solid var(--border-color);
      border-radius: 14px;
      font-size: 1rem;
      font-family: 'Cairo', sans-serif;
      color: var(--theme-text);
      transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
      backdrop-filter: blur(10px);
      position: relative;
    }

    .form-control::placeholder {
      color: rgba(255, 255, 255, 0.45);
      font-weight: 500;
    }

    .form-control:hover:not(:focus) {
      border-color: rgba(198, 166, 117, 0.4);
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.06) 0%, rgba(255, 255, 255, 0.03) 100%);
    }

    .form-control:focus {
      outline: none;
      border-color: var(--gold);
      background: var(--theme-soft);
      box-shadow: 0 0 0 4px rgba(198, 166, 117, 0.2), 0 8px 25px rgba(198, 166, 117, 0.15);
      transform: translateY(-2px);
    }

    .form-control.is-invalid {
      border-color: #ff3b30;
      background: rgba(255, 59, 48, 0.08);
    }

    .form-control.is-invalid:focus {
      box-shadow: 0 0 0 4px rgba(255, 59, 48, 0.2), 0 8px 25px rgba(255, 59, 48, 0.15);
    }

    .error-text {
      color: #ff6b6b;
      font-size: 0.9rem;
      margin-top: 0.8rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-weight: 600;
      animation: slideUp 0.3s ease-out;
    }

    .error-text::before {
      content: '';
      width: 4px;
      height: 4px;
      background: #ff6b6b;
      border-radius: 50%;
      margin-right: 0.3rem;
    }

    /* ===== REMEMBER & FORGOT ===== */
    .remember-forgot {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin: 1rem 0 1.2rem 0;
      font-size: 0.95rem;
      animation: slideUp 0.7s ease-out 0.5s both;
      gap: 1.2rem;
      padding: 0.85rem 1rem;
      background: rgba(198, 166, 117, 0.05);
      border-radius: 14px;
      border: 1px solid rgba(198, 166, 117, 0.15);
      transition: all 0.3s ease;
    }

    .remember-forgot:hover {
      background: rgba(198, 166, 117, 0.08);
      border-color: rgba(198, 166, 117, 0.25);
    }

    .remember-checkbox {
      display: flex;
      align-items: center;
      gap: 0.65rem;
      cursor: pointer;
      color:var(--theme-gold-dark);
      font-weight: 700;
      transition: all 0.3s ease;
      letter-spacing: 0.3px;
    }

    .remember-checkbox:hover {
      color: var(--theme-gold-dark);
      transform: translateX(2px);
    }

    .remember-checkbox input[type="checkbox"] {
      cursor: pointer;
      width: 20px;
      height: 20px;
      appearance: none;
      -webkit-appearance: none;
      border: 1.5px solid color-mix(in srgb, var(--gold) 55%, #fff 15%);
      border-radius: 6px;
      background: color-mix(in srgb, var(--theme-surface) 86%, #000 14%);
      transition: all 0.25s ease;
      display: grid;
      place-content: center;
      box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.25);
    }

    .remember-checkbox input[type="checkbox"]:hover {
      transform: scale(1.1);
    }

    .remember-checkbox input[type="checkbox"]::before {
      content: '';
      width: 10px;
      height: 10px;
      border-radius: 3px;
      transform: scale(0);
      transition: transform 0.2s ease;
      background: linear-gradient(145deg, var(--gold), var(--dark-gold));
    }

    .remember-checkbox input[type="checkbox"]:checked {
      border-color: var(--gold);
      box-shadow: 0 0 0 3px rgba(198, 166, 117, 0.2);
    }

    .remember-checkbox input[type="checkbox"]:checked::before {
      transform: scale(1);
    }

    .forgot-password {
      color: var(--gold);
      text-decoration: none;
      font-weight: 800;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      letter-spacing: 0.4px;
      display: inline-flex;
      align-items: center;
      gap: 0.6rem;
      font-size: 0.95rem;
      white-space: nowrap;
    }

    .forgot-password:hover {
      color: var(--theme-gold-dark);
      transform: translateX(-4px);
    }

    .forgot-password i {
      transition: all 0.3s ease;
      font-size: 1.1rem;
    }

    .forgot-password:hover i {
      transform: rotate(-15deg);
    }

    /* ===== BUTTONS ===== */
    .login-button {
      width: 100%;
      padding: 1.4rem;
      background: linear-gradient(135deg, var(--gold) 0%, var(--dark-gold) 100%);
      color: var(--theme-text);
      border: none;
      border-radius: 14px;
      font-size: 1.05rem;
      font-weight: 800;
      cursor: pointer;
      transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
      margin-top: 1rem;
      letter-spacing: 0.5px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.9rem;
      position: relative;
      overflow: hidden;
      animation: slideUp 0.7s ease-out 0.55s both;
      box-shadow: 0 15px 45px rgba(198, 166, 117, 0.35);
    }

    .login-button::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: rgba(255, 255, 255, 0.2);
      transition: left 0.5s ease;
    }

    .login-button:hover:not(:disabled)::before {
      left: 100%;
    }

    .login-button:hover:not(:disabled) {
      transform: translateY(-4px);
      box-shadow: 0 22px 60px rgba(198, 166, 117, 0.5);
    }

    .login-button:active:not(:disabled) {
      transform: translateY(-2px);
    }

    .login-button:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }

    .login-button i {
      font-size: 1.2rem;
      transition: all 0.3s ease;
      box-shadow: inset 0 0 0 1px rgba(198, 166, 117, 0.28);
    }

    .login-button:hover:not(:disabled) i {
      transform: scale(1.2);
    }

    /* ===== DIVIDER ===== */
    .divider {
      display: flex;
      align-items: center;
      gap: 14px;
      margin: 1rem 0 0.75rem;
      animation: slideUp 0.7s ease-out 0.6s both;
    }

    .divider::before,
    .divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: linear-gradient(90deg, transparent 0%, var(--border-color) 100%);
    }

    .divider::after {
      background: linear-gradient(90deg, var(--border-color) 0%, transparent 100%);
    }

    .divider-text {
      position: static;
      padding: 0;
      color: rgba(255, 255, 255, 0.75);
      font-size: 0.9rem;
      font-weight: 700;
      letter-spacing: 0.4px;
    }

    /* ===== SIGNUP LINK ===== */
    .signup-link {
      text-align: center;
      margin-top: 0.6rem;
      font-size: 1rem;
      color: rgba(255, 255, 255, 0.85);
      animation: slideUp 0.7s ease-out 0.65s both;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.8rem;
      font-weight: 600;
      letter-spacing: 0.3px;
    }

    .signup-link a {
      color: var(--gold);
      text-decoration: none;
      font-weight: 800;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      letter-spacing: 0.4px;
      display: inline-flex;
      align-items: center;
      gap: 0.6rem;
      padding: 0.6rem 1.2rem;
      border-radius: 10px;
      border: 2px solid transparent;
    }

    .signup-link a:hover {
      color: var(--theme-text);
      border-color: var(--gold);
      background: rgba(198, 166, 117, 0.1);
      transform: translateX(-3px);
    }

    .signup-link a i {
      transition: all 0.3s ease;
      font-size: 1.1rem;
    }

    .signup-link a:hover i {
      transform: translateX(-4px);
    }

    /* ===== LIGHT THEME CONTRAST FIX ===== */
    body.theme-light .login-header p,
    body[data-theme="light"] .login-header p,
    body.theme-light .form-group label,
    body[data-theme="light"] .form-group label,
    body.theme-light .divider-text,
    body[data-theme="light"] .divider-text,
    body.theme-light .signup-link,
    body[data-theme="light"] .signup-link {
      color: var(--theme-text-soft);
    }

    body.theme-light .form-control::placeholder,
    body[data-theme="light"] .form-control::placeholder {
      color: rgba(34, 43, 58, 0.55);
    }

    body.theme-light .remember-forgot,
    body[data-theme="light"] .remember-forgot {
      background: color-mix(in srgb, var(--theme-surface) 86%, white 14%);
      border-color: var(--theme-border);
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 700px) {
      .login-container {
        width: 100% !important;
        min-height: 0;
        padding: 2.5rem 1.8rem;
        border-radius: 18px;
      }

      .login-header {
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
      }

      .login-logo {
        width: 95px;
        height: 95px;
        font-size: 3.5rem;
      }

      .login-header h1 {
        font-size: 1.6rem;
        margin-bottom: 0.6rem;
      }

      .login-header p {
        font-size: 0.9rem;
      }

      .form-group {
        margin-bottom: 1.6rem;
      }

      .form-group label {
        font-size: 0.95rem;
        margin-bottom: 0.8rem;
      }

      .form-control {
        padding: 1rem 1.2rem;
        font-size: 0.96rem;
        border-radius: 12px;
      }

      .remember-forgot {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
        margin: 2rem 0;
        padding: 1.2rem;
      }

      .forgot-password {
        font-size: 0.9rem;
      }

      .login-button {
        padding: 1.1rem;
        font-size: 1rem;
        margin-top: 0.8rem;
      }

      .divider {
        margin: 2rem 0;
      }

      .signup-link {
        margin-top: 1.5rem;
        font-size: 0.95rem;
      }

      .signup-link a {
        padding: 0.5rem 1rem;
      }
    }
  </style>
</head>
<body dir="rtl">
  <div class="login-container">
        <!-- Header -->
        <div class="login-header">
        <div class="login-logo">🎓</div>
            <h1>تسجيل الدخول</h1>
            <p>أهلًا بعودتك إلى منصتنا التعليمية</p>
        </div>

        <!-- Alerts -->
        @if ($errors->any())
            <div class="alert-error">
                <strong>خطأ:</strong> {{ $errors->first() }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert-success">
                ✓ {{ session('success') }}
            </div>
        @endif

        <!-- Login Form -->
        <form action="{{ route('login') }}" method="POST" class="form-wrapper" novalidate>
            @csrf

            <!-- Email Field -->
            <div class="form-group @error('email') has-error @enderror">
                <label for="email">البريد الإلكتروني</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="أدخل بريدك الإلكتروني"
                    value="{{ old('email') }}"
                    required
                    autofocus
                >
                @error('email')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password Field -->
            <div class="form-group @error('password') has-error @enderror">
                <label for="password">كلمة المرور</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="أدخل كلمة المرور"
                    required
                >
                @error('password')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <!-- Remember & Forgot -->
            <div class="remember-forgot">
                <label class="remember-checkbox">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span>تذكرني</span>
                </label>
                <a href="{{ route('password.request') }}" class="forgot-password">
                    <i class="ri-question-line"></i> نسيت كلمة المرور؟
                </a>
            </div>

            <!-- Login Button -->
            <button type="submit" class="login-button">
                <i class="ri-login-box-line"></i> تسجيل الدخول
            </button>
        </form>

        <!-- Divider -->
        <div class="divider">
            <span class="divider-text">ليس لديك حساب؟</span>
        </div>

        <!-- Signup Link -->
        <div class="signup-link">
            <a href="{{ route('register') }}">
                ← إنشاء حساب جديد الآن
            </a>
        </div>
    </div>

    @include('components.account-theme-foot')
</body>
</html>



