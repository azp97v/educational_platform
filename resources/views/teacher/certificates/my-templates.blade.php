<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قوالبي - الشهادات</title>
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
            padding: 40px;
        }
        .card {
            background: var(--theme-surface);
            backdrop-filter: blur(24px);
            border: 1px solid var(--theme-border);
            border-radius: 22px;
            padding: 32px;
            box-shadow: 0 18px 50px rgba(0,0,0,0.35);
            max-width: 1300px; margin: 0 auto;
        }
        .page-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 28px; flex-wrap: wrap; gap: 16px;
        }
        .page-header h1 { font-size: 26px; font-weight: 800; color: var(--theme-gold); }
        .page-header .sub { color: var(--text-secondary); font-size: 13px; margin-top: 4px; }
        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 20px; border-radius: 12px; font-weight: 700;
            font-size: 13px; text-decoration: none; cursor: pointer;
            border: none; transition: all 0.3s ease;
            font-family: 'Tajawal', sans-serif; white-space: nowrap;
        }
        .btn-primary { background: var(--theme-gold); color: #000; }
        .btn-primary:hover { background: var(--theme-gold-dark); transform: translateY(-2px); }
        .btn-outline { background: var(--theme-surface-2); color: var(--text-secondary); border: 1px solid var(--theme-border); }
        .btn-outline:hover { background: var(--theme-gold-soft); color: var(--text-primary); }
        .btn-sm { padding: 6px 14px; font-size: 12px; border-radius: 10px; }
        .btn-danger { background: rgba(255,59,48,0.12); color: #FF3B30; }
        .btn-danger:hover { background: rgba(255,59,48,0.2); }
        .btn-green { background: rgba(52,199,89,0.12); color: #34C759; border: 1px solid rgba(52,199,89,0.2); }
        .btn-green:hover { background: rgba(52,199,89,0.2); }

        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }

        .template-card {
            background: var(--theme-surface-2);
            border: 1px solid var(--theme-border);
            border-radius: 18px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .template-card:hover { transform: translateY(-6px); box-shadow: 0 20px 50px rgba(0,0,0,0.35); border-color: var(--theme-gold-soft); }
        .template-card .preview-area {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            height: 160px; display: flex; align-items: center; justify-content: center;
            position: relative; overflow: hidden;
        }
        .template-card .preview-area .preview-inner {
            text-align: center; padding: 16px; color: rgba(255,255,255,0.8);
        }
        .template-card .preview-area .preview-title { font-size: 16px; font-weight: 700; color: #C6A675; }
        .template-card .preview-area .preview-student { font-size: 12px; color: rgba(255,255,255,0.6); margin-top: 4px; }
        .preview-bg {
            position: absolute; inset: 0; background-size: cover; background-position: center;
            opacity: 0.3;
        }
        .template-card .body { padding: 18px; }
        .template-card .body .name { font-size: 15px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px; }
        .template-card .body .student-tag {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 999px;
            background: var(--theme-gold-soft); color: var(--theme-gold);
            font-size: 11px; font-weight: 600; margin-bottom: 10px;
        }
        .issued-tag {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 999px;
            background: rgba(52,199,89,0.12); color: #34C759;
            font-size: 11px; font-weight: 600; margin-bottom: 10px; margin-right: 6px;
        }
        .draft-tag {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 999px;
            background: rgba(255,159,10,0.12); color: #FF9F0A;
            font-size: 11px; font-weight: 600; margin-bottom: 10px; margin-right: 6px;
        }
        .template-card .body .actions { display: flex; gap: 6px; flex-wrap: wrap; margin-top: 12px; }

        .empty-state { text-align: center; padding: 60px 20px; color: var(--text-secondary); }
        .empty-state i { font-size: 52px; color: var(--theme-gold-soft); margin-bottom: 16px; display: block; }
        .empty-state h3 { font-size: 18px; color: var(--text-primary); margin-bottom: 8px; }

        .new-template-banner {
            background: linear-gradient(135deg, rgba(196,150,58,0.1), rgba(196,150,58,0.05));
            border: 1px dashed var(--theme-gold-soft);
            border-radius: 18px; padding: 24px; margin-bottom: 24px;
            display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;
        }
        .new-template-banner h3 { font-size: 16px; font-weight: 700; color: var(--theme-gold); margin-bottom: 4px; }
        .new-template-banner p { color: var(--text-secondary); font-size: 13px; }

        .pagination { display: flex; justify-content: center; gap: 6px; margin-top: 24px; flex-wrap: wrap; }
        .pagination a, .pagination span { padding: 8px 14px; border-radius: 10px; background: var(--theme-surface-2); color: var(--text-secondary); text-decoration: none; font-size: 13px; border: 1px solid var(--theme-border); }
        .pagination a:hover { background: var(--theme-gold-soft); color: var(--theme-gold); }
        .pagination .active { background: var(--theme-gold); color: #000; border-color: var(--theme-gold); }

        /* Student picker modal */
        .modal-overlay { display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.65); backdrop-filter:blur(6px); align-items:center; justify-content:center; }
        .modal-overlay.show { display:flex; }
        .modal-card { background:var(--theme-surface); border:1px solid var(--theme-border); border-radius:22px; padding:32px; max-width:480px; width:90%; box-shadow:0 30px 60px rgba(0,0,0,0.5); animation:fadeInUp 0.3s ease; }
        .modal-card h3 { font-size:18px; font-weight:800; color:var(--text-primary); margin-bottom:16px; }
        .modal-card select { width:100%; padding:12px 14px; border-radius:12px; border:1px solid var(--theme-border); background:var(--theme-surface-2); color:var(--text-primary); font-size:14px; font-family:'Tajawal',sans-serif; outline:none; margin-bottom:16px; }
        .modal-card select:focus { border-color:var(--theme-gold); }
        .modal-card .modal-actions { display:flex; gap:12px; justify-content:flex-end; }
        @keyframes fadeInUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
    </style>
</head>
<body>
    <div class="card">
        <div class="page-header">
            <div>
                <h1><i class="ri-file-text-line"></i> قوالبي المخصصة</h1>
                <div class="sub">جميع قوالب الشهادات التي صممتها — {{ $templates->total() }} قالب</div>
            </div>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <button class="btn btn-primary student-picker-btn">
                    <i class="ri-add-line"></i> قالب جديد
                </button>
                <a href="{{ route('teacher.certificates.students') }}" class="btn btn-outline">
                    <i class="ri-group-line"></i> المستفيدون
                </a>
                <a href="{{ route('teacher.dashboard') }}" class="btn btn-outline">
                    <i class="ri-arrow-right-line"></i> العودة
                </a>
            </div>
        </div>

        <div class="new-template-banner">
            <div>
                <h3><i class="ri-magic-line"></i> ابدأ تصميم شهادة جديدة</h3>
                <p>اختر أحد المستفيدين لتبدأ بتصميم شهادة إنجاز مخصصة له</p>
            </div>
            <button class="btn btn-primary student-picker-btn">
                <i class="ri-brush-line"></i> ابدأ التصميم
            </button>
        </div>

        @if($templates->isEmpty())
            <div class="empty-state">
                <i class="ri-file-paper-2-line"></i>
                <h3>لا توجد قوالب بعد</h3>
                <p style="font-size:13px;">ابدأ بإنشاء قالب شهادة مخصص لأحد المستفيدين</p>
                <button class="btn btn-primary student-picker-btn" style="margin-top:20px;">
                    <i class="ri-add-line"></i> إنشاء قالب الآن
                </button>
            </div>
        @else
            <div class="grid">
                @foreach($templates as $template)
                    @php $student = $template->certificateStudent; @endphp
                    <div class="template-card">
                        <div class="preview-area">
                            @if($template->background_image)
                                <div class="preview-bg" style="background-image:url('{{ asset('storage/'.$template->background_image) }}');"></div>
                            @else
                                <div class="preview-bg" style="background:linear-gradient(135deg,{{ $template->primary_color }},{{ $template->secondary_color }});opacity:0.6;"></div>
                            @endif
                            <div class="preview-inner" style="position:relative;z-index:1;">
                                <div class="preview-title">{{ $template->title }}</div>
                                @if($student)
                                    <div class="preview-student">{{ $student->name }}</div>
                                @endif
                                <div style="font-size:10px;color:rgba(255,255,255,0.5);margin-top:6px;">{{ $template->font_family }}</div>
                            </div>
                        </div>
                        <div class="body">
                            <div class="name">{{ $template->name }}</div>
                            @if($student)
                                <span class="student-tag"><i class="ri-user-line"></i> {{ $student->name }}</span>
                            @endif
                            @if($template->is_issued)
                                <span class="issued-tag"><i class="ri-checkbox-circle-fill"></i> صدرت</span>
                            @else
                                <span class="draft-tag"><i class="ri-draft-line"></i> مسودة</span>
                            @endif
                            <div class="actions">
                                @if($student)
                                    <a href="{{ route('teacher.certificates.custom.show', [$student, $template]) }}" class="btn btn-outline btn-sm"><i class="ri-eye-line"></i> معاينة</a>
                                    <a href="{{ route('teacher.certificates.custom.edit', [$student, $template]) }}" class="btn btn-primary btn-sm"><i class="ri-edit-line"></i> تعديل</a>
                                    @if(!$template->is_issued)
                                        <form method="POST" action="{{ route('teacher.certificates.custom.issue', [$student, $template]) }}" style="display:inline;" class="issue-form">
                                            @csrf
                                            <button type="submit" class="btn btn-green btn-sm"><i class="ri-send-plane-line"></i> إصدار</button>
                                        </form>
                                    @endif
                                @endif
                                <form method="POST" action="{{ route('teacher.certificates.custom.destroy', [$student, $template]) }}" style="display:inline;" class="delete-form">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="ri-delete-bin-line"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($templates->hasPages())
                <div class="pagination">{{ $templates->links() }}</div>
            @endif
        @endif
    </div>

    <!-- Student Picker Modal -->
    <div class="modal-overlay" id="studentPickerModal">
        <div class="modal-card">
            <h3><i class="ri-user-star-line"></i> اختر المستفيد</h3>
            <p style="color:var(--text-secondary);font-size:13px;margin-bottom:16px;">اختر الطالب الذي ستصمم له شهادة الإنجاز</p>
            <select id="studentSelect">
                <option value="">-- اختر مستفيداً --</option>
                @foreach($students as $s)
                    <option value="{{ route('teacher.certificates.custom.create', $s) }}">{{ $s->name }} — {{ $s->course }}</option>
                @endforeach
            </select>
            <div class="modal-actions">
                <button class="btn btn-outline" id="closeStudentPickerBtn"><i class="ri-close-line"></i> إلغاء</button>
                <a href="{{ route('teacher.certificates.students.create') }}" class="btn btn-outline"><i class="ri-user-add-line"></i> مستفيد جديد</a>
                <button class="btn btn-primary" id="goDesignBtn"><i class="ri-brush-line"></i> ابدأ التصميم</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.student-picker-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    document.getElementById('studentPickerModal').classList.add('show');
                });
            });
            document.getElementById('closeStudentPickerBtn').addEventListener('click', function () {
                document.getElementById('studentPickerModal').classList.remove('show');
            });
            document.getElementById('goDesignBtn').addEventListener('click', function () {
                var val = document.getElementById('studentSelect').value;
                if (!val) { alert('الرجاء اختيار مستفيد أولاً'); return; }
                window.location.href = val;
            });
            document.getElementById('studentPickerModal').addEventListener('click', function (e) {
                if (e.target === this) {
                    document.getElementById('studentPickerModal').classList.remove('show');
                }
            });
            document.querySelectorAll('.issue-form').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    if (!confirm('إصدار الشهادة للطالب؟')) {
                        e.preventDefault();
                    }
                });
            });
            document.querySelectorAll('.delete-form').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    if (!confirm('حذف هذا القالب نهائياً؟')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
    @include('components.account-theme-foot')
</body>
</html>
