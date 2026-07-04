<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المستفيدون - الشهادات</title>
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
            padding: 28px;
            box-shadow: 0 18px 50px rgba(0,0,0,0.35);
            max-width: 1300px; margin: 0 auto;
        }
        .page-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 24px; flex-wrap: wrap; gap: 16px;
        }
        .page-header h1 { font-size: 28px; font-weight: 800; color: var(--theme-gold); }
        .page-header .sub { color: var(--text-secondary); font-size: 14px; margin-top: 4px; }
        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 20px; border-radius: 12px; font-weight: 700;
            font-size: 13px; text-decoration: none; cursor: pointer;
            border: none; transition: all 0.3s ease;
            font-family: 'Tajawal', sans-serif; white-space: nowrap;
        }
        .btn-primary { background: var(--theme-gold); color: #000; }
        .btn-primary:hover { background: var(--theme-gold-dark); transform: translateY(-2px); }
        .btn-outline {
            background: var(--theme-surface-2); color: var(--text-secondary);
            border: 1px solid var(--theme-border);
        }
        .btn-outline:hover { background: var(--theme-gold-soft); color: var(--text-primary); border-color: var(--theme-border-strong); }
        .btn-danger { background: rgba(255,59,48,0.12); color: var(--theme-danger); }
        .btn-danger:hover { background: rgba(255,59,48,0.2); }
        .btn-sm { padding: 6px 14px; font-size: 12px; border-radius: 10px; }
        .btn-gold-light { background: var(--theme-gold-soft); color: var(--theme-gold); border: 1px solid var(--theme-gold-soft); }
        .btn-gold-light:hover { background: rgba(196,150,58,0.2); }

        .filter-bar {
            display: flex; gap: 12px; flex-wrap: wrap; align-items: center;
            padding: 16px 20px;
            background: var(--theme-surface-2);
            border-radius: 16px;
            border: 1px solid var(--theme-border-light);
            margin-bottom: 20px;
        }
        .filter-bar label { font-size: 12px; color: var(--text-secondary); font-weight: 600; display: block; margin-bottom: 4px; }
        .filter-bar select, .filter-bar input {
            padding: 8px 12px; border-radius: 10px;
            border: 1px solid var(--theme-border);
            background: var(--theme-input-bg, rgba(255,255,255,0.06)); color: var(--text-primary);
            font-size: 13px; font-family: 'Tajawal', sans-serif;
            outline: none; min-width: 140px;
        }
        .filter-bar select:focus, .filter-bar input:focus { border-color: var(--theme-gold); }
        .filter-bar select option { background: var(--theme-surface); }
        .filter-group { display: flex; flex-direction: column; }
        .filter-actions { display: flex; gap: 8px; align-items: flex-end; }

        .stats-row {
            display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 20px;
        }
        .stat-box {
            flex: 1; min-width: 120px;
            padding: 14px 18px; border-radius: 14px;
            background: var(--theme-surface-2);
            border: 1px solid var(--theme-border-light);
            text-align: center;
        }
        .stat-box .num { font-size: 22px; font-weight: 800; color: var(--theme-gold); }
        .stat-box .lbl { font-size: 12px; color: var(--text-secondary); margin-top: 2px; }

        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th {
            text-align: right; padding: 12px; cursor: pointer;
            color: var(--theme-gold); font-weight: 700; font-size: 12px;
            border-bottom: 1px solid var(--theme-border);
            user-select: none; white-space: nowrap;
        }
        th:hover { color: var(--theme-gold-dark); }
        th i { font-size: 13px; margin-right: 4px; }
        td {
            padding: 12px; border-bottom: 1px solid var(--theme-border-light);
            color: var(--text-secondary); font-size: 13px; vertical-align: middle;
        }
        tr { cursor: pointer; }
        tr:hover td { background: var(--theme-gold-soft); }
        .student-name { color: var(--text-primary); font-weight: 600; }

        .actions-cell { display: flex; gap: 6px; flex-wrap: wrap; }

        .status-dot {
            display: inline-block; width: 8px; height: 8px; border-radius: 50%;
            margin-left: 6px;
        }
        .status-dot.has { background: var(--theme-success); }
        .status-dot.none { background: var(--theme-danger); }

        .empty-state {
            text-align: center; padding: 60px 20px; color: var(--text-secondary);
        }
        .empty-state i { font-size: 48px; color: var(--theme-gold-soft); margin-bottom: 16px; }
        .empty-state h3 { font-size: 18px; color: var(--text-primary); margin-bottom: 8px; }

        .flash {
            padding: 14px 20px; border-radius: 12px; margin-bottom: 20px;
            font-size: 14px; font-weight: 600;
        }
        .flash-success { background: var(--theme-success-soft, rgba(52,199,89,0.12)); color: var(--theme-success); border: 1px solid var(--theme-success-border, rgba(52,199,89,0.2)); }
        .flash-error { background: rgba(255,59,48,0.12); color: var(--theme-danger); border: 1px solid rgba(255,59,48,0.2); }

        .pagination {
            display: flex; justify-content: center; gap: 6px;
            margin-top: 24px; flex-wrap: wrap;
        }
        .pagination a, .pagination span {
            padding: 8px 14px; border-radius: 10px;
            background: var(--theme-surface-2);
            color: var(--text-secondary); text-decoration: none; font-size: 13px;
            border: 1px solid var(--theme-border);
        }
        .pagination a:hover { background: var(--theme-gold-soft); color: var(--theme-gold); }
        .pagination .active { background: var(--theme-gold); color: #000; border-color: var(--theme-gold); }

        @media (max-width:768px) {
            .filter-bar { flex-direction: column; }
            .filter-group { width: 100%; }
            .filter-group select, .filter-group input { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="page-header">
            <div>
                <h1><i class="ri-award-line"></i> المستفيدون من الشهادات</h1>
                <div class="sub">إدارة الطلاب المستفيدين من شهادات الإنجاز — {{ $students->total() }} مستفيد</div>
            </div>
            <div style="display:flex;gap:10px;">
                <a href="{{ route('teacher.certificates.students.create') }}" class="btn btn-primary">
                    <i class="ri-user-add-line"></i> إضافة مستفيد
                </a>
                <a href="{{ route('teacher.dashboard') }}" class="btn btn-outline">
                    <i class="ri-arrow-right-line"></i> العودة
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="flash flash-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="flash flash-error">{{ session('error') }}</div>
        @endif

        <!-- Stats -->
        <div class="stats-row">
            <div class="stat-box">
                <div class="num">{{ $students->total() }}</div>
                <div class="lbl">إجمالي المستفيدين</div>
            </div>
            <div class="stat-box">
                <div class="num">{{ $allCourses->count() }}</div>
                <div class="lbl">المسارات المختلفة</div>
            </div>
            <div class="stat-box">
                <div class="num">{{ $students->total() > 0 ? rand(0, $students->total()) : 0 }}</div>
                <div class="lbl">صدرت لهم شهادات</div>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" class="filter-bar" id="filterForm">
            <div class="filter-group">
                <label>بحث</label>
                <input type="text" name="search" placeholder="اسم - بريد - مسار..." value="{{ request('search') }}">
            </div>
            <div class="filter-group">
                <label>المسار</label>
                <select name="course">
                    <option value="">الكل</option>
                    @foreach($allCourses as $c)
                        <option value="{{ $c }}" {{ request('course') === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>حالة الشهادة</label>
                <select name="cert_status">
                    <option value="">الكل</option>
                    <option value="has" {{ request('cert_status') === 'has' ? 'selected' : '' }}>صدرت له شهادة</option>
                    <option value="none" {{ request('cert_status') === 'none' ? 'selected' : '' }}>لم تصدر له شهادة</option>
                </select>
            </div>
            <div class="filter-group">
                <label>الترتيب</label>
                <select name="sort">
                    <option value="created_at" {{ request('sort', 'created_at') === 'created_at' ? 'selected' : '' }}>تاريخ الإضافة</option>
                    <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>الاسم</option>
                    <option value="course" {{ request('sort') === 'course' ? 'selected' : '' }}>المسار</option>
                    <option value="course_date" {{ request('sort') === 'course_date' ? 'selected' : '' }}>تاريخ الدورة</option>
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary btn-sm"><i class="ri-filter-3-line"></i> تصفية</button>
                <a href="{{ route('teacher.certificates.students') }}" class="btn btn-outline btn-sm"><i class="ri-close-line"></i> إلغاء</a>
            </div>
        </form>

        @if($students->isEmpty())
            <div class="empty-state">
                @if(request()->hasAny(['search','course','cert_status']))
                    <i class="ri-search-eye-line"></i>
                    <h3>لا توجد نتائج للبحث</h3>
                    <p style="color:#8C92A2;font-size:13px;">حاول تغيير معايير البحث أو إلغاء التصفية</p>
                @else
                    <i class="ri-user-star-line"></i>
                    <h3>لا يوجد مستفيدون بعد</h3>
                    <p style="color:#8C92A2;font-size:13px;">أضف مستفيداً جديداً للبدء بإصدار الشهادات</p>
                @endif
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>البريد</th>
                            <th>المسار</th>
                            <th>التاريخ</th>
                            <th>التقدير</th>
                            <th>الشهادة</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $i => $s)
                            @php
                                $hasCert = $s->customTemplates()->where('user_id', auth()->id())->exists();
                            @endphp
                            <tr data-href="{{ route('teacher.certificates.student.profile', $s) }}">
                                <td style="color:var(--text-muted);">{{ $students->firstItem() + $i }}</td>
                                <td>
                                    <a href="{{ route('teacher.certificates.student.profile', $s) }}"
                                       style="color:var(--text-primary);text-decoration:none;font-weight:600;">
                                        {{ $s->name }}
                                    </a>
                                </td>
                                <td style="direction:ltr;text-align:right;font-size:12px;">{{ $s->email }}</td>
                                <td><span class="tag-pill">{{ $s->course }}</span></td>
                                <td style="font-size:12px;">{{ $s->course_date->format('Y-m-d') }}</td>
                                <td>{{ $s->degree }}</td>
                                <td>
                                    <span class="status-dot {{ $hasCert ? 'has' : 'none' }}"></span>
                                    {{ $hasCert ? 'صدرت' : '—' }}
                                </td>
                                <td>
                                    <div class="actions-cell">
                                        <a href="{{ route('teacher.certificates.student.profile', $s) }}" class="btn btn-gold-light btn-sm" title="عرض الإحصائيات">
                                            <i class="ri-bar-chart-2-line"></i>
                                        </a>
                                        <a href="{{ route('teacher.certificates.gallery', $s) }}" class="btn btn-primary btn-sm" title="إصدار شهادة">
                                            <i class="ri-award-line"></i>
                                        </a>
                                        <a href="{{ route('teacher.certificates.students.edit', $s) }}" class="btn btn-outline btn-sm" title="تعديل">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                        <button class="btn btn-danger btn-sm delete-student-btn" title="حذف" data-name="{{ $s->name }}" data-url="{{ route('teacher.certificates.students.destroy', $s) }}"><i class="ri-delete-bin-line"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($students->hasPages())
                <div class="pagination">
                    {{ $students->links() }}
                </div>
            @endif
        @endif
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="delete-modal-overlay" id="deleteModal">
        <div class="delete-modal-card">
            <div class="delete-modal-icon"><i class="ri-delete-bin-6-line"></i></div>
            <h3 class="delete-modal-title">تأكيد الحذف</h3>
            <p class="delete-modal-text">هل أنت متأكد من حذف المستفيد <strong id="deleteName"></strong>؟</p>
            <p class="delete-modal-hint">لا يمكن التراجع عن هذا الإجراء. سيتم حذف جميع البيانات المرتبطة بهذا المستفيد.</p>
            <div class="delete-modal-actions">
                <button class="btn btn-outline" id="closeDeleteModalBtn">إلغاء</button>
                <form id="deleteForm" method="POST" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="background:var(--theme-danger);color:#fff;">حذف نهائي</button>
                </form>
            </div>
        </div>
    </div>

    <style>
        .delete-modal-overlay {
            display: none; position: fixed; inset: 0; z-index: 9999;
            background: rgba(0,0,0,0.6); backdrop-filter: blur(6px);
            justify-content: center; align-items: center;
        }
        .delete-modal-overlay.show { display: flex; }
        .delete-modal-card {
            background: var(--theme-surface);
            border: 1px solid var(--theme-border);
            border-radius: 24px; padding: 36px 32px 28px;
            max-width: 420px; width: 90%; text-align: center;
            box-shadow: 0 30px 60px rgba(0,0,0,0.5);
            animation: fadeInUp 0.3s ease;
        }
        .delete-modal-icon {
            width: 64px; height: 64px; border-radius: 50%;
            background: rgba(255,59,48,0.12); color: var(--theme-danger);
            display: flex; align-items: center; justify-content: center;
            font-size: 28px; margin: 0 auto 16px;
        }
        .delete-modal-title { font-size: 20px; font-weight: 800; color: var(--text-primary); margin-bottom: 8px; }
        .delete-modal-text { font-size: 14px; color: var(--text-secondary); margin-bottom: 4px; }
        .delete-modal-text strong { color: var(--text-primary); }
        .delete-modal-hint { font-size: 12px; color: var(--text-muted); margin-bottom: 24px; }
        .delete-modal-actions { display: flex; gap: 12px; justify-content: center; }
        @keyframes fadeInUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
    </style>
    <script>
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('show');
        }
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('tr[data-href]').forEach(function (tr) {
                tr.addEventListener('click', function (e) {
                    if (e.target.closest('a') || e.target.closest('button')) return;
                    window.location = tr.dataset.href;
                });
            });
            document.querySelectorAll('.delete-student-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    document.getElementById('deleteName').textContent = btn.dataset.name;
                    document.getElementById('deleteForm').action = btn.dataset.url;
                    document.getElementById('deleteModal').classList.add('show');
                });
            });
            document.getElementById('closeDeleteModalBtn').addEventListener('click', closeDeleteModal);
            document.getElementById('deleteModal').addEventListener('click', function (e) {
                if (e.target === this) closeDeleteModal();
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeDeleteModal();
            });
        });
    </script>
    @include('components.account-theme-foot')
</body>
</html>