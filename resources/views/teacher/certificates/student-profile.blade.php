<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ملف {{ $student->name }} — الشهادات</title>
    @include('components.account-theme-head')
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --gold: var(--theme-gold);
            --gold-hover: var(--theme-gold-dark);
            --gold-soft: var(--theme-gold-soft);
            --gold-border: var(--theme-border-strong);
            --bg: radial-gradient(circle at top left, rgba(198,166,117,0.18), transparent 22%),
                   linear-gradient(180deg, var(--theme-page-bg) 0%, var(--theme-surface) 40%, var(--theme-surface-2) 100%);
            --card: var(--theme-surface);
            --card-alt: var(--theme-surface-2);
            --text: var(--text-primary);
            --text-secondary: var(--theme-text-soft);
            --text-muted: var(--theme-muted);
            --success: var(--theme-success);
            --danger: var(--theme-danger);
            --warning: #FF9500;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Tajawal', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            padding: 30px 20px;
            transition: background 0.3s, color 0.3s;
        }
        .container { max-width: 1100px; margin: 0 auto; }

        .card {
            background: var(--card);
            backdrop-filter: blur(24px);
            border: 1px solid var(--gold-border);
            border-radius: 24px;
            padding: 28px;
            box-shadow: 0 18px 50px rgba(0,0,0,0.35);
            transition: background 0.3s;
        }
        .card-alt {
            background: var(--card-alt);
            border-radius: 16px;
            padding: 16px 20px;
            border: 1px solid var(--gold-border);
        }

        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 20px; border-radius: 12px; font-weight: 700;
            font-size: 13px; text-decoration: none; cursor: pointer;
            border: none; transition: all 0.3s ease;
            font-family: 'Tajawal', sans-serif; white-space: nowrap;
        }
        .btn-primary { background: var(--gold); color: #000; }
        .btn-primary:hover { background: var(--gold-hover); transform: translateY(-2px); }
        .btn-outline { background: var(--theme-surface-2); color: var(--text-secondary); border: 1px solid var(--theme-border); }
        .btn-outline:hover { background: var(--theme-gold-soft); color: var(--text-primary); border-color: var(--theme-border-strong); }
        .btn-sm { padding: 6px 14px; font-size: 12px; border-radius: 10px; }

        @media (max-width:768px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .profile-hero { flex-direction: column; text-align: center; }
            .profile-info .meta { justify-content: center; }
        }

        .header {
            display: flex; justify-content: space-between; align-items: center;
            flex-wrap: wrap; gap: 14px; margin-bottom: 20px;
        }
        .header h1 { font-size: 22px; font-weight: 800; color: var(--text); }
        .header .sub { font-size: 13px; color: var(--text-muted); margin-top: 4px; }

        .profile-hero {
            display: flex; align-items: center; gap: 24px;
            padding: 16px 0 8px; flex-wrap: wrap;
        }
        .profile-avatar {
            width: 80px; height: 80px; border-radius: 50%;
            background: var(--gold-soft); color: var(--gold);
            display: flex; align-items: center; justify-content: center;
            font-size: 32px; font-weight: 800;
            flex-shrink: 0; overflow: hidden;
            border: 3px solid var(--gold);
        }
        .profile-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
        .avatar-wrap { position: relative; flex-shrink: 0; width: 80px; height: 80px; }
        .online-dot {
            position: absolute; bottom: -2px; right: -2px;
            width: 18px; height: 18px; border-radius: 50%;
            background: var(--success);
            border: 3px solid var(--card);
            z-index: 2;
            box-shadow: 0 0 0 2px var(--success);
        }
        .profile-info h2 { font-size: 20px; font-weight: 800; color: var(--text); }
        .profile-info .email { font-size: 13px; color: var(--text-muted); margin: 2px 0 8px; }
        .profile-info .meta {
            display: flex; flex-wrap: wrap; gap: 10px; align-items: center;
        }
        .profile-info .meta span {
            font-size: 12px; color: var(--text-secondary);
            display: inline-flex; align-items: center; gap: 4px;
        }
        .badge-gold {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 10px; border-radius: 999px;
            background: var(--gold-soft); color: var(--gold);
            font-size: 11px; font-weight: 700;
        }

        .stats-grid {
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: 14px; margin-top: 8px;
        }
        @media (max-width:600px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
        .stat-card {
            background: var(--card-alt); border-radius: 16px;
            padding: 18px 16px; text-align: center;
            border: 1px solid var(--theme-border-light, var(--theme-border));
        }
        .stat-icon {
            width: 40px; height: 40px; border-radius: 12px;
            background: var(--gold-soft); color: var(--gold);
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; margin: 0 auto 10px;
        }
        .stat-num { font-size: 28px; font-weight: 800; color: var(--text); }
        .stat-lbl { font-size: 12px; color: var(--text-secondary); margin-top: 4px; font-weight: 600; }
        .stat-sub { font-size: 11px; color: var(--text-muted); margin-top: 2px; }

        .progress-ring-wrap { display: flex; align-items: center; gap: 12px; }
        .progress-ring { position: relative; width: 70px; height: 70px; }
        .progress-ring svg { transform: rotate(-90deg); }
        .circle-bg { fill: none; stroke: var(--theme-surface-2); stroke-width: 6; }
        .circle-fg { fill: none; stroke: var(--gold); stroke-width: 6; stroke-linecap: round; transition: stroke-dashoffset 0.5s; }
        .center-text {
            position: absolute; inset: 0; display: flex;
            align-items: center; justify-content: center;
            font-size: 16px; font-weight: 800; color: var(--gold);
        }

        .section-title {
            font-size: 16px; font-weight: 700; color: var(--text);
            margin-bottom: 14px; display: flex; align-items: center; gap: 8px;
        }

        .course-list { display: flex; flex-direction: column; gap: 8px; }
        .course-item {
            display: flex; justify-content: space-between; align-items: center;
            padding: 12px 14px; background: var(--card-alt);
            border-radius: 12px; gap: 10px; flex-wrap: wrap;
            border: 1px solid var(--theme-border-light, transparent);
        }
        .course-name { font-size: 13px; font-weight: 700; color: var(--text); }
        .course-badge {
            font-size: 11px; font-weight: 700; padding: 4px 10px;
            border-radius: 999px; white-space: nowrap;
        }
        .course-badge.has-cert { background: rgba(34,153,102,0.12); color: var(--success); }
        .course-badge.pending { background: rgba(255,149,0,0.12); color: var(--warning); }
        .course-badge.enrolled { background: var(--theme-gold-soft); color: var(--gold); }

        .cert-list { display: flex; flex-direction: column; gap: 8px; }
        .cert-item {
            display: flex; justify-content: space-between; align-items: center;
            padding: 12px 14px; background: var(--card-alt);
            border-radius: 12px; gap: 10px; flex-wrap: wrap;
            border: 1px solid var(--theme-border-light, transparent);
        }
        .cert-name { font-size: 13px; font-weight: 700; color: var(--text); display: flex; align-items: center; gap: 6px; }
        .cert-meta { font-size: 11px; color: var(--text-muted); }
        .cert-actions { display: flex; gap: 6px; }

        .empty-hint {
            text-align: center; padding: 32px 16px; color: var(--text-muted);
        }
        .empty-hint i { font-size: 36px; color: var(--gold-soft); margin-bottom: 12px; display: block; }
        .empty-hint p { font-size: 13px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="card" style="margin-bottom:24px;">
            <div class="header">
                <div>
                    <h1><i class="ri-bar-chart-2-line"></i> ملف الشهادات</h1>
                    <div class="sub">إحصائيات وإنجازات المستفيد {{ $student->name }}</div>
                </div>
                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <a href="{{ route('teacher.certificates.gallery', $student) }}" class="btn btn-primary"><i class="ri-award-line"></i> إصدار شهادة</a>
                    <a href="{{ route('teacher.certificates.students.edit', $student) }}" class="btn btn-outline"><i class="ri-edit-line"></i> تعديل</a>
                    <a href="{{ route('teacher.certificates.students') }}" class="btn btn-outline"><i class="ri-arrow-right-line"></i> العودة</a>
                    <form method="POST" action="{{ route('teacher.certificates.toggle-auto-issue') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn {{ $autoIssue ? 'btn-primary' : 'btn-outline' }}"
                                title="{{ $autoIssue ? 'الإصدار التلقائي مفعّل — انقر لإيقافه' : 'الإصدار التلقائي موقوف — انقر لتفعيله' }}"
                                style="{{ $autoIssue ? 'background:rgba(52,199,89,0.18);color:#34c759;border:1px solid rgba(52,199,89,0.35);' : '' }}">
                            <i class="ri-toggle-{{ $autoIssue ? 'fill' : 'line' }}" style="font-size:16px;"></i>
                            {{ $autoIssue ? 'إصدار تلقائي: مفعّل' : 'إصدار تلقائي: موقوف' }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Profile Hero -->
            <div class="profile-hero">
                <div class="avatar-wrap">
                    <div class="profile-avatar">
                        @if($student->image)
                            <img src="{{ asset('storage/' . $student->image) }}" alt="{{ $student->name }}">
                        @elseif($systemUser && $systemUser->avatar_url)
                            <img src="{{ asset('storage/' . $systemUser->avatar_url) }}" alt="{{ $student->name }}">
                        @else
                            {{ mb_substr($student->name, 0, 1) }}
                        @endif
                    </div>
                    @if($systemUser)
                        <span class="online-dot"></span>
                    @endif
                </div>
                <div class="profile-info">
                    <h2>{{ $student->name }}</h2>
                    <div class="email">{{ $student->email }}</div>
                    <div class="meta">
                        <span><i class="ri-book-2-line"></i> {{ $student->course }}</span>
                        <span><i class="ri-calendar-line"></i> {{ $student->course_date->format('Y-m-d') }}</span>
                        <span><i class="ri-medal-line"></i> {{ $student->degree }}</span>
                        @if($systemUser)
                            <span class="badge-gold"><i class="ri-checkbox-circle-line"></i> مسجل في النظام</span>
                        @else
                            <span class="badge-gold" style="background:rgba(255,59,48,0.08);color:var(--danger);"><i class="ri-user-unfollow-line"></i> غير مسجل في النظام</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="ri-award-fill"></i></div>
                    <div class="stat-num">{{ $totalCerts }}</div>
                    <div class="stat-lbl">إجمالي الشهادات</div>
                    <div class="stat-sub">{{ $systemCertificates->count() }} نظام · {{ $certTemplates->count() }} مخصصة</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="ri-book-2-fill"></i></div>
                    <div class="stat-num">{{ $totalCourses }}</div>
                    <div class="stat-lbl">المسارات المسجل بها</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="ri-hourglass-line"></i></div>
                    <div class="stat-num">{{ $pendingCerts }}</div>
                    <div class="stat-lbl">شهادات معلقة</div>
                    <div class="stat-sub">مسارات مكتملة دون شهادة</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="ri-pie-chart-2-line"></i></div>
                    <div class="progress-ring-wrap" style="justify-content:center;">
                        <div class="progress-ring">
                            <svg width="70" height="70" viewBox="0 0 70 70">
                                <circle class="circle-bg" cx="35" cy="35" r="28"/>
                                <circle class="circle-fg" cx="35" cy="35" r="28"
                                    stroke-dasharray="176"
                                    stroke-dashoffset="{{ 176 - (176 * $completionRate / 100) }}"/>
                            </svg>
                            <div class="center-text" style="color:var(--gold);">{{ $completionRate }}%</div>
                        </div>
                        <div style="text-align:right;">
                            <div class="stat-lbl">معدل الإنجاز</div>
                            <div class="stat-sub">{{ $systemCertificates->count() }}/{{ $totalCourses }} مسار</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
            <!-- Left: Courses -->
            <div class="card">
                <div class="section-title"><i class="ri-book-2-line"></i> المسارات @if($systemUser)<span style="font-size:12px;color:var(--text-muted);font-weight:400;">({{ $totalCourses }} مسار)</span>@endif</div>

                @if($systemUser && $enrolledCourses->isNotEmpty())
                    <div class="course-list">
                        @foreach($enrolledCourses as $course)
                            @php
                                $hasSystemCert    = $systemCertificates->where('course_id', $course->id)->isNotEmpty();
                                $hasCustomCert    = isset($issuedCustomByCourse[$course->id]);
                                $isPending        = $completedNoCert->contains('id', $course->id);
                                $pct              = $courseCompletion[$course->id] ?? 0;
                                $galleryUrl       = route('teacher.certificates.gallery', $student) . '?course_id=' . $course->id;
                            @endphp
                            <div class="course-item">
                                <div style="flex:1;min-width:0;">
                                    <div class="course-name"><i class="ri-book-2-line"></i> {{ $course->name }}</div>
                                    <div style="font-size:12px;color:var(--text-muted);margin-top:3px;">
                                        {{ $course->lessons_count }} درس
                                        @if($pct > 0)
                                            · <span style="color:{{ $pct >= 100 ? 'var(--success)' : 'var(--warning)' }};">{{ $pct }}% مكتمل</span>
                                        @endif
                                    </div>
                                </div>
                                <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
                                    @if($hasSystemCert)
                                        <span class="course-badge has-cert"><i class="ri-checkbox-circle-fill"></i> شهادة نظام ✓</span>
                                    @endif
                                    @if($hasCustomCert)
                                        @php $issuedTpl = $issuedCustomByCourse[$course->id]; @endphp
                                        <span class="course-badge has-cert"><i class="ri-award-fill"></i> صدرت</span>
                                        <a href="{{ route('teacher.certificates.custom.show', [$student, $issuedTpl]) }}"
                                           class="btn btn-outline btn-sm" style="font-size:11px;padding:4px 10px;">عرض</a>
                                    @elseif($isPending)
                                        <a href="{{ $galleryUrl }}" class="btn btn-primary btn-sm" style="font-size:11px;padding:5px 12px;background:rgba(255,149,0,0.85);color:#fff;">
                                            <i class="ri-award-line"></i> إصدار الآن
                                        </a>
                                    @elseif(!$hasSystemCert)
                                        <a href="{{ $galleryUrl }}" class="btn btn-outline btn-sm" style="font-size:11px;padding:4px 10px;">
                                            <i class="ri-award-line"></i> إصدار شهادة
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif(!$systemUser)
                    <div class="empty-hint">
                        <i class="ri-user-unfollow-line"></i>
                        <p>{{ $student->name }} غير مسجل في النظام — بيانات المسارات غير متوفرة</p>
                        <a href="{{ route('teacher.certificates.gallery', $student) }}" class="btn btn-primary btn-sm" style="margin-top:12px;">
                            <i class="ri-award-line"></i> إصدار شهادة
                        </a>
                    </div>
                @else
                    <div class="empty-hint">
                        <i class="ri-book-open-line"></i>
                        <p>غير مسجل في أي مسار حالياً</p>
                    </div>
                @endif
            </div>

            <!-- Right: Certificates -->
            <div class="card">
                <div class="section-title"><i class="ri-award-fill"></i> الشهادات <span style="font-size:12px;color:var(--text-muted);font-weight:400;">({{ $totalCerts }} شهادة)</span></div>

                @if($systemCertificates->isNotEmpty())
                    <div style="font-size:13px;font-weight:600;color:var(--text-secondary);margin-bottom:10px;">
                        <i class="ri-checkbox-circle-fill" style="color:var(--success);"></i> شهادات النظام ({{ $systemCertificates->count() }})
                    </div>
                    <div class="cert-list">
                        @foreach($systemCertificates as $cert)
                            <div class="cert-item">
                                <div>
                                    <div class="cert-name"><i class="ri-verified-badge-fill" style="color:var(--success);"></i> {{ $cert->course->name ?? 'غير محدد' }}</div>
                                    <div class="cert-meta">{{ $cert->certificate_number }} · {{ $cert->issued_at->format('Y-m-d') }}</div>
                                </div>
                                <div class="cert-meta" style="font-size:11px;">{{ $cert->score }}%</div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($certTemplates->isNotEmpty())
                    <div style="font-size:13px;font-weight:600;color:var(--text-secondary);margin-bottom:10px;{{ $systemCertificates->isNotEmpty() ? 'margin-top:20px;' : '' }}">
                        <i class="ri-pencil-ruler-2-fill" style="color:var(--gold);"></i> شهادات مخصصة ({{ $certTemplates->count() }})
                    </div>
                    <div class="cert-list">
                        @foreach($certTemplates as $tpl)
                            <div class="cert-item">
                                <div>
                                    <div class="cert-name"><i class="ri-file-text-line" style="color:var(--gold);"></i> {{ $tpl->name }}</div>
                                    <div class="cert-meta">{{ $tpl->title }} · {{ $tpl->created_at->format('Y-m-d') }}</div>
                                </div>
                                <div class="cert-actions">
                                    <a href="{{ route('teacher.certificates.custom.show', [$student, $tpl]) }}" class="btn btn-outline btn-sm">عرض</a>
                                    <a href="{{ route('teacher.certificates.custom.download', [$student, $tpl]) }}" class="btn btn-primary btn-sm">PDF</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($systemCertificates->isEmpty() && $certTemplates->isEmpty())
                    <div class="empty-hint">
                        <i class="ri-award-line"></i>
                        <p>لم تصدر أي شهادة لهذا المستفيد بعد</p>
                        <a href="{{ route('teacher.certificates.gallery', $student) }}" class="btn btn-primary" style="margin-top:16px;">
                            <i class="ri-award-line"></i> إصدار شهادة الآن
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pending Certificates Alert -->
        @if($pendingCerts > 0)
            <div class="card" style="margin-top:24px;border-color:rgba(255,149,0,0.2);background:rgba(255,149,0,0.04);">
                <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
                    <div style="width:48px;height:48px;border-radius:50%;background:rgba(255,149,0,0.12);display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0;">
                        <i class="ri-sparkling-2-line" style="color:var(--warning);"></i>
                    </div>
                    <div style="flex:1;">
                        <div style="font-weight:700;font-size:15px;">شهادات معلقة — مسارات مكتملة</div>
                        <div style="font-size:13px;color:var(--text-muted);">
                            هناك <strong style="color:var(--warning);">{{ $pendingCerts }}</strong> مساراً مكتملاً لم تصدر له شهادة بعد.
                            يمكنك إصدار شهادة من معرض القوالب.
                        </div>
                    </div>
                    <a href="{{ route('teacher.certificates.gallery', $student) }}" class="btn btn-primary">
                        <i class="ri-award-line"></i> إصدار الآن
                    </a>
                </div>
            </div>
        @endif
    </div>

    @include('components.account-theme-foot')
</body>
</html>