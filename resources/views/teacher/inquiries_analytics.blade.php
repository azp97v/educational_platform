<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    @include('components.account-theme-head')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تحليل الأسئلة والملاحظات - لوحة المعلم</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        :root {
            --gold: #C4963A;
            --gold-dark: #A07A28;
            --gold-light: rgba(196,150,58,0.12);
            --bg: #F4F5F7;
            --sidebar-bg: #FFFFFF;
            --card-bg: #FFFFFF;
            --text-primary: #1C1C1E;
            --text-secondary: #6C6C70;
            --text-muted: #AEAEB2;
            --success: #34C759;
            --danger: #FF3B30;
            --warning: #FF9F40;
            --border: rgba(0,0,0,0.04);
            --sidebar-w: 240px;
            --transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
        }

        [data-theme="dark"] {
            --bg: #121212;
            --sidebar-bg: #1E1E1E;
            --card-bg: #1E1E1E;
            --text-primary: #F2F2F7;
            --text-secondary: #AEAEB2;
            --text-muted: #636366;
            --border: rgba(255,255,255,0.04);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Tajawal', sans-serif;
            background: var(--bg);
            color: var(--text-primary);
            transition: background 0.3s, color 0.3s;
        }

        .app { display: flex; min-height: 100vh; }

        .sidebar {
            width: var(--sidebar-w);
            background: var(--sidebar-bg);
            position: fixed;
            right: 0; top: 0;
            height: 100vh;
            z-index: 200;
            border-left: 1px solid var(--border);
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .sidebar-logo {
            padding: 30px 20px;
            text-align: center;
            border-bottom: 1px solid var(--border);
        }

        .logo-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 12px;
            color: var(--gold);
            font-size: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--gold-light);
            border-radius: 12px;
        }

        .logo-name { font-size: 18px; font-weight: 800; color: var(--gold); }
        .logo-sub { font-size: 11px; color: var(--text-muted); margin-top: 4px; }

        .sidebar-nav {
            flex: 1;
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .nav-btn {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border: none;
            border-radius: 10px;
            background: transparent;
            color: var(--text-secondary);
            font-family: 'Tajawal', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .nav-btn:hover { background: var(--gold-light); color: var(--text-primary); }
        .nav-btn.active { background: linear-gradient(135deg, var(--gold), var(--gold-dark)); color: #fff; }

        .main {
            flex: 1;
            margin-right: var(--sidebar-w);
            display: flex;
            flex-direction: column;
        }

        .topbar {
            height: 70px;
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .content {
            flex: 1;
            padding: 32px;
            overflow-y: auto;
        }

        .page-title { font-size: 28px; font-weight: 700; margin-bottom: 8px; }
        .page-subtitle { font-size: 14px; color: var(--text-secondary); margin-bottom: 24px; }

        .analytics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-size: 24px;
        }

        .stat-icon.gold { background: linear-gradient(135deg, var(--gold-light), rgba(196, 150, 58, 0.05)); color: var(--gold); }
        .stat-icon.success { background: rgba(52, 199, 89, 0.1); color: var(--success); }
        .stat-icon.warning { background: rgba(255, 159, 64, 0.1); color: var(--warning); }

        .stat-value { font-size: 32px; font-weight: 900; }
        .stat-label { font-size: 13px; color: var(--text-secondary); font-weight: 600; }

        .filter-bar {
            margin-bottom: 24px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 10px 16px;
            border: 1px solid var(--border);
            background: var(--card-bg);
            border-radius: 8px;
            font-family: 'Tajawal', sans-serif;
            color: var(--text-primary);
            cursor: pointer;
            transition: var(--transition);
            font-weight: 600;
            font-size: 13px;
        }

        .filter-btn:hover { background: var(--gold-light); border-color: var(--gold); }
        .filter-btn.active { background: linear-gradient(135deg, var(--gold), var(--gold-dark)); color: white; border-color: var(--gold); }

        .inquiries-table {
            background: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--border);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .table-header {
            padding: 16px 20px;
            background: var(--gold-light);
            display: grid;
            grid-template-columns: 2fr 1.5fr 2fr 1fr 1fr 1fr 1fr;
            gap: 12px;
            font-weight: 700;
            font-size: 13px;
            color: var(--gold);
            border-bottom: 1px solid var(--border);
        }

        .table-row {
            padding: 16px 20px;
            display: grid;
            grid-template-columns: 2fr 1.5fr 2fr 1fr 1fr 1fr;
            gap: 12px;
            border-bottom: 1px solid var(--border);
            align-items: center;
            transition: var(--transition);
        }

        .table-row:hover { background: var(--gold-light); }

        .student-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .student-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
        }

        .student-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .student-avatar .avatar-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(255,214,122,1), rgba(196,150,58,1));
            color: white;
            font-weight: 700;
        }

        .student-name { font-weight: 600; font-size: 14px; }
        .lesson-name { font-size: 13px; color: var(--text-secondary); }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .badge.pending { background: rgba(255, 159, 64, 0.15); color: var(--warning); }
        .badge.answered { background: rgba(52, 199, 89, 0.15); color: var(--success); }
        .badge.note { background: rgba(196, 150, 58, 0.12); color: var(--gold); }
        .badge.question { background: rgba(32, 178, 170, 0.15); color: #2EC4B6; }

        .action-btn {
            padding: 6px 12px;
            border: 1px solid var(--gold);
            background: transparent;
            color: var(--gold);
            border-radius: 6px;
            font-family: 'Tajawal', sans-serif;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .action-btn:hover { background: var(--gold); color: white; }

        .course-path {
            font-size: 12px;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            margin: 24px 0 16px 0;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active { display: flex; }

        .modal-content {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 30px;
            max-width: 700px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--text-secondary);
        }

        .inquiry-detail {
            margin-bottom: 20px;
        }

        .detail-label {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-secondary);
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .detail-value {
            font-size: 14px;
            color: var(--text-primary);
            padding: 12px;
            background: var(--bg);
            border-radius: 8px;
            min-height: 40px;
            display: flex;
            align-items: center;
        }

        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-family: 'Tajawal', sans-serif;
            font-size: 14px;
            color: var(--text-primary);
            background: var(--bg);
            resize: vertical;
            min-height: 120px;
        }

        textarea:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px var(--gold-light);
        }

        .modal-footer {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-family: 'Tajawal', sans-serif;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: white;
        }

        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(196, 150, 58, 0.3); }

        .btn-secondary {
            background: var(--gold-light);
            color: var(--gold);
            border: 1px solid var(--gold);
        }

        .btn-secondary:hover { background: var(--gold); color: white; }

        ::-webkit-scrollbar { display: none; }

        @media (max-width: 1024px) {
            :root { --sidebar-w: 72px; }
            .sidebar-nav .nav-btn span,
            .sidebar .logo-name,
            .sidebar .logo-sub { display: none; }
            .sidebar-nav .nav-btn { justify-content: center; padding: 12px; }
        }

        @media (max-width: 768px) {
            .app { flex-direction: column; }
            .sidebar { width: 100% !important; height: auto; position: relative; top: auto; right: auto; bottom: auto; flex-direction: row; flex-wrap: wrap; border-radius: 0; padding: 12px; gap: 8px; }
            .sidebar-nav { flex-direction: row; display: flex; flex-wrap: wrap; gap: 8px; overflow: visible; }
            .sidebar-logo { display: none; }
            .sidebar-footer { display: none; }
            .main { margin-right: 0 !important; }
            .table-header, .table-row { grid-template-columns: 1fr; }
            .analytics-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 480px) {
            .content { padding: 10px !important; }
        }
    </style>
