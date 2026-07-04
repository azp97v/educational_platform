<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  @include('components.account-theme-head')
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>إنشاء حساب جديد | جمعية إجلال</title>
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

    /* ===== REGISTER CONTAINER ===== */
    .register-box {
      width: 100%;
      max-width: 600px;
      background: linear-gradient(145deg, var(--theme-surface) 0%, var(--theme-surface-2) 100%);
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
      width: 120px;
      height: 120px;
      background: linear-gradient(135deg, var(--gold) 0%, var(--dark-gold) 100%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 4rem;
      margin: 0 auto 1.5rem;
      box-shadow: 0 20px 50px rgba(198, 166, 117, 0.35);
      border: 3px solid rgba(198, 166, 117, 0.3);
    }

    h1 {
      color: var(--theme-text);
      font-size: 2rem;
      margin: 0 0 0.7rem 0;
      font-weight: 800;
      letter-spacing: 0.5px;
    }

    .subtitle {
      color: var(--text-secondary);
      font-size: 0.95rem;
      margin: 0;
      font-weight: 500;
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

    .alert-danger {
      background: rgba(211, 47, 47, 0.12);
      border-color: rgba(211, 47, 47, 0.35);
      color: var(--theme-danger-soft, #ff6b6b);
    }

    .alert-danger i {
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

    /* ===== FORM CONTAINER ===== */
    .form-wrapper {
      display: flex;
      flex-direction: column;
      gap: 1.8rem;
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1.5rem;
      animation: slideUp 0.7s ease-out both;
    }

    .form-row.full {
      grid-template-columns: 1fr;
    }

    .form-row:nth-child(1) { animation-delay: 0.2s; }
    .form-row:nth-child(2) { animation-delay: 0.3s; }
    .form-row:nth-child(3) { animation-delay: 0.4s; }

    .form-group {
      display: flex;
      flex-direction: column;
    }

    .form-group label {
      font-weight: 700;
      color: var(--text-primary);
      margin-bottom: 0.8rem;
      font-size: 0.95rem;
      letter-spacing: 0.3px;
      display: flex;
      align-items: center;
      gap: 0.6rem;
    }

    .form-group label i {
      color: var(--gold);
      font-size: 1.1rem;
    }

    input, select {
      padding: 1rem 1.3rem;
      background: color-mix(in srgb, var(--theme-surface) 94%, transparent);
      border: 1.5px solid var(--border-color);
      border-radius: 12px;
      font-size: 0.95rem;
      font-family: 'Cairo', sans-serif;
      color: var(--theme-text);
      transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
      backdrop-filter: blur(10px);
    }

    input::placeholder, select option {
      color: var(--text-muted);
    }

    input:focus, select:focus {
      outline: none;
      border-color: var(--gold);
      background: color-mix(in srgb, var(--gold) 10%, var(--theme-surface));
      box-shadow: 0 0 0 3px rgba(198, 166, 117, 0.2);
    }

    input.bad {
      border-color: var(--theme-danger);
      background: rgba(255, 59, 48, 0.08);
    }

    input.bad:focus {
      box-shadow: 0 0 0 3px rgba(255, 59, 48, 0.2);
    }

    .msg {
      color: var(--theme-danger-soft, #ff6b6b);
      font-size: 0.85rem;
      margin-top: 0.6rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    /* ===== BUTTONS ===== */
    .button-group {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      margin-top: 2rem;
    }

    .register-button {
      padding: 1.2rem;
      background: linear-gradient(135deg, var(--gold) 0%, var(--dark-gold) 100%);
      color: var(--theme-text);
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
      box-shadow: 0 12px 30px rgba(198, 166, 117, 0.25);
    }

    .register-button:hover:not(:disabled) {
      transform: translateY(-3px);
      box-shadow: 0 18px 45px rgba(198, 166, 117, 0.4);
    }

    .register-button:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }

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
      color: var(--text-secondary);
      font-size: 0.9rem;
      font-weight: 700;
      letter-spacing: 0.4px;
    }

    .signup-link {
      text-align: center;
      margin-top: 0.6rem;
      font-size: 1rem;
      color: var(--text-secondary);
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
    body.theme-light .subtitle,
    body[data-theme="light"] .subtitle,
    body.theme-light .form-group label,
    body[data-theme="light"] .form-group label,
    body.theme-light .signup-link,
    body[data-theme="light"] .signup-link,
    body.theme-light .divider-text,
    body[data-theme="light"] .divider-text {
      color: var(--theme-text-soft);
    }

    body.theme-light input::placeholder,
    body[data-theme="light"] input::placeholder {
      color: rgba(34, 43, 58, 0.55);
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 700px) {
      .register-box {
        padding: 2.5rem 1.8rem;
        border-radius: 16px;
      }

      .header {
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
      }

      .logo {
        width: 100px;
        height: 100px;
        font-size: 3.5rem;
      }

      h1 {
        font-size: 1.6rem;
        margin-bottom: 0.5rem;
      }

      .form-row {
        grid-template-columns: 1fr;
        gap: 1.2rem;
      }
    }
  </style>
</head>
<body dir="rtl">
  <div class="register-box">
    <div class="header">
      <div class="logo">🎓</div>
      <h1>إنشاء حساب جديد</h1>
      <p class="subtitle">انضم إلى آلاف الطلاب في مسيرتك التعليمية</p>
    </div>

    @if ($errors->any())
      <div class="alert alert-danger">
        <i class="ri-error-warning-line"></i>
        <div>
          <strong>خطأ:</strong> {{ $errors->first() }}
        </div>
      </div>
    @endif

    @if (session('error'))
      <div class="alert alert-danger">
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

    <form action="{{ route('register') }}" method="POST" class="form-wrapper">
      @csrf

      <div class="form-row full">
        <div class="form-group">
          <label for="name"><i class="ri-user-line"></i> الاسم الكامل</label>
          <input type="text" id="name" name="name" value="{{ old('name') }}" @error('name') class="bad" @enderror placeholder="أدخل اسمك الكامل" required autofocus>
          @error('name') <div class="msg"><i class="ri-alert-line"></i>{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="form-row full">
        <div class="form-group">
          <label for="username"><i class="ri-at-line"></i> اسم المستخدم</label>
          <input type="text" id="username" name="username" value="{{ old('username') }}" @error('username') class="bad" @enderror placeholder="مثال: ahmed_2024" required minlength="3" maxlength="50" pattern="[a-zA-Z0-9_.]+">
          <small style="color:var(--text-muted);font-size:11px;display:block;margin-top:4px;">يُستخدم للوصول إليك في تطبيق المراسلة. يجب أن يكون فريداً.</small>
          @error('username') <div class="msg"><i class="ri-alert-line"></i>{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="form-row full">
        <div class="form-group">
          <label for="email"><i class="ri-mail-line"></i> البريد الإلكتروني</label>
          <input type="email" id="email" name="email" value="{{ old('email') }}" @error('email') class="bad" @enderror placeholder="بريدك الإلكتروني" required>
          @error('email') <div class="msg"><i class="ri-alert-line"></i>{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="password"><i class="ri-lock-line"></i> كلمة المرور</label>
          <input type="password" id="password" name="password" @error('password') class="bad" @enderror placeholder="كلمة مرور قوية" minlength="8" required>
          @error('password') <div class="msg"><i class="ri-alert-line"></i>{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
          <label for="password_confirmation"><i class="ri-lock-check-line"></i> تأكيد كلمة المرور</label>
          <input type="password" id="password_confirmation" name="password_confirmation" @error('password_confirmation') class="bad" @enderror placeholder="أعد الإدخال" minlength="8" required>
          @error('password_confirmation') <div class="msg"><i class="ri-alert-line"></i>{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="button-group">
        <button type="submit" class="register-button"><i class="ri-user-add-line"></i> إنشاء الحساب</button>
      </div>
    </form>

    <div class="divider">
      <span class="divider-text">لديك حساب بالفعل؟</span>
    </div>

    <div class="signup-link">
      <a href="{{ route('login') }}">
        <i class="ri-login-box-line"></i> تسجيل الدخول
      </a>
    </div>
  </div>

  @include('components.account-theme-foot')
</body>
</html>


