<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  @include('components.account-theme-head')
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>نسيت كلمة المرور | جمعية إجلال</title>
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

    /* ===== FORGOT PASSWORD CONTAINER ===== */
    .forgot-container {
      width: 100%;
      max-width: 860px;
      background: linear-gradient(145deg, var(--theme-surface) 0%, var(--theme-surface-2) 100%);
      border: 2px solid var(--border-color);
      border-radius: 24px;
      padding: 3rem 3.2rem;
      box-shadow: 0 50px 120px rgba(0, 0, 0, 0.7);
      animation: slideInLeft 0.7s ease-out;
      backdrop-filter: blur(10px);
    }

    .forgot-header {
      text-align: center;
      margin-bottom: 3.5rem;
      padding-bottom: 2.5rem;
      border-bottom: 2px solid var(--border-color);
      animation: slideUp 0.7s ease-out 0.1s both;
    }

    .forgot-icon {
      width: 140px;
      height: 140px;
      background: linear-gradient(135deg, var(--gold) 0%, var(--dark-gold) 100%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 5rem;
      margin: 0 auto 2rem;
      box-shadow: 0 25px 60px rgba(198, 166, 117, 0.3);
      border: 4px solid rgba(198, 166, 117, 0.3);
    }

    .forgot-header h1 {
      font-size: 2.3rem;
      color: var(--theme-text);
      margin-bottom: 1rem;
      font-weight: 800;
      letter-spacing: 0.8px;
    }

    .forgot-header p {
      color: rgba(255, 255, 255, 0.8);
      font-size: 1.05rem;
      margin: 0;
      font-weight: 500;
      letter-spacing: 0.3px;
      line-height: 1.6;
    }

    /* ===== INFO BOX ===== */
    .info-box {
      background: var(--theme-gold-soft);
      border: 2px solid rgba(198, 166, 117, 0.3);
      border-radius: 14px;
      padding: 1.8rem;
      margin-bottom: 2.5rem;
      display: flex;
      align-items: flex-start;
      gap: 1.2rem;
      animation: slideUp 0.7s ease-out 0.15s both;
    }

    .info-box i {
      color: var(--gold);
      font-size: 1.5rem;
      flex-shrink: 0;
      margin-top: 0.2rem;
    }

    .info-box-content {
      flex: 1;
    }

    .info-box-content p {
      margin: 0;
      color: rgba(255, 255, 255, 0.85);
      font-size: 0.95rem;
      line-height: 1.6;
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

    .form-group:nth-child(1) { animation-delay: 0.2s; }

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
      color: var(--gold);
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
      color: var(--theme-text);
      transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
      backdrop-filter: blur(10px);
    }

    .form-control::placeholder {
      color: rgba(255, 255, 255, 0.5);
    }

    .form-control:focus {
      outline: none;
      border-color: var(--gold);
      background: var(--theme-gold-soft);
      box-shadow: 0 0 0 3px rgba(198, 166, 117, 0.2);
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

    /* ===== BUTTONS ===== */
    .button-group {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1.5rem;
      margin-top: 3rem;
      animation: slideUp 0.7s ease-out 0.3s both;
    }

    .btn-send {
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
      letter-spacing: 0.5px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 1rem;
      position: relative;
      overflow: hidden;
      box-shadow: 0 15px 40px rgba(198, 166, 117, 0.3);
    }

    .btn-send:hover:not(:disabled) {
      transform: translateY(-3px);
      box-shadow: 0 22px 55px rgba(153, 119, 34, 0.5);
    }

    .btn-send:disabled {
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
    }

    .btn-back:hover {
      background: var(--border-color);
      border-color: var(--gold);
      color: var(--gold);
      transform: translateY(-2px);
    }

    /* ===== LIGHT THEME CONTRAST FIX ===== */
    body.theme-light .form-description,
    body[data-theme="light"] .form-description,
    body.theme-light .form-group label,
    body[data-theme="light"] .form-group label,
    body.theme-light .help-text,
    body[data-theme="light"] .help-text {
      color: var(--theme-text-soft);
    }

    body.theme-light .form-control::placeholder,
    body[data-theme="light"] .form-control::placeholder {
      color: rgba(34, 43, 58, 0.55);
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 600px) {
      .forgot-container {
        padding: 2.5rem 1.8rem;
        border-radius: 18px;
      }

      .forgot-header {
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
      }

      .forgot-icon {
        width: 95px;
        height: 95px;
        font-size: 3.5rem;
        margin-bottom: 1.5rem;
      }

      .forgot-header h1 {
        font-size: 1.6rem;
        margin-bottom: 0.6rem;
      }

      .forgot-header p {
        font-size: 0.9rem;
      }

      .info-box {
        padding: 1.2rem;
        margin-bottom: 1.8rem;
        gap: 1rem;
      }

      .info-box i {
        font-size: 1.3rem;
      }

      .info-box-content p {
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

      .button-group {
        grid-template-columns: 1fr;
        gap: 0.8rem;
        margin-top: 2rem;
      }

      .btn-send,
      .btn-back {
        padding: 0.95rem;
        font-size: 0.95rem;
        border-radius: 12px;
      }

      .info-box {
        padding: 1.5rem;
      }
    }
  </style>
</head>
<body dir="rtl">
  <div class="forgot-container">
    <!-- Header -->
    <div class="forgot-header">
      <div class="forgot-icon">🔑</div>
      <h1>استعادة كلمة المرور</h1>
      <p>أدخل بريدك الإلكتروني وسنرسل لك رابطًا لإعادة تعيين كلمة المرور</p>
    </div>

    <!-- Info Box -->
    <div class="info-box">
      <i class="ri-information-line"></i>
      <div class="info-box-content">
        <p>سنرسل إليك رابطًا آمنًا لإنشاء كلمة مرور جديدة. الرابط صالح لمدة 60 دقيقة فقط.</p>
      </div>
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

    @if (session('status'))
      <div class="alert alert-success">
        <i class="ri-check-line"></i>
        <div>{{ session('status') }}</div>
      </div>
    @endif

    <!-- Forgot Form -->
    <form action="{{ route('password.email') }}" method="POST" novalidate>
      @csrf

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
            placeholder="أدخل بريدك الإلكتروني المسجل"
            value="{{ old('email') }}"
            required
            autofocus
        >
        @error('email')
          <div class="error-text">
            <i class="ri-alert-line"></i> {{ $message }}
          </div>
        @enderror
      </div>

      <!-- Buttons -->
      <div class="button-group">
        <button type="submit" class="btn-send">
          <i class="ri-mail-send-line"></i> إرسال الرابط
        </button>
        <a href="{{ route('login') }}" class="btn-back" style="text-decoration: none;">
          <i class="ri-arrow-right-line"></i> رجوع للدخول
        </a>
      </div>
    </form>
  </div>

  @include('components.account-theme-foot')
</body>
</html>



