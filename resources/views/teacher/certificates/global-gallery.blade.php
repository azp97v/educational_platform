<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>معرض قوالب الشهادات</title>
    @include('components.account-theme-head')
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Tajawal', sans-serif;
            background: radial-gradient(circle at top left, rgba(198,166,117,0.18), transparent 22%),
                        linear-gradient(180deg, var(--theme-page-bg) 0%, var(--theme-surface) 40%, var(--theme-surface-2) 100%);
            color: var(--text-primary);
            min-height: 100vh;
            padding: 30px;
        }
        .container { max-width: 1200px; margin: 0 auto; }

        .top-bar {
            display: flex; justify-content: flex-end; align-items: center; margin-bottom: 20px;
        }
        .back-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 20px; border-radius: 12px;
            background: var(--theme-surface); border: 1px solid var(--theme-border);
            color: var(--text-secondary); font-size: 13px; font-weight: 600;
            text-decoration: none; transition: 0.25s;
        }
        .back-btn:hover { background: var(--theme-gold-soft); color: var(--text-primary); border-color: var(--theme-gold-soft); }
        .back-btn i { font-size: 16px; }

        .page-title { text-align: center; margin-bottom: 32px; }
        .page-title h1 { font-size: 28px; font-weight: 800; color: var(--theme-gold); }
        .page-title p { color: var(--text-secondary); font-size: 14px; margin-top: 6px; }

        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 12px 24px; border-radius: 14px; font-weight: 700;
            font-size: 14px; text-decoration: none; cursor: pointer;
            border: none; transition: all 0.3s ease;
            font-family: 'Tajawal', sans-serif; white-space: nowrap;
        }
        .btn-primary { background: var(--theme-gold); color: #000; }
        .btn-primary:hover { background: var(--theme-gold-dark); transform: translateY(-2px); }
        .btn-outline {
            background: var(--theme-surface-2); color: var(--text-secondary);
            border: 1px solid var(--theme-border);
        }
        .btn-outline:hover { background: rgba(198,166,117,0.08); color: var(--text-primary); border-color: var(--theme-gold-soft); }
        .btn-sm { padding: 8px 16px; font-size: 12px; border-radius: 10px; }
        .btn-block { width: 100%; justify-content: center; }
        .btn-gold { background: linear-gradient(135deg, var(--theme-gold), var(--theme-gold-dark)); color: #000; }
        .btn-gold:hover { transform: translateY(-2px); box-shadow: 0 12px 30px rgba(196,150,58,0.3); }

        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }

        .template-card {
            position: relative;
            background: var(--theme-surface);
            backdrop-filter: blur(24px);
            border: 1px solid var(--theme-border);
            border-radius: 22px; overflow: hidden;
            transition: all 0.3s ease;
        }
        .template-card:hover { transform: translateY(-8px); box-shadow: 0 20px 60px rgba(0,0,0,0.4); }
        .template-card img { width: 100%; height: 200px; object-fit: cover; display: block; }
        .template-card .body { padding: 18px; }
        .template-card .body h5 { color: var(--theme-gold); font-size: 16px; font-weight: 700; margin-bottom: 4px; }
        .template-card .body p  { color: var(--text-secondary); font-size: 12px; margin-bottom: 14px; }

        /* ── Modal ── */
        .modal-overlay {
            display: none; position: fixed; inset: 0; z-index: 1000;
            background: rgba(0,0,0,0.65); backdrop-filter: blur(4px);
            align-items: center; justify-content: center;
        }
        .modal-overlay.open { display: flex; }
        .modal-box {
            background: var(--theme-surface);
            border: 1px solid var(--theme-border);
            border-radius: 24px; padding: 32px 28px;
            width: 100%; max-width: 460px;
            box-shadow: 0 32px 80px rgba(0,0,0,0.5);
            animation: slideUp 0.22s ease;
        }
        @keyframes slideUp { from { opacity:0; transform:translateY(24px); } to { opacity:1; transform:translateY(0); } }
        .modal-box h2 { font-size: 20px; font-weight: 800; color: var(--theme-gold); margin-bottom: 6px; }
        .modal-box .modal-subtitle { font-size: 13px; color: var(--text-secondary); margin-bottom: 22px; }
        .modal-box label { display: block; font-size: 13px; font-weight: 700; color: var(--text-secondary); margin-bottom: 8px; }
        .modal-box select {
            width: 100%; padding: 12px 14px; border-radius: 12px;
            background: var(--theme-surface-2); border: 1px solid var(--theme-border);
            color: var(--text-primary); font-family: 'Tajawal', sans-serif; font-size: 14px;
            margin-bottom: 22px; appearance: none; cursor: pointer;
        }
        .modal-box select:focus { outline: none; border-color: var(--theme-gold); }
        .modal-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .modal-actions .btn-full { grid-column: 1/-1; }
        .modal-close-row { display: flex; justify-content: flex-end; margin-top: 14px; }
        .modal-close-btn {
            background: none; border: none; cursor: pointer;
            color: var(--text-secondary); font-size: 13px; font-family: 'Tajawal', sans-serif;
            display: inline-flex; align-items: center; gap: 4px; padding: 6px 0;
        }
        .modal-close-btn:hover { color: var(--text-primary); }

        .empty-state {
            text-align: center; padding: 60px 20px;
            background: var(--theme-surface); border: 1px solid var(--theme-border);
            border-radius: 22px; grid-column: 1/-1;
        }
        .empty-state i { font-size: 48px; color: var(--theme-gold); margin-bottom: 14px; display: block; }
        .empty-state h3 { font-size: 18px; font-weight: 700; color: var(--text-primary); margin-bottom: 8px; }
        .empty-state p { font-size: 13px; color: var(--text-secondary); margin-bottom: 20px; }

        .no-students-note {
            display: inline-flex; align-items: center; gap: 6px; font-size: 12px;
            color: var(--text-secondary); padding: 6px 12px;
            background: rgba(255,159,10,0.08); border: 1px solid rgba(255,159,10,0.2);
            border-radius: 999px; margin-bottom: 14px;
        }
        .no-students-note i { color: #ff9f0a; }
    </style>
</head>
<body>
<div class="container">

    <div class="top-bar">
        <a href="{{ route('teacher.certificates.students') }}" class="back-btn">
            <i class="ri-arrow-right-line"></i> قائمة المستفيدين
        </a>
    </div>

    <div class="page-title">
        <h1><i class="ri-layout-grid-line"></i> معرض قوالب الشهادات</h1>
        <p>تصفح جميع القوالب الجاهزة — اختر القالب المناسب ثم حدد المستفيد</p>
    </div>

    @if($students->isEmpty())
        <div class="no-students-note" style="display:flex;margin-bottom:24px;">
            <i class="ri-information-line"></i>
            لا يوجد مستفيدون بعد —
            <a href="{{ route('teacher.certificates.students.create') }}" style="color:var(--theme-gold);text-decoration:none;font-weight:700;">أضف مستفيداً الآن</a>
        </div>
    @endif

    <div class="grid">
        @php
            $presets = [
                1 => ['qw1.jpeg', 'القالب الكلاسيكي',  'زخارف رسمية وخلفية تقليدية.'],
                2 => ['qw2.jpeg', 'القالب العصري',      'بسيط وألوان هادئة للشركات الناشئة.'],
                3 => ['qw3.jpeg', 'القالب الذهبي',      'تصميم فاخر للمناسبات والجوائز.'],
                4 => ['qw4.jpeg', 'الدرع الرقمي',       'نمط تقني مستقبلي.'],
                5 => ['qw5.jpeg', 'القالب الأكاديمي',   'كلاسيكي بوقار مؤسسي.'],
                6 => ['qw6.jpeg', 'الإبداع الهندسي',    'زخارف هندسية تعبر عن الدقة.'],
                7 => ['qw7.jpeg', 'الوسام المهني',      'رصين يركز على الكفاءة والخبرة.'],
                8 => ['qw8.jpeg', 'الطراز الأكاديمي',   'نقوش خفيفة توحي بالعراقة العلمية.'],
                9 => ['qw9.jpeg', 'مودرن جرافيك',       'يجمع بين الأناقة والابتكار.'],
            ];
        @endphp

        @foreach($presets as $num => $p)
            <div class="template-card">
                <img src="{{ asset('image/'.$p[0]) }}" alt="{{ $p[1] }}">
                <div class="body">
                    <h5>{{ $p[1] }}</h5>
                    <p>{{ $p[2] }}</p>
                    @if($students->isEmpty())
                        <a href="{{ route('teacher.certificates.students.create') }}" class="btn btn-outline btn-sm btn-block">
                            <i class="ri-user-add-line"></i> أضف مستفيداً أولاً
                        </a>
                    @else
                        <button class="btn btn-gold btn-sm btn-block open-modal"
                                data-preset="{{ $num }}"
                                data-name="{{ $p[1] }}">
                            <i class="ri-award-line"></i> إصدار لمستفيد
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

</div>

{{-- Modal --}}
<div class="modal-overlay" id="studentModal">
    <div class="modal-box">
        <h2 id="modalTemplateName">اختر المستفيد</h2>
        <div class="modal-subtitle">حدد المستفيد الذي ستصدر له هذه الشهادة</div>

        <label for="modalStudentSelect">المستفيد</label>
        <select id="modalStudentSelect">
            <option value="">— اختر المستفيد —</option>
            @foreach($students as $s)
                <option value="{{ $s->id }}"
                        data-name="{{ $s->name }}"
                        data-course="{{ $s->course ?? '' }}">
                    {{ $s->name }}{{ $s->course ? ' — '.$s->course : '' }}
                </option>
            @endforeach
        </select>

        <div class="modal-actions" id="modalActions" style="display:none;">
            <a id="btnPreview"  href="#" class="btn btn-outline btn-sm" style="justify-content:center;">
                <i class="ri-eye-line"></i> معاينة
            </a>
            <a id="btnDownload" href="#" class="btn btn-primary btn-sm" style="justify-content:center;">
                <i class="ri-download-2-line"></i> PDF
            </a>
            <a id="btnEdit"     href="#" class="btn btn-outline btn-sm" style="justify-content:center;">
                <i class="ri-pencil-ruler-2-line"></i> تعديل
            </a>
            <a id="btnGallery"  href="#" class="btn btn-gold btn-sm" style="justify-content:center;">
                <i class="ri-gallery-line"></i> معرض الطالب
            </a>
        </div>

        <div class="modal-close-row">
            <button class="modal-close-btn" id="closeModal">
                <i class="ri-close-line"></i> إغلاق
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    const routes = {
        preview:  '{{ url("teacher/certificates/preview") }}',
        download: '{{ url("teacher/certificates/download") }}',
        customCreate: '{{ url("teacher/certificates/students") }}',
        gallery:  '{{ url("teacher/certificates/students") }}',
    };

    let activePreset = null;

    document.querySelectorAll('.open-modal').forEach(btn => {
        btn.addEventListener('click', function () {
            activePreset = this.dataset.preset;
            document.getElementById('modalTemplateName').textContent = 'إصدار شهادة: ' + this.dataset.name;
            document.getElementById('modalStudentSelect').value = '';
            document.getElementById('modalActions').style.display = 'none';
            document.getElementById('studentModal').classList.add('open');
        });
    });

    document.getElementById('modalStudentSelect').addEventListener('change', function () {
        const studentId = this.value;
        if (!studentId || !activePreset) {
            document.getElementById('modalActions').style.display = 'none';
            return;
        }
        document.getElementById('btnPreview').href  = routes.preview  + '/' + activePreset + '/' + studentId;
        document.getElementById('btnDownload').href = routes.download + '/' + activePreset + '/' + studentId;
        document.getElementById('btnEdit').href     = routes.customCreate + '/' + studentId + '/custom/create?preset=' + activePreset;
        document.getElementById('btnGallery').href  = routes.gallery  + '/' + studentId + '/gallery';
        document.getElementById('modalActions').style.display = 'grid';
    });

    function closeModal() {
        document.getElementById('studentModal').classList.remove('open');
        activePreset = null;
    }

    document.getElementById('closeModal').addEventListener('click', closeModal);
    document.getElementById('studentModal').addEventListener('click', function (e) {
        if (e.target === this) closeModal();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeModal();
    });
})();
</script>
@include('components.account-theme-foot')
</body>
</html>
