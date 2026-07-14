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
        .btn-send {
            background: linear-gradient(135deg, var(--theme-gold), var(--theme-gold-dark));
            color: #000;
        }
        .btn-send:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(196,150,58,0.35); }

        /* ── Email Modal ── */
        .email-modal-overlay {
            display: none; position: fixed; inset: 0; z-index: 2000;
            background: rgba(0,0,0,0.65); backdrop-filter: blur(6px);
            align-items: center; justify-content: center; padding: 20px;
        }
        .email-modal-overlay.open { display: flex; }
        .email-modal {
            background: var(--theme-surface);
            border: 1px solid var(--theme-border);
            border-radius: 28px; width: 100%; max-width: 500px;
            box-shadow: 0 40px 100px rgba(0,0,0,0.55);
            animation: emSlideUp 0.24s cubic-bezier(.22,.68,0,1.2);
            overflow: hidden; font-family: 'Tajawal', sans-serif;
        }
        @keyframes emSlideUp { from{opacity:0;transform:translateY(28px) scale(.97)} to{opacity:1;transform:none} }
        .em-header {
            display: flex; align-items: center; gap: 14px;
            padding: 22px 26px 18px; border-bottom: 1px solid var(--theme-border);
        }
        .em-icon {
            width: 44px; height: 44px; border-radius: 14px; flex-shrink: 0;
            background: rgba(198,166,117,0.15); border: 1px solid rgba(198,166,117,0.25);
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; color: var(--theme-gold);
        }
        .em-header h2 { font-size: 18px; font-weight: 800; color: var(--text-primary); margin-bottom: 2px; }
        .em-header p  { font-size: 12px; color: var(--text-secondary); }
        .em-close {
            margin-right: auto; background: none; border: none; cursor: pointer;
            color: var(--text-secondary); font-size: 20px; padding: 4px; border-radius: 8px; transition: .18s;
        }
        .em-close:hover { color: var(--text-primary); background: var(--theme-surface-2); }
        .em-body { padding: 22px 26px; }
        .em-preview {
            display: flex; gap: 14px; align-items: center;
            background: var(--theme-surface-2); border: 1px solid var(--theme-border);
            border-radius: 16px; padding: 14px; margin-bottom: 20px;
        }
        .em-preview img { width: 70px; height: 50px; object-fit: cover; border-radius: 10px; flex-shrink: 0; }
        .em-preview .em-tname { font-size: 14px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px; }
        .em-recipient {
            display: flex; gap: 12px;
            background: rgba(198,166,117,0.06); border: 1px solid rgba(198,166,117,0.2);
            border-radius: 14px; padding: 12px 16px; margin-bottom: 20px; align-items: center;
        }
        .em-avatar {
            width: 38px; height: 38px; border-radius: 50%; flex-shrink: 0;
            background: var(--theme-gold-soft); display: flex; align-items: center;
            justify-content: center; font-size: 18px; color: var(--theme-gold);
        }
        .em-rname { font-size: 14px; font-weight: 700; color: var(--text-primary); margin-bottom: 2px; }
        .em-remail { font-size: 12px; color: var(--text-secondary); direction: ltr; text-align: right; }
        .em-label { font-size: 12px; font-weight: 700; color: var(--text-secondary); margin-bottom: 6px; display: block; }
        .em-textarea {
            width: 100%; padding: 12px 14px; border-radius: 14px;
            background: var(--theme-surface-2); border: 1px solid var(--theme-border);
            color: var(--text-primary); font-family: 'Tajawal', sans-serif; font-size: 13px;
            resize: none; min-height: 84px; line-height: 1.6; transition: border-color .2s;
        }
        .em-textarea:focus { outline: none; border-color: var(--theme-gold); }
        .em-textarea::placeholder { color: var(--text-secondary); opacity: .6; }
        .em-footer {
            display: flex; gap: 10px; justify-content: flex-end;
            padding: 16px 26px 22px; border-top: 1px solid var(--theme-border);
        }
        .btn-confirm-send {
            background: linear-gradient(135deg, var(--theme-gold), var(--theme-gold-dark));
            color: #000; border: none; border-radius: 14px;
            padding: 11px 28px; font-weight: 700; font-size: 14px;
            cursor: pointer; font-family: 'Tajawal', sans-serif;
            display: inline-flex; align-items: center; gap: 8px; transition: .25s;
        }
        .btn-confirm-send:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(196,150,58,0.35); }
        .btn-cancel-em {
            background: var(--theme-surface-2); color: var(--text-secondary);
            border: 1px solid var(--theme-border); border-radius: 14px;
            padding: 11px 22px; font-weight: 600; font-size: 14px;
            cursor: pointer; font-family: 'Tajawal', sans-serif; transition: .2s;
        }
        .btn-cancel-em:hover { color: var(--text-primary); background: var(--theme-surface); }

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
        <button type="button" class="btn btn-send" id="openEmailModal"
                @if(!$student->email) disabled title="لا يوجد بريد إلكتروني لهذا المستفيد" @endif>
            <i class="ri-mail-send-line"></i> إرسال للبريد
        </button>
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
    {{-- Email Confirmation Modal --}}
    <div class="email-modal-overlay" id="emailModal">
        <div class="email-modal">
            <div class="em-header">
                <div class="em-icon"><i class="ri-mail-send-line"></i></div>
                <div>
                    <h2>إرسال الشهادة بالبريد</h2>
                    <p>مراجعة بيانات الإرسال قبل التأكيد</p>
                </div>
                <button class="em-close" id="closeEmailModal" title="إغلاق"><i class="ri-close-line"></i></button>
            </div>
            <div class="em-body">
                {{-- Template preview --}}
                <div class="em-preview">
                    <img src="{{ asset('image/qw'.$templateNum.'.jpeg') }}" alt="القالب {{ $templateNum }}">
                    <div>
                        <div class="em-tname">
                            @php
                                $names = [1=>'القالب الكلاسيكي',2=>'القالب العصري',3=>'القالب الذهبي',4=>'الدرع الرقمي',5=>'القالب الأكاديمي',6=>'الإبداع الهندسي',7=>'الوسام المهني',8=>'الطراز الأكاديمي',9=>'مودرن جرافيك'];
                            @endphp
                            {{ $names[$templateNum] ?? 'القالب '.$templateNum }}
                        </div>
                        <div style="font-size:12px;color:var(--text-secondary);">القالب المختار</div>
                    </div>
                    <i class="ri-award-fill" style="font-size:22px;color:var(--theme-gold);flex-shrink:0;margin-right:auto;"></i>
                </div>
                {{-- Recipient --}}
                <div class="em-recipient">
                    <div class="em-avatar"><i class="ri-user-3-line"></i></div>
                    <div>
                        <div class="em-rname">{{ $student->name }}</div>
                        <div class="em-remail">{{ $student->email ?: 'لم يُحدد بريد إلكتروني' }}</div>
                    </div>
                </div>
                {{-- Personal message --}}
                <label class="em-label" for="emMessage"><i class="ri-chat-1-line"></i> رسالة شخصية (اختيارية)</label>
                <textarea id="emMessage" class="em-textarea" placeholder="أضف رسالة تشجيعية أو تهنئة شخصية للطالب..." maxlength="500"></textarea>
            </div>
            <div class="em-footer">
                <button class="btn-cancel-em" id="cancelEmailModal">إلغاء</button>
                <form id="emailForm" method="POST"
                      action="{{ route('teacher.certificates.email', [$student, $templateNum]) }}"
                      style="display:contents;">
                    @csrf
                    <input type="hidden" name="message" id="emMsgHidden">
                    <button type="submit" class="btn-confirm-send">
                        <i class="ri-send-plane-line"></i> إرسال الشهادة
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const overlay = document.getElementById('emailModal');
            const msgField = document.getElementById('emMessage');
            const msgHidden = document.getElementById('emMsgHidden');
            const form = document.getElementById('emailForm');

            document.getElementById('openEmailModal')?.addEventListener('click', function () {
                msgField.value = '';
                overlay.classList.add('open');
                setTimeout(() => msgField.focus(), 280);
            });
            function close() { overlay.classList.remove('open'); }
            document.getElementById('closeEmailModal').addEventListener('click', close);
            document.getElementById('cancelEmailModal').addEventListener('click', close);
            overlay.addEventListener('click', e => { if (e.target === overlay) close(); });
            document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });
            form.addEventListener('submit', () => { msgHidden.value = msgField.value.trim(); });
        });
    </script>
    @include('components.account-theme-foot')
</body>
</html>
