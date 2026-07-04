<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
  <meta charset="UTF-8">`r`n  @include('components.account-theme-head')
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>مرحباً بك | جمعية إجلال</title>
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

    @keyframes bounceIn {
      0% {
        opacity: 0;
        transform: scale(0);
      }
      50% {
        opacity: 1;
      }
      70% {
        transform: scale(1.15);
      }
      100% {
        transform: scale(1);
      }
    }

    @keyframes checkmark {
      0% {
        transform: scale(0);
      }
      70% {
        transform: scale(1.2);
      }
      100% {
        transform: scale(1);
      }
    }

    /* ===== WELCOME CONTAINER ===== */
    .container {
      width: 100%;
      max-width: 600px;
      background: linear-gradient(135deg, rgba(10, 14, 39, 0.95) 0%, rgba(22, 33, 62, 0.95) 100%);
      border: 1.5px solid var(--border-color);
      border-radius: 20px;
      padding: 3.5rem 3rem;
      box-shadow: 0 40px 100px rgba(0, 0, 0, 0.6);
      animation: slideInLeft 0.7s ease-out;
      backdrop-filter: blur(10px);
      text-align: center;
    }

    .success-animation {
      width: 140px;
      height: 140px;
      margin: 0 auto 2rem;
      background: linear-gradient(135deg, var(--gold) 0%, var(--dark-gold) 100%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 70px;
      box-shadow: 0 20px 50px rgba(198, 117, 46, 0.35);
      animation: bounceIn 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55);
      border: 3px solid rgba(198, 117, 46, 0.3);
    }

    .checkmark {
      animation: checkmark 0.8s ease-in-out;
    }

    h1 {
      font-size: 2.2rem;
      color: white;
      margin: 0 0 0.7rem 0;
      font-weight: 800;
      letter-spacing: 0.5px;
      animation: slideUp 0.7s ease-out 0.2s both;
    }

    .subtitle {
      font-size: 1.1rem;
      color: var(--gold);
      margin-bottom: 2rem;
      font-weight: 600;
      letter-spacing: 0.3px;
      animation: slideUp 0.7s ease-out 0.3s both;
    }

    .message {
      color: rgba(255, 255, 255, 0.85);
      font-size: 0.95rem;
      line-height: 1.8;
      margin-bottom: 2rem;
      animation: slideUp 0.7s ease-out 0.4s both;
      padding: 1.5rem;
      background: rgba(198, 117, 46, 0.08);
      border: 1.5px solid rgba(198, 117, 46, 0.25);
      border-radius: 12px;
      backdrop-filter: blur(10px);
    }

    /* ===== USER INFO ===== */
    .user-info {
      background: linear-gradient(135deg, rgba(198, 117, 46, 0.15) 0%, rgba(165, 122, 40, 0.08) 100%);
      border: 1.5px solid rgba(198, 117, 46, 0.35);
      border-radius: 12px;
      padding: 1.8rem;
      margin: 2rem 0;
      text-align: right;
      animation: slideUp 0.7s ease-out 0.5s both;
      backdrop-filter: blur(10px);
    }

    .user-info p {
      margin: 0.8rem 0;
      color: rgba(255, 255, 255, 0.85);
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      gap: 0.8rem;
    }

    .user-info strong {
      color: var(--gold);
      font-weight: 700;
    }

    .user-info i {
      color: var(--gold);
      font-size: 1.1rem;
    }

    .verified-badge {
      font-size: 0.85rem;
      margin-top: 1.2rem;
      padding-top: 1.2rem;
      border-top: 1px solid rgba(198, 117, 46, 0.3);
      color: var(--success);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.6rem;
      font-weight: 600;
      letter-spacing: 0.3px;
    }

    .verified-badge i {
      color: var(--success);
    }

    /* ===== FEATURES LIST ===== */
    .features-list {
      text-align: right;
      margin: 2rem 0;
      background: var(--card-bg);
      border: 1.5px solid var(--border-color);
      padding: 1.8rem;
      border-radius: 12px;
      animation: slideUp 0.7s ease-out 0.55s both;
      backdrop-filter: blur(10px);
    }

    .features-list h3 {
      color: white;
      font-size: 1.1rem;
      margin-bottom: 1.3rem;
      font-weight: 700;
      letter-spacing: 0.3px;
      display: flex;
      align-items: center;
      gap: 0.6rem;
    }

    .features-list h3 i {
      color: var(--gold);
      font-size: 1.3rem;
    }

    .features-list ul {
      list-style: none;
      padding: 0;
    }

    .features-list li {
      color: rgba(255, 255, 255, 0.8);
      padding: 0.9rem 0;
      font-size: 0.9rem;
      border-bottom: 1px solid rgba(198, 117, 46, 0.15);
      display: flex;
      align-items: center;
      gap: 0.8rem;
    }

    .features-list li:last-child {
      border-bottom: none;
      padding-bottom: 0;
    }

    .features-list li:before {
      content: "✨";
      color: var(--gold);
      font-size: 1.1rem;
      flex-shrink: 0;
    }

    /* ===== BUTTONS ===== */
    .button-group {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      margin: 2rem 0;
      animation: slideUp 0.7s ease-out 0.6s both;
    }

    .btn {
      padding: 1.2rem 1.5rem;
      border: none;
      border-radius: 12px;
      font-size: 0.95rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.8rem;
      letter-spacing: 0.3px;
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--gold) 0%, var(--dark-gold) 100%);
      color: white;
      box-shadow: 0 12px 30px rgba(198, 117, 46, 0.25);
    }

    .btn-primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 18px 45px rgba(198, 117, 46, 0.4);
    }

    .btn-secondary {
      background: rgba(198, 117, 46, 0.15);
      color: var(--gold);
      border: 1.5px solid rgba(198, 117, 46, 0.4);
    }

    .btn-secondary:hover {
      background: var(--gold);
      color: white;
      border-color: var(--gold);
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(198, 117, 46, 0.3);
    }

    /* ===== DIVIDER ===== */
    .divider {
      display: flex;
      align-items: center;
      margin: 2rem 0;
      color: rgba(255, 255, 255, 0.5);
    }

    .divider::before,
    .divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: var(--border-color);
    }

    .divider span {
      padding: 0 1rem;
      font-size: 0.85rem;
      font-weight: 600;
    }

    /* ===== PROFILE TIPS ===== */
    .profile-tips {
      text-align: right;
      font-size: 0.9rem;
      color: rgba(255, 255, 255, 0.75);
      margin-top: 2rem;
      padding: 1.5rem;
      background: rgba(3, 169, 244, 0.08);
      border: 1.5px solid rgba(3, 169, 244, 0.25);
      border-radius: 12px;
      animation: slideUp 0.7s ease-out 0.65s both;
    }

    .profile-tips p {
      margin: 0.8rem 0;
      display: flex;
      align-items: center;
      gap: 0.8rem;
    }

    .profile-tips i {
      color: #64b5f6;
      font-size: 1.1rem;
      flex-shrink: 0;
    }

    .profile-tips a {
      color: #64b5f6;
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      margin-left: 0.5rem;
    }

    .profile-tips a:hover {
      color: rgba(255, 255, 255, 0.95);
      text-decoration: underline;
    }

    /* ===== FOOTER ===== */
    .footer-text {
      font-size: 0.85rem;
      color: rgba(255, 255, 255, 0.6);
      margin-top: 2rem;
      padding-top: 1.5rem;
      border-top: 1px solid var(--border-color);
      animation: slideUp 0.7s ease-out 0.7s both;
    }

    .footer-text a {
      color: var(--gold);
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s ease;
    }

    .footer-text a:hover {
      color: rgba(255, 255, 255, 0.95);
      text-decoration: underline;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 600px) {
      .container {
        padding: 2.5rem 1.8rem;
        border-radius: 16px;
      }

      h1 {
        font-size: 1.8rem;
      }

      .success-animation {
        width: 120px;
        height: 120px;
        font-size: 60px;
        margin-bottom: 1.5rem;
      }

      .button-group {
        gap: 0.8rem;
      }

      .btn {
        padding: 1rem 1.2rem;
      }

      .features-list li {
        font-size: 0.85rem;
      }
    }
  </style>
