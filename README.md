# منصة إجلال التعليمية الذكية

> منصة تعليمية متكاملة بميزات Smart Learning، Gamification، تواصل فوري، وشهادات مخصصة.

![Laravel](https://img.shields.io/badge/Laravel-12.56.0-red) ![PHP](https://img.shields.io/badge/PHP-8.2.12-blue) ![MySQL](https://img.shields.io/badge/MySQL-5.7+-green) ![Redis](https://img.shields.io/badge/Redis-8.8-red) ![Tests](https://img.shields.io/badge/Tests-21_Feature_Passing-green)

---

## المميزات الرئيسية

| الميزة | الوصف |
|--------|-------|
| **🎬 Smart Rewind** | إعادة توجيه الطالب للأجزاء الخاطئة تلقائياً |
| **🏆 Gamification** | نقاط، Streaks، Leaderboard، Achievements |
| **📜 Certificates** | محرك شهادات مخصص مع QR Code، PDF، is_issued |
| **💬 Messaging** | محادثات خاصة، Stories، Calls (WebRTC)، إشعارات فورية |
| **📊 Analytics** | تحليلات متقدمة للأسئلة، الطلاب، المسارات |
| **🔐 Security** | CSP Nonces، 2FA، Rate Limiting، RBAC، Error Masking |
| **🎨 Themes** | Dark/Light mode كامل باستخدام CSS Variables |
| **⚡ Performance** | Redis caching، 25 Performance Indexes، N+1 Queries Fixed |

## متطلبات التشغيل

- PHP 8.2+
- MySQL 5.7+
- Redis 7+ (اختياري — للتخزين المؤقت)
- Composer 2.x
- Node.js 18+ (لبناء الأصول)

## التشغيل السريع

```bash
git clone <repo-url>
cd educational
cp .env.example .env
composer install
npm install && npm run build
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## النظام

### الأدوار
- **Admin** — إدارة المستخدمين، إعدادات المنصة، تقارير
- **Teacher** — مسارات، دروس، اختبارات، شهادات، متابعة طلاب
- **Student** — مشاهدة دروس، اختبارات، نقاط، شهادات

### الإحصائيات
- Controllers: 20+ | Routes: 700+ | Tests: 21 Feature
- Messaging: WebRTC calls, Stories, E2E Encryption
- Certificates: 4 types (automatic, custom, gallery, designed)
- Performance: Redis + 25 indexes + N+1 fixed
- Security: CSP, 2FA, Rate Limiting, RBAC, Error Masking

## الاختبارات

```bash
php artisan test --testsuite=Feature
php composer loadtest:smoke     # K6 smoke test
php composer health             # System health check
```

## النشر

```bash
php composer backup             # DB dump + uploads zip
php composer deploy             # Zero-downtime deployment
php composer monitor            # Health monitoring with Slack alerts
```

## هيكل المشروع

```
app/
├── Http/Controllers/          # 20+ Controllers (Messaging, Teacher, Student, Admin, etc.)
│   ├── Teacher/               # CertificateDesignerController
│   └── Traits/                # MessagingPrivacyTrait
├── Models/                    # User, Message, Course, Lesson, Exam, Certificate, etc.
├── Http/Middleware/           # SecurityHeaders, CheckRole
├── Mail/                      # SendOtpCode
├── Notifications/             # AppNotification
database/migrations/           # 25+ Migrations
resources/views/               # 80+ Blade Templates
routes/web.php                 # 700+ Routes
public/css/                    # account-theme-unified.css, messaging-app.css
tests/                         # 5 Feature test files, K6 load tests
scripts/                       # deploy.ps1, backup.ps1, monitor.ps1
.github/workflows/             # tests.yml (CI)
```

## الأمان

| الميزة | الوصف |
|--------|-------|
| CSP Headers | Nonce لكل طلب، `'unsafe-inline'` ممنوع في script-src |
| Rate Limiting | Throttle على login، register، search، messaging |
| Role-Based Access | 3 أدوار مع Middleware مخصص |
| 2FA | رمز OTP عبر البريد الإلكتروني |
| Error Masking | رسائل خطأ عامة بدون تسريب للمسارات |
| Custom Error Pages | 5 صفحات خطأ عربية مع Dark/Light mode |

## الأداء

- Redis 8.8 للتخزين المؤقت للجلسات والـ Cache
- 25 Performance Indexes عبر 14 جدول
- N+1 Query fixes في StudentController
- K6 load testing (smoke, scenarios, stress)

## الترخيص

هذا المشروع مطوّر بواسطة **جمعية إجلال** — جميع الحقوق محفوظة.
