<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>شهادة - {{ $student->name }}</title>
    @include('components.account-theme-head')
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        @page { margin: 0; size: A4 landscape; }
        * { box-sizing: border-box; }
        body {
            margin: 0; padding: 0; direction: rtl;
            font-family: 'Tajawal', sans-serif;
            background: var(--theme-page-bg);
            display: flex; flex-direction: column; align-items: center;
            min-height: 100vh;
        }
        .actions-bar {
            width: 100%; padding: 16px 24px;
            background: var(--theme-surface);
            backdrop-filter: blur(24px);
            display: flex; justify-content: center; gap: 12px;
            position: sticky; top: 0; z-index: 1000;
            border-bottom: 1px solid var(--theme-border);
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 20px; border-radius: 12px; font-weight: 700;
            font-size: 14px; text-decoration: none; cursor: pointer;
            border: none; transition: 0.3s; font-family: 'Tajawal', sans-serif;
        }
        .btn-primary { background: var(--theme-gold); color: #000; }
        .btn-primary:hover { background: var(--theme-gold-dark); transform: translateY(-2px); }
        .btn-outline {
            background: var(--theme-surface-2); color: var(--text-secondary);
            border: 1px solid var(--theme-border);
        }
        .btn-outline:hover { background: var(--theme-gold-soft); color: var(--text-primary); }
        .btn-send { background: var(--theme-success); color: #000; }
        .btn-send:hover { opacity: 0.85; transform: translateY(-2px); }
        .flash {
            width: 100%; max-width: 800px; margin: 12px auto; padding: 12px 20px;
            border-radius: 12px; font-size: 13px; font-weight: 600;
        }
        .flash-success { background: var(--theme-success-soft, rgba(52,199,89,0.12)); color: var(--theme-success); border: 1px solid var(--theme-success-border, rgba(52,199,89,0.2)); }
        .flash-error { background: rgba(255,59,48,0.12); color: var(--theme-danger); border: 1px solid rgba(255,59,48,0.2); }

        .cert-wrap { padding: 20px; display: flex; justify-content: center; width: 100%; }
        .cert-paper {
            width: 297mm; height: 210mm;
            background-image: url("{{ $backgroundImage }}");
            background-size: 100% 100%;
            background-color: white;
            position: relative;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            display: flex; flex-direction: column;
            padding: 50pt 70pt;
        }
        .main-title { font-size: 48pt; font-weight: 800; text-align: center; margin: 10pt 0; color: #1e3a8a; }
        .statement { font-size: 18pt; color: #475569; text-align: center; margin: 15pt 0; }
        .name { font-size: 20pt; font-weight: bold; color: #111827; }
        .course-text { font-size: 19pt; line-height: 1.6; color: #1e293b; text-align: center; margin-bottom: 30pt; }
        .course-name { font-weight: bold; font-size: 24pt; }
        .footer-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .footer-cell { width: 33%; text-align: center; vertical-align: middle; }
        .sig-line { border-top: 1.5pt solid #1e293b; width: 160pt; margin: 0 auto 8pt; }
        .label-text { font-size: 12pt; color: #64748b; margin-bottom: 5pt; }
        .name-text { font-weight: bold; font-size: 16pt; color: #1e293b; }
        .seal-box {
            width: 85pt; height: 85pt;
            border: 2pt double var(--theme-gold); border-radius: 50%;
            line-height: 85pt; text-align: center;
            color: var(--theme-gold); font-size: 11pt; font-weight: bold;
            margin: 0 auto; transform: rotate(-15deg);
        }
        .inline-form { display: inline-block; }
        @media screen and (max-width: 1024px) {
            .cert-paper { width: 100%; height: auto; aspect-ratio: 297/210; padding: 5% 7%; }
            .main-title { font-size: 6vw; }
        }
        @media print { .actions-bar { display: none; } .cert-paper { box-shadow: none; } }
    </style>
</head>
<body>
    <div class="actions-bar">
        <a href="{{ route('teacher.certificates.download', [$templateNum, $student]) }}" class="btn btn-primary">
            <i class="ri-download-line"></i> تحميل PDF
        </a>
        <a href="{{ route('teacher.certificates.gallery', $student) }}" class="btn btn-outline">
            <i class="ri-arrow-right-line"></i> رجوع
        </a>
        <form action="{{ route('teacher.certificates.email', [$student, $templateNum]) }}" method="POST" class="inline-form">
            @csrf
            <button type="submit" class="btn btn-send" id="sendCertBtn">
                <i class="ri-mail-send-line"></i> إرسال للبريد
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="flash flash-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="flash flash-error">{{ session('error') }}</div>
    @endif

    <div class="cert-wrap">
        <div class="cert-paper">
            <div class="main-title">شهادة إجتياز</div>
            <div class="statement">
                يشـهد معهد {{ auth()->user()->name }} بأن المتدرب/ـة : <span class="name">{{ $student->name }}</span>
            </div>
            <div class="course-text">
                قد اجتاز بنجاح الدورة التدريبية بعنوان :
                <span class="course-name">"{{ $student->course }}"</span>
                التي أقيمت في مركزنا التدريبي<br><br>
                والمنعقدة بتاريخ {{ $student->course_date->format('Y-m-d') }} م وقد حصل على تقدير عام : {{ $student->degree }}
            </div>
            <div class="course-text">بناءً عليه، مُنحت له هذه الشهادة تقديراً لجهوده وتمنياتنا له بمزيد من التوفيق والنجاح.</div>
            <div style="text-align:center;font-size:14pt;color:#475569;margin-bottom:10pt;">
                صدرت في: {{ now()->format('Y-m-d') }}
            </div>
            <table class="footer-table">
                <tr>
                    <td class="footer-cell">
                        <div class="sig-line"></div>
                        <div class="label-text">الجهة</div>
                        <div class="name-text">{{ auth()->user()->name }}</div>
                    </td>
                    <td class="footer-cell">
                        <div class="seal-box">الختم الرسمي</div>
                    </td>
                    <td class="footer-cell">
                        <div class="sig-line"></div>
                        <div class="label-text">اعتماد التوقيع</div>
                        <div class="name-text">رقمي</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var f = document.getElementById('sendCertBtn')?.closest('form');
            if (f) {
                f.addEventListener('submit', function (e) {
                    if (!confirm('إرسال الشهادة إلى {{ $student->email }}؟')) {
                        e.preventDefault();
                    }
                });
            }
        });
    </script>
    @include('components.account-theme-foot')
</body>
</html>