</head>
<body dir="rtl">
  <div class="container">
    <div class="success-animation">
      <span class="checkmark">✓</span>
    </div>

    <h1>مرحباً بك في إجلال!</h1>
    <p class="subtitle">🎉 تم تفعيل حسابك بنجاح</p>

    <div class="message">
      <p>تهانينا! تم تأكيد حسابك وأنت الآن جزء من عائلة منصة إجلال التعليمية الرائعة.</p>
    </div>

    <div class="user-info">
      <p>
        <i class="ri-user-line"></i>
        <strong>الاسم:</strong> {{ $user->name }}
      </p>
      <p>
        <i class="ri-mail-line"></i>
        <strong>البريد الإلكتروني:</strong> {{ $user->email }}
      </p>
      <div class="verified-badge">
        <i class="ri-verified-badge-fill"></i>
        البريد الإلكتروني مُتحقق من
      </div>
    </div>

    <div class="features-list">
      <h3>
        <i class="ri-lightbulb-flash-line"></i>
        ماذا يمكنك فعله الآن؟
      </h3>
      <ul>
        <li>الوصول الكامل إلى جميع الدورات والدروس التعليمية</li>
        <li>متابعة تقدمك التعليمي والإحصائيات المفصلة</li>
        <li>التفاعل مع محتوى الدروس والأنشطة والاختبارات</li>
        <li>الحصول على شهادات معتمدة عند إكمال الدورات</li>
        <li>التواصل مع معلميك والطلاب الآخرين</li>
        <li>استكشاف موارد تعليمية إضافية وممتعة</li>
      </ul>
    </div>

    <div class="button-group">
      <a href="{{ route('dashboard') }}" class="btn btn-primary">
        <i class="ri-arrow-left-line"></i> انتقل إلى لوحة التحكم
      </a>
      <a href="{{ route('courses.index') }}" class="btn btn-secondary">
        <i class="ri-compass-line"></i> استكشف الدورات
      </a>
    </div>

    <div class="divider">
      <span>المزيد</span>
    </div>

    <div class="profile-tips">
      <p>
        <i class="ri-user-settings-line"></i>
        <strong>نصيحة:</strong> قم بإكمال ملف التعريف الشخصي
      </p>
      <p style="margin-top: 1rem;">
        <i class="ri-arrow-right-line"></i>
        <a href="{{ route('profile.edit') }}">اكمل الملف الشخصي →</a>
      </p>
    </div>

    <div class="footer-text">
      <p>لديك أي استفسارات أو مشاكل؟ 
        <a href="mailto:support@eglal.com">
          <i class="ri-mail-line"></i> تواصل معنا
        </a>
      </p>
      <p style="margin-top: 0.8rem;">© 2024 منصة إجلال التعليمية - جميع الحقوق محفوظة</p>
    </div>
  </div>

  <!-- Theme Manager Script -->
  @include('components.account-theme-foot')
</body>
</html>
</html>