</head>
<body>
<div class="app">
  <aside class="sidebar">
    <div class="sidebar-logo">
      <div class="logo-icon"><i class="ri-book-read-fill"></i></div>
      <div class="logo-name">إجلال</div>
      <div class="logo-sub">المنصة التعليمية</div>
    </div>

    <nav class="sidebar-nav">
      <button class="nav-btn" data-href="{{ route('teacher.index') }}">
        <i class="ri-home-4-line"></i><span>الرئيسية</span>
      </button>
      <button class="nav-btn" data-href="{{ route('teacher.index') }}">
        <i class="ri-book-2-line"></i><span>المسارات</span>
      </button>
      <button class="nav-btn" data-href="{{ route('teacher.categories') }}">
        <i class="ri-price-tag-3-line"></i><span>الفئات</span>
      </button>
      <button class="nav-btn" data-href="{{ route('teacher.exams') }}">
        <i class="ri-file-list-line"></i><span>الاختبارات</span>
      </button>
      <button class="nav-btn" data-href="{{ route('teacher.analytics') }}">
        <i class="ri-bar-chart-2-line"></i><span>نسبة الإنجاز</span>
      </button>
      <button class="nav-btn" data-href="{{ route('teacher.students') }}">
        <i class="ri-team-line"></i><span>طلابي</span>
      </button>
      <button class="nav-btn active" data-href="{{ route('teacher.questions.manage') }}">
        <i class="ri-chat-3-line"></i><span>الأسئلة والاستفسارات</span>
      </button>
    </nav>
  </aside>

  <div class="main">
    <header class="topbar">
      <div style="display: flex; align-items: center; gap: 12px;">
        <button id="darkModeToggle" class="nav-btn" style="width: 40px; height: 40px; padding: 0; border: 1px solid var(--border);">
          <i class="ri-moon-line"></i>
        </button>
      </div>
      <div>{{ Auth::user()->name ?? 'المعلم' }}</div>
    </header>

    <div class="content">
      <h1 class="page-title">الأسئلة والملاحظات التفصيلية</h1>
      <p class="page-subtitle">تحليل شامل لجميع أسئلة واستفسارات الطلاب مع معلومات المسار والدرس</p>

      <!-- Statistics -->
      <div class="analytics-grid">
        <div class="stat-card">
          <div style="display: flex; align-items: center; gap: 12px;">
            <div class="stat-icon warning"><i class="ri-time-line"></i></div>
            <div>
              <div class="stat-label">قيد الانتظار</div>
              <div class="stat-value">{{ $pendingInquiries->count() }}</div>
            </div>
          </div>
        </div>

        <div class="stat-card">
          <div style="display: flex; align-items: center; gap: 12px;">
            <div class="stat-icon success"><i class="ri-check-double-line"></i></div>
            <div>
              <div class="stat-label">مجاب عليه</div>
              <div class="stat-value">{{ $answeredInquiries->count() }}</div>
            </div>
          </div>
        </div>

        <div class="stat-card">
          <div style="display: flex; align-items: center; gap: 12px;">
            <div class="stat-icon gold"><i class="ri-chat-3-line"></i></div>
            <div>
              <div class="stat-label">المجموع</div>
              <div class="stat-value">{{ $pendingInquiries->count() + $answeredInquiries->count() }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="filter-bar">
        <button class="filter-btn active" data-filter="all">جميع الاستفسارات</button>
        <button class="filter-btn" data-filter="pending">قيد الانتظار فقط</button>
        <button class="filter-btn" data-filter="answered">المجاب عليها فقط</button>
      </div>

      <!-- Pending Inquiries -->
      @if($pendingInquiries->count() > 0)
        <div class="section-title">
          <i class="ri-time-line" style="color: var(--warning);"></i>
          الاستفسارات قيد الانتظار ({{ $pendingInquiries->count() }})
        </div>

        <div class="inquiries-table">
          <div class="table-header">
            <span>الطالب</span>
            <span>المسار</span>
            <span>الدرس</span>
            <span>النوع</span>
            <span>وقت الاستفسار</span>
            <span>الحالة</span>
            <span>الإجراء</span>
          </div>

          @foreach($pendingInquiries as $inquiry)
            <div class="table-row">
              <div class="student-cell">
                <div class="student-avatar">
                  @if($inquiry->student->avatar_url)
                    <img src="{{ asset('storage/' . $inquiry->student->avatar_url) }}" loading="lazy" alt="{{ $inquiry->student->name }}">
                  @else
                    <div class="avatar-placeholder">{{ mb_substr($inquiry->student->name, 0, 1) }}</div>
                  @endif
                </div>
                <div>
                  <div class="student-name">{{ $inquiry->student->name }}</div>
                  <div class="lesson-name">{{ $inquiry->student->email }}</div>
                </div>
              </div>

              <div>
                <div class="course-path">
                  <i class="ri-folder-line"></i>
                  {{ $inquiry->course->name ?? 'غير محدد' }}
                </div>
              </div>

              <div>
                <div class="course-path">
                  <i class="ri-book-line"></i>
                  {{ $inquiry->lesson->name ?? 'غير محدد' }}
                </div>
              </div>

              <div>
                <span class="badge {{ $inquiry->inquiry_type === 'note' ? 'note' : 'question' }}">
                  {{ $inquiry->inquiry_type === 'note' ? 'ملاحظة' : 'سؤال' }}
                </span>
              </div>

              <div style="font-size: 13px; color: var(--text-secondary);">
                {{ $inquiry->created_at->format('d/m/Y H:i') }}
              </div>

              <div>
                <span class="badge pending">
                  <i class="ri-time-line"></i>
                  قيد الانتظار
                </span>
              </div>

              <div>
                <button class="action-btn reply-btn" data-inquiry-id="{{ $inquiry->id }}" data-question-text="{{ $inquiry->question_text }}">
                  <i class="ri-reply-line"></i> رد
                </button>
              </div>
            </div>
          @endforeach
        </div>
      @endif

      <!-- Answered Inquiries -->
      @if($answeredInquiries->count() > 0)
        <div class="section-title">
          <i class="ri-check-double-line" style="color: var(--success);"></i>
          الاستفسارات المجاب عليها ({{ $answeredInquiries->count() }})
        </div>

        <div class="inquiries-table">
          <div class="table-header">
            <span>الطالب</span>
            <span>المسار</span>
            <span>الدرس</span>
            <span>النوع</span>
            <span>التاريخ</span>
            <span>الحالة</span>
            <span>الإجراء</span>
          </div>

          @foreach($answeredInquiries as $inquiry)
            <div class="table-row">
              <div class="student-cell">
                <div class="student-avatar">
                  @if($inquiry->student->avatar_url)
                    <img src="{{ asset('storage/' . $inquiry->student->avatar_url) }}" loading="lazy" alt="{{ $inquiry->student->name }}">
                  @else
                    <div class="avatar-placeholder">{{ mb_substr($inquiry->student->name, 0, 1) }}</div>
                  @endif
                </div>
                <div>
                  <div class="student-name">{{ $inquiry->student->name }}</div>
                  <div class="lesson-name">{{ $inquiry->student->email }}</div>
                </div>
              </div>

              <div>
                <div class="course-path">
                  <i class="ri-folder-line"></i>
                  {{ $inquiry->course->name ?? 'غير محدد' }}
                </div>
              </div>

              <div>
                <div class="course-path">
                  <i class="ri-book-line"></i>
                  {{ $inquiry->lesson->name ?? 'غير محدد' }}
                </div>
              </div>

              <div>
                <span class="badge {{ $inquiry->inquiry_type === 'note' ? 'note' : 'question' }}">
                  {{ $inquiry->inquiry_type === 'note' ? 'ملاحظة' : 'سؤال' }}
                </span>
              </div>

              <div style="font-size: 13px; color: var(--text-secondary);">
                {{ $inquiry->answered_at ? \Illuminate\Support\Carbon::parse($inquiry->answered_at)->format('d/m/Y H:i') : '-' }}
              </div>

              <div>
                <span class="badge answered">
                  <i class="ri-check-line"></i>
                  مجاب عليه
                </span>
              </div>

              <div>
                <button class="action-btn view-btn" data-inquiry-id="{{ $inquiry->id }}" data-question-text="{{ $inquiry->question_text }}" data-answer-text="{{ $inquiry->answer_text }}">
                  <i class="ri-eye-line"></i> عرض
                </button>
              </div>
            </div>
          @endforeach
        </div>
      @endif

      @if($pendingInquiries->count() == 0 && $answeredInquiries->count() == 0)
        <div style="text-align: center; padding: 60px 20px; color: var(--text-secondary);">
          <i class="ri-inbox-line" style="font-size: 64px; opacity: 0.3; margin-bottom: 15px; display: block;"></i>
          <h3 style="font-size: 18px; font-weight: 600; color: var(--text-primary); margin-bottom: 8px;">لا توجد استفسارات</h3>
          <p>جميع الاستفسارات من الطلاب ستظهر هنا</p>
        </div>
      @endif
    </div>
  </div>
</div>

<!-- Reply Modal -->
<div id="replyModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      الرد على السؤال
      <button id="closeReplyModalBtn" class="modal-close">×</button>
    </div>

    <div class="inquiry-detail">
      <div class="detail-label">السؤال</div>
      <div class="detail-value" id="questionText" style="white-space: pre-wrap;"></div>
    </div>

    <form id="replyForm" method="POST">
      @csrf
      <div class="inquiry-detail">
        <label class="detail-label">إجابتك</label>
        <textarea name="answer_text" placeholder="اكتب إجابة مفصلة وواضحة للطالب..." required></textarea>
      </div>

      <div class="modal-footer">
        <button id="closeReplyModalBtn2" type="button" class="btn btn-secondary">إلغاء</button>
        <button type="submit" class="btn btn-primary">إرسال الرد</button>
      </div>
    </form>
  </div>
</div>

<!-- View Answer Modal -->
<div id="viewModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      السؤال والإجابة
      <button id="closeViewModalBtn" class="modal-close">×</button>
    </div>

    <div class="inquiry-detail">
      <div class="detail-label">السؤال</div>
      <div class="detail-value" id="viewQuestionText" style="white-space: pre-wrap;"></div>
    </div>

    <div class="inquiry-detail">
      <div class="detail-label">الإجابة</div>
      <div class="detail-value" id="viewAnswerText" style="white-space: pre-wrap; background: rgba(52, 199, 89, 0.05); border-left: 3px solid var(--success);"></div>
    </div>

    <div class="modal-footer">
      <button id="closeViewModalBtn2" class="btn btn-secondary" style="flex: 1;">إغلاق</button>
    </div>
  </div>
</div>

<script>
// Load theme preference
(function() {
  const saved = localStorage.getItem('app-theme') || 'light';
  document.documentElement.setAttribute('data-theme', saved);
})();

function toggleDarkMode() {
  const html = document.documentElement;
  const isDark = html.getAttribute('data-theme') === 'dark';
  html.setAttribute('data-theme', isDark ? 'light' : 'dark');
  localStorage.setItem('app-theme', isDark ? 'light' : 'dark');
}

function openReplyModal(inquiryId, questionText) {
  document.getElementById('questionText').textContent = questionText;
  document.getElementById('replyForm').action = '/teacher/inquiries/' + inquiryId + '/answer';
  document.getElementById('replyModal').classList.add('active');
}

function closeModal(modalId) {
  document.getElementById(modalId).classList.remove('active');
}

function viewAnswer(inquiryId, questionText, answerText) {
  document.getElementById('viewQuestionText').textContent = questionText;
  document.getElementById('viewAnswerText').textContent = answerText;
  document.getElementById('viewModal').classList.add('active');
}

function filterInquiries(type, event) {
  document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
  if (event && event.target) {
    const btn = event.target.closest('.filter-btn');
    if (btn) btn.classList.add('active');
  }
}

document.addEventListener('DOMContentLoaded', function() {
  // 1. Dark mode toggle via addEventListener
  document.getElementById('darkModeToggle').addEventListener('click', toggleDarkMode);

  // 2. Nav buttons via event delegation on .sidebar-nav
  document.querySelector('.sidebar-nav').addEventListener('click', function(e) {
    const btn = e.target.closest('.nav-btn');
    if (btn && btn.dataset.href) {
      location.href = btn.dataset.href;
    }
  });

  // 3. Filter buttons via event delegation on .filter-bar
  document.querySelector('.filter-bar').addEventListener('click', function(e) {
    const btn = e.target.closest('.filter-btn');
    if (btn && btn.dataset.filter) {
      filterInquiries(btn.dataset.filter, e);
    }
  });

  // 4. Reply buttons via event delegation (dynamically rendered rows)
  document.addEventListener('click', function(e) {
    const btn = e.target.closest('.reply-btn');
    if (btn) {
      openReplyModal(btn.dataset.inquiryId, btn.dataset.questionText);
    }
  });

  // 5. View answer buttons via event delegation
  document.addEventListener('click', function(e) {
    const btn = e.target.closest('.view-btn');
    if (btn) {
      viewAnswer(btn.dataset.inquiryId, btn.dataset.questionText, btn.dataset.answerText);
    }
  });

  // 6. Close reply modal buttons
  document.getElementById('closeReplyModalBtn').addEventListener('click', function() {
    closeModal('replyModal');
  });
  document.getElementById('closeReplyModalBtn2').addEventListener('click', function() {
    closeModal('replyModal');
  });

  // 7. Close view modal buttons
  document.getElementById('closeViewModalBtn').addEventListener('click', function() {
    closeModal('viewModal');
  });
  document.getElementById('closeViewModalBtn2').addEventListener('click', function() {
    closeModal('viewModal');
  });
});
</script>
    @include('components.account-theme-foot')
</body>
</html>






