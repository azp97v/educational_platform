# دليل منصة إجلال — التطبيق

## المحتويات
1. [نظام الأدوار والصلاحيات](#نظام-الأدوار-والصلاحيات)
2. [نظام المحادثات والمراسلة](#نظام-المحادثات-والمراسلة)
3. [نظام الشهادات](#نظام-الشهادات)
4. [نظام المكالمات](#نظام-المكالمات)
5. [نظام الإشعارات](#نظام-الإشعارات)
6. [لوحة التحكم](#لوحة-التحكم)
7. [الأمان](#الأمان)
8. [الأداء](#الأداء)

---

## نظام الأدوار والصلاحيات

### Admin
- `/admin/settings` — إعدادات المنصة
- `/admin/users` — إدارة المستخدمين
- `/admin/analytics` — التقارير والإحصائيات
- Middleware: `auth`, `role:admin`

### Teacher
- `/teacher/courses` — إنشاء وإدارة المسارات
- `/teacher/lessons` — دروس فيديو مع timestamps للـ Smart Rewind
- `/teacher/exams` — اختبارات مع بنك أسئلة
- `/teacher/certificates/*` — 7 صفحات لإدارة الشهادات
- `/teacher/students` — متابعة الطلاب
- `/teacher/inquiries` — الاستفسارات

### Student
- `/student/courses` — المسارات المتاحة
- `/student/lessons/{id}` — مشاهدة الدروس مع تتبع التقدم
- `/student/exams` — أداء الاختبارات
- `/student/achievements` — الشهادات والإنجازات
- `/student/points` — النقاط والـ Leaderboard

---

## نظام المحادثات والمراسلة

التطبيق الرئيسي في `resources/views/messaging-app.blade.php` (~12500 سطر).

### الميزات
- **محادثات فردية** مع إشعارات فورية
- **حالات (Stories)** مثل WhatsApp — تختفي بعد 24 ساعة
- **مكالمات صوتية/فيديو** عبر WebRTC
- **الرسائل المحفوظة** — Self-chat
- **حظر المستخدمين** — إخفاء كامل للمحادثة
- **كتم المحادثات** — مؤقت (15min–8h) أو للأبد

### نظام الصوت
- نغمات إشعارات مخصصة لكل مستخدم
- 8 نغمات مدمجة مع معاينة Web Audio API
- رفع نغمات MP3/Ogg/Wav (حد 500KB)
- تحكم في مستوى الصوت لكل مستخدم (0–100%)
- تشذيب تلقائي للنغمات الأطول من 4 ثوانٍ

### ملفات مهمة
- `app/Http/Controllers/MessagingController.php` — 2038 سطر
- `app/Http/Controllers/StatusController.php` — 505 سطر
- `app/Http/Controllers/MessagingSettingsController.php` — 345 سطر
- `app/Http/Controllers/CallController.php` — 324 سطر
- `app/Http/Controllers/Traits/MessagingPrivacyTrait.php` — مشترك للخصوصية
- `public/css/messaging-app.css` — CSS كامل
- `public/js/messaging-app.js` — JavaScript

---

## نظام الشهادات

### أنواع الشهادات
1. **تلقائية** — عند إكمال مسار
2. **مخصصة (Custom Designer)** — المعلم يصمم شهادة ب HTML/CSS
3. **من المعرض (Gallery)** — قوالب جاهزة
4. **مصممة مسبقاً** — للمعلمين مع تخصيص الألوان

### is_issued System
- الشهادات المخصصة تكون `is_issued=false` عند الإنشاء
- تتحول إلى `is_issued=true` فقط عندما يشاهدها المعلم (صاحبها) أول مرة
- الطلاب لا يرون الشهادات غير المصدرة (`is_issued=true`)
- هذا يمنع إرسال إشعارات فارغة للطلاب

### الملفات
- `app/Http/Controllers/Teacher/CertificateDesignerController.php`
- `app/Models/Certificates/CustomTemplate.php`
- `resources/views/teacher/certificates/*` — 8 صفحات

---

## نظام المكالمات

- WebRTC audio/video calls
- سجل مكالمات مع `messaging_calls` في localStorage
- tabs: الكل / الفائتة
- مكالمات جماعية (عدة مشاركين)
- call cards داخل المحادثة مع direction/status/duration

---

## نظام الإشعارات

- إشعارات قاعدة بيانات عبر `app_notifications` table
- Bell dropdown في التوب بار
- Bell toast للإشعارات الحية (تظهر 5 ثوانٍ)
- Polling كل 15 ثانية
- إشعارات للشهادات، الاستفسارات، التسجيل، المحادثات

---

## لوحة التحكم

- `/admin/settings` — إعدادات المنصة (اسم، وصف، شعار، لينكات تواصل)
- `/admin/dashboard` — إحصائيات المستخدمين والمسارات
- `/admin/users` — CRUD المستخدمين مع ربط معلم-طالب
- `/admin/analytics` — تحليلات متقدمة

---

## الأمان

| الميزة | الملف |
|--------|-------|
| CSP Nonces | `app/Http/Middleware/SecurityHeaders.php` |
| Rate Limiting | `app/Http/Middleware/ThrottleRequests.php` (Laravel) |
| Role Middleware | `app/Http/Middleware/CheckRole.php` |
| 2FA (OTP) | `app/Http/Controllers/Auth/RegisterController.php` |
| Error Masking | `bootstrap/app.php` — `$exceptions->dontReport()` |
| Error Pages | `resources/views/errors/{403,404,419,500,503}.php` |
| Session | Encrypted, HttpOnly, SameSite=Lax, Secure |
| Password | Bcrypt 12 rounds + Pepper |

---

## الأداء

- **Redis 8.8** — Cache، Session، Queue
- **25 Performance Indexes** — عبر 14 جدول
- **N+1 Query Fixes** — `StudentController::index()`، `academy()`، `exams()`
- **K6 Load Tests** — Smoke, Scenarios, Stress scenarios
- **Composer scripts**:
  ```bash
  php composer test              # Run feature tests
  php composer loadtest:smoke    # K6 smoke test
  php composer loadtest:stress   # K6 stress test
  php composer health            # Health check endpoint
  php composer backup            # DB + uploads backup
  php composer deploy            # Zero-downtime deploy
  php composer monitor           # Slack monitoring
  ```
