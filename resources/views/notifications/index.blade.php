@extends('layouts.master')

@section('title', 'الإشعارات')

@section('content')
    <section class="notifications-page">
        <div class="header-bar">
            @php
                $backRoute = match(auth()->user()->role) {
                    'teacher' => route('teacher.dashboard'),
                    'admin'   => route('admin.index'),
                    default   => route('student.academy'),
                };
                $backText = match(auth()->user()->role) {
                    'teacher' => 'العودة للوحة التحكم',
                    'admin'   => 'العودة للإدارة',
                    default   => 'العودة للأكاديمية',
                };
            @endphp
            <a href="{{ $backRoute }}" class="back-navigation">
                <i class="ri-arrow-left-line"></i>
                {{ $backText }}
            </a>

            <div class="header-actions">
                <form action="{{ route('notifications.readAll') }}" method="POST" class="inline-form">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="ri-checkbox-circle-line"></i>
                        تمييز الكل كمقروء
                    </button>
                </form>
            </div>
        </div>

        <div class="hero-section">
            <div class="hero-content">
                <span class="hero-badge"><i class="ri-notification-3-line"></i> إشعارات</span>
                <h1 class="hero-title">لوحة الإشعارات</h1>
                <p class="hero-description">تابع تحديثات الدورة، الرسائل والعروض المهمة ضمن واجهة مرتبطة بهوية الموقع الحالية.</p>

                <div class="hero-cards">
                    <div class="hero-card">
                        <span>الإجمالي</span>
                        <strong>{{ $notifications->total() }}</strong>
                    </div>
                    <div class="hero-card">
                        <span>غير المقروءة</span>
                        <strong>{{ $unreadCount }}</strong>
                    </div>
                    <div class="hero-card">
                        <span>آخر تحديث</span>
                        <strong>{{ now()->format('d / m / Y') }}</strong>
                    </div>
                </div>

                <div class="hero-buttons">
                    <a href="{{ $backRoute }}" class="btn btn-secondary btn-lg">
                        <i class="ri-arrow-{{ auth()->user()->role === 'teacher' || auth()->user()->role === 'admin' ? 'left' : 'left' }}-line"></i>
                        {{ $backText }}
                    </a>
                    <form action="{{ route('notifications.readAll') }}" method="POST" class="inline-form">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="ri-checkbox-circle-line"></i>
                            تمييز الكل كمقروء
                        </button>
                    </form>
                </div>
            </div>

            <div class="hero-visual">
                <div class="hero-glow"></div>
                <div class="hero-icon">
                    <i class="ri-notification-3-line"></i>
                </div>
            </div>
        </div>

        <div class="main-content">
            <div class="list-panel">
                <div class="section-header">
                    <div>
                        <h2>آخر الإشعارات</h2>
                        <p>عرض مرتّب وواضح لكل الإشعارات مع حالة القراءة والفئة ووقت الاستلام.</p>
                    </div>
                    <div class="notifications-toolbar">
                        <button class="toolbar-btn active" type="button">الكل</button>
                        <button class="toolbar-btn" type="button">غير المقروءة</button>
                        <button class="toolbar-btn" type="button">المقروءة</button>
                    </div>
                </div>

                @if($notifications->count())
                    <div class="notifications-list" id="notifications-list">
                        @foreach($notifications as $notification)
                            @php
                                $data = $notification->data;
                                $iconMap = [
                                    'enrollment' => 'ri-user-add-line',
                                    'inquiry' => 'ri-question-line',
                                    'message' => 'ri-message-2-line',
                                    'system' => 'ri-notification-3-line',
                                    'course' => 'ri-book-open-line',
                                    'exam' => 'ri-survey-line',
                                    'achievement' => 'ri-medal-line',
                                    'general' => 'ri-notification-3-line'
                                ];
                                $fallbackEmoji = [
                                    'enrollment' => '👥',
                                    'inquiry' => '❓',
                                    'message' => '💬',
                                    'system' => '🔔',
                                    'course' => '📖',
                                    'exam' => '📝',
                                    'achievement' => '🏆',
                                    'general' => '📬'
                                ];
                                $category = strtolower($data['category'] ?? 'general');
                                $iconClass = !empty($data['icon']) ? $data['icon'] : ($iconMap[$category] ?? $iconMap['general']);
                                $fallbackEmoji = $fallbackEmoji[$category] ?? $fallbackEmoji['general'];
                            @endphp
                            <a href="{{ route('notifications.goto', $notification->id) }}" class="notification-card {{ is_null($notification->read_at) ? 'unread' : 'read' }}" data-category="{{ $category }}">
                                <div class="notification-icon">
                                    <i class="{{ $iconClass }}" data-fallback="{{ $fallbackEmoji }}"></i>
                                </div>
                                <div class="notification-body">
                                    <div class="notification-head">
                                        <h3>{{ $data['title'] ?? 'تنبيه جديد' }}</h3>
                                        <span>{{ $notification->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p>{{ $data['message'] ?? 'تفاصيل الإشعار غير متوفرة.' }}</p>
                                    <div class="notification-meta">
                                        <span class="meta-badge">{{ ucfirst($data['category'] ?? 'عام') }}</span>
                                        <span class="meta-status">{{ is_null($notification->read_at) ? 'غير مقروء' : 'مقروء' }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="pagination-wrapper">{{ $notifications->fragment('notifications-list')->links() }}</div>
                @else
                    <div class="empty-state">
                        <i class="ri-notification-3-line empty-icon"></i>
                        <h3>لا توجد إشعارات جديدة</h3>
                        <p>ستظهر الإشعارات هنا فور ورود أي تحديث جديد من النظام أو من المعلمين.</p>
                    </div>
                @endif
            </div>

            <aside class="notifications-sidebar">
                <div class="sidebar-widget stats-widget">
                    <div class="widget-header">
                        <h3><i class="ri-bar-chart-line"></i> إحصائيات</h3>
                    </div>
                    <div class="widget-body">
                        <div class="stat-row">
                            <span>الإجمالي:</span>
                            <strong>{{ $notifications->total() }}</strong>
                        </div>
                        <div class="stat-row">
                            <span>غير مقروء:</span>
                            <strong class="unread-count">{{ $unreadCount }}</strong>
                        </div>
                        <div class="stat-row">
                            <span>المعروض:</span>
                            <strong>{{ $notifications->count() }}</strong>
                        </div>
                    </div>
                </div>

                <div class="sidebar-widget settings-widget">
                    <div class="widget-header">
                        <h3><i class="ri-settings-3-line"></i> إعدادات الإشعارات</h3>
                    </div>
                    <div class="widget-body">
                        <form id="notification-preferences-form" class="preferences-form">
                            @csrf
                            <div class="setting-item">
                                <label class="setting-toggle">
                                    <input type="checkbox" name="notify_enrollment" id="notify_enrollment" {{ auth()->user()->notify_enrollment ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </label>
                                <div class="setting-info">
                                    <span class="setting-title">طلبات الالتحاق</span>
                                    <span class="setting-desc">إشعارات جديدة عند قيام الطلاب بطلب الالتحاق</span>
                                </div>
                            </div>
                            <div class="setting-item">
                                <label class="setting-toggle">
                                    <input type="checkbox" name="notify_inquiry" id="notify_inquiry" {{ auth()->user()->notify_inquiry ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </label>
                                <div class="setting-info">
                                    <span class="setting-title">الأسئلة والملاحظات</span>
                                    <span class="setting-desc">إشعارات بأسئلة الطلاب والملاحظات الجديدة</span>
                                </div>
                            </div>
                            <div class="setting-item">
                                <label class="setting-toggle">
                                    <input type="checkbox" name="notify_message" id="notify_message" {{ auth()->user()->notify_message ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </label>
                                <div class="setting-info">
                                    <span class="setting-title">الرسائل الخاصة</span>
                                    <span class="setting-desc">إشعارات برسائل خاصة جديدة</span>
                                </div>
                            </div>
                            <div class="setting-item">
                                <label class="setting-toggle">
                                    <input type="checkbox" name="notify_system" id="notify_system" {{ auth()->user()->notify_system ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </label>
                                <div class="setting-info">
                                    <span class="setting-title">تنبيهات النظام</span>
                                    <span class="setting-desc">إشعارات عامة من النظام والمنصة</span>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="ri-save-line"></i>
                                حفظ التفضيلات
                            </button>
                        </form>
                        <div id="preferences-success" class="alert alert-success" style="display: none; margin-top: 1rem;">
                            <i class="ri-check-circle-line"></i>
                            <span>تم حفظ التفضيلات بنجاح</span>
                        </div>
                    </div>
                </div>

                <div class="sidebar-widget help-widget">
                    <div class="widget-header">
                        <h3><i class="ri-question-line"></i> نصائح</h3>
                    </div>
                    <div class="widget-body">
                        <ul class="help-list">
                            <li>استخدم أزرار التصفية لعرض الإشعارات المحددة</li>
                            <li>الإشعارات غير المقروءة مميزة بلون مختلف</li>
                            <li>يمكنك تخصيص أنواع الإشعارات المطلوبة</li>
                            <li>تصفح السجل الكامل للإشعارات السابقة</li>
                        </ul>
                    </div>
                </div>
            </aside>
        </div>
    </section>
@endsection

@section('styles')
<style>
        :root {
            --gold: var(--color-gold);
            --gold-light: rgba(196, 150, 58, 0.15);
            --surface-1: rgba(255, 255, 255, 0.06);
            --surface-2: rgba(255, 255, 255, 0.08);
            --surface-3: rgba(255, 255, 255, 0.14);
            --border-light: rgba(196, 150, 58, 0.14);
        }
        .notifications-page .btn-primary,
        .notifications-page .btn.btn-primary { background: linear-gradient(135deg, var(--color-gold), var(--color-gold-dark)) !important; border-color: color-mix(in srgb, var(--color-gold-dark) 70%, transparent) !important; color: #111 !important; box-shadow: 0 8px 20px rgba(196, 150, 58, 0.28) !important; }
        .notifications-page .btn-primary:hover,
        .notifications-page .btn.btn-primary:hover { filter: brightness(1.05); background: linear-gradient(135deg, var(--color-gold), var(--color-gold-dark)) !important; border-color: color-mix(in srgb, var(--color-gold-dark) 75%, transparent) !important; color: #111 !important; }
        .notifications-page .btn-secondary,
        .notifications-page .btn.btn-secondary { background: rgba(255, 255, 255, 0.08) !important; border-color: rgba(255, 255, 255, 0.16) !important; color: #fff !important; }
        .notifications-page .btn:focus,
        .notifications-page .btn:focus-visible,
        .notifications-page .toolbar-btn:focus,
        .notifications-page .toolbar-btn:focus-visible,
        .notifications-page .setting-toggle input:focus + .toggle-slider { outline: none !important; box-shadow: 0 0 0 3px color-mix(in srgb, var(--color-gold) 35%, transparent) !important; border-color: color-mix(in srgb, var(--color-gold) 55%, transparent) !important; }
        .notifications-page .alert.alert-success,
        .notifications-page #preferences-success { background: rgba(196, 150, 58, 0.16) !important; border: 1px solid rgba(196, 150, 58, 0.35) !important; color: #f3e2b6 !important; }
        .notifications-page .alert.alert-success i,
        .notifications-page #preferences-success i { color: var(--color-gold) !important; }
        body, .app-container, .app-main, .page-content { background: radial-gradient(circle at top left, color-mix(in srgb, var(--color-gold) 20%, transparent), transparent 22%), linear-gradient(180deg, var(--theme-page-bg) 0%, var(--theme-surface) 40%, var(--theme-page-bg) 100%); }
        .notifications-page { padding: 2rem 1.5rem 4rem; display: grid; gap: 1.75rem; max-width: 1280px; margin: 0 auto; background: transparent; border-radius: 32px; border: none; box-shadow: none; }
        .header-bar { display: flex; justify-content: space-between; align-items: center; gap: 1rem; padding: 1rem 1.25rem; border-radius: 18px; background: linear-gradient(90deg, color-mix(in srgb, var(--theme-surface) 95%, transparent) 0%, color-mix(in srgb, var(--theme-surface-2) 95%, transparent) 100%); border: 1px solid var(--theme-border); box-shadow: 0 18px 46px rgba(0, 0, 0, 0.18); }
        .back-navigation { display: inline-flex; align-items: center; gap: 0.75rem; color: var(--gold); text-decoration: none; font-weight: 700; font-size: 1rem; padding: 0.8rem 1.2rem; border-radius: 12px; transition: all 0.25s ease; background: var(--theme-soft, rgba(255, 255, 255, 0.04)); border: 1px solid var(--theme-border, rgba(255, 255, 255, 0.1)); }
        .back-navigation:hover { background: rgba(196, 150, 58, 0.12); color: #fff; transform: translateY(-1px); }
        .header-actions { display: flex; gap: 0.75rem; flex-wrap: wrap; }
        .hero-section { display: grid; grid-template-columns: 1.3fr 0.9fr; gap: 2rem; align-items: center; padding: 2.4rem; border-radius: 28px; background: linear-gradient(135deg, color-mix(in srgb, var(--theme-surface) 96%, transparent) 0%, color-mix(in srgb, var(--theme-surface-2) 96%, transparent) 100%); border: 1px solid var(--theme-border); box-shadow: 0 26px 64px rgba(0, 0, 0, 0.18); overflow: hidden; }
        .hero-badge { display: inline-flex; align-items: center; gap: 0.65rem; padding: 0.85rem 1.1rem; border-radius: 999px; background: var(--theme-soft, rgba(255, 255, 255, 0.06)); color: var(--gold); font-weight: 700; letter-spacing: 0.02em; border: 1px solid var(--theme-border, rgba(255, 255, 255, 0.1)); }
        .hero-title { font-size: clamp(2.4rem, 4vw, 3.5rem); line-height: 1.05; margin: 1.5rem 0 1rem; color: var(--theme-text, #fff); font-weight: 900; }
        .hero-description { max-width: 640px; color: var(--theme-text-soft, rgba(255, 255, 255, 0.78)); line-height: 1.8; margin-bottom: 2rem; font-size: 1rem; }
        .hero-cards { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; }
        .hero-card { padding: 1.25rem 1.3rem; border-radius: 20px; background: var(--theme-soft, rgba(255, 255, 255, 0.06)); border: 1px solid var(--theme-border); color: var(--theme-text, #fff); }
        .hero-card span { display: block; color: var(--theme-text-soft, rgba(255, 255, 255, 0.7)); margin-bottom: 0.65rem; font-size: 0.95rem; }
        .hero-card strong { font-size: 1.6rem; color: var(--theme-text, #fff); }
        .hero-buttons { display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 1.75rem; }
        .hero-buttons .btn { min-width: 170px; }
        .hero-visual { position: relative; min-height: 320px; display: grid; place-items: center; }
        .hero-glow { position: absolute; inset: 0; background: radial-gradient(circle at 50% 40%, rgba(196, 150, 58, 0.22), transparent 42%); filter: blur(28px); opacity: 0.85; }
        .hero-icon { position: relative; width: 180px; height: 180px; border-radius: 50%; display: grid; place-items: center; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.14); color: var(--gold); font-size: 4rem; box-shadow: 0 18px 40px rgba(0, 0, 0, 0.25); z-index: 1; }
        .main-content { display: grid; grid-template-columns: minmax(0, 1fr) 320px; gap: 1.75rem; align-items: flex-start; }
        .list-panel { display: grid; gap: 1.5rem; }
        .section-header { display: flex; justify-content: space-between; gap: 1rem; align-items: flex-start; margin-bottom: 1.5rem; flex-wrap: wrap; }
        .section-header h2 { font-size: 2rem; margin: 0; color: var(--text-primary); }
        .section-header p { max-width: 700px; margin: 0.6rem 0 0; color: var(--text-secondary); line-height: 1.8; }
        .notifications-toolbar { display: flex; gap: 0.75rem; flex-wrap: wrap; }
        .toolbar-btn { padding: 0.9rem 1.25rem; border-radius: 999px; border: 1px solid rgba(255, 255, 255, 0.12); background: rgba(255, 255, 255, 0.08); color: #fff; font-weight: 700; cursor: pointer; transition: var(--transition-fast); }
        .toolbar-btn:hover, .toolbar-btn.active { border-color: var(--gold); background: rgba(196, 150, 58, 0.18); color: var(--gold); }
        .notifications-list { display: grid; gap: 1rem; scroll-margin-top: 110px; }
        .notification-card { display: grid; grid-template-columns: auto 1fr; gap: 1rem; padding: 1.4rem 1.5rem; border-radius: var(--radius-xl); background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.1); box-shadow: 0 18px 46px rgba(0, 0, 0, 0.18); transition: var(--transition-fast); text-decoration: none; color: #fff; }
        .notification-card:hover { transform: translateY(-2px); border-color: rgba(196, 150, 58, 0.24); }
        .notification-card.unread { background: rgba(255, 255, 255, 0.12); border-color: rgba(196, 150, 58, 0.28); }
        .notification-icon { width: 62px; min-width: 62px; height: 62px; border-radius: 18px; background: rgba(196, 150, 58, 0.15); display: grid; place-items: center; color: var(--gold); font-size: 1.5rem; flex-shrink: 0; z-index: 1; }
        .notification-icon i { font-size: 1.5rem; color: var(--gold); display: block; line-height: 1; }
        .notification-body { display: grid; gap: 0.75rem; }
        .notification-head { display: flex; justify-content: space-between; gap: 1rem; align-items: center; flex-wrap: wrap; }
        .notification-head h3 { margin: 0; font-size: 1.05rem; color: #fff; }
        .notification-head span { color: rgba(255, 255, 255, 0.72); font-size: 0.9rem; }
        .notification-body p { margin: 0; color: rgba(255, 255, 255, 0.78); line-height: 1.75; }
        .notification-meta { display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; }
        .meta-badge,.meta-status { display: inline-flex; align-items: center; padding: 0.55rem 0.9rem; border-radius: 999px; border: 1px solid rgba(255, 255, 255, 0.12); font-size: 0.85rem; font-weight: 700; }
        .meta-badge { color: var(--gold); background: rgba(196, 150, 58, 0.14); }
        .meta-status { color: rgba(255, 255, 255, 0.72); background: rgba(255, 255, 255, 0.08); }
        .pagination-wrapper { margin-top: 1rem; }
        .empty-state { padding: 2.5rem; border-radius: var(--radius-xl); background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.1); display: grid; place-items: center; gap: 1rem; text-align: center; color: rgba(255, 255, 255, 0.82); }
        .empty-icon { font-size: 3.5rem; color: var(--gold); }
        .empty-state h3 { margin: 0; color: #fff; font-size: 1.6rem; }
        footer.app-footer { display: none !important; }
        body .sidebar { display: none !important; }
        .app-main { margin-right: 0 !important; }
        .notifications-sidebar { display: flex; flex-direction: column; gap: 1.5rem; }
        .sidebar-widget { padding: 1.5rem; background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 16px; box-shadow: 0 18px 46px rgba(0, 0, 0, 0.18); color: #fff; }
        .widget-header { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
        .widget-header h3 { margin: 0; font-size: 1rem; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 0.5rem; }
        .widget-header i { color: var(--color-gold); }
        .widget-body { display: flex; flex-direction: column; gap: 0.75rem; }
        .stat-row { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: rgba(255, 255, 255, 0.03); border-radius: 8px; border-left: 3px solid var(--color-gold); }
        .stat-row span { font-size: 0.9rem; color: rgba(255, 255, 255, 0.8); }
        .stat-row strong { font-size: 1.25rem; color: var(--color-gold); font-weight: 800; }
        .setting-item { display: flex; align-items: center; gap: 1rem; padding: 1rem; background: rgba(255, 255, 255, 0.03); border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.08); transition: all 0.25s ease; }
        .setting-toggle { position: relative; display: inline-block; width: 50px; height: 28px; flex-shrink: 0; }
        .setting-toggle input { opacity: 0; width: 0; height: 0; }
        .toggle-slider { position: absolute; top: 0; right: 0; bottom: 0; left: 0; background-color: rgba(255, 255, 255, 0.2); border-radius: 34px; cursor: pointer; transition: all 0.3s ease; border: 1px solid rgba(255, 255, 255, 0.3); }
        .toggle-slider:before { content: ''; position: absolute; height: 22px; width: 22px; right: 3px; bottom: 3px; background-color: #fff; border-radius: 50%; transition: all 0.3s ease; }
        .setting-toggle input:checked + .toggle-slider { background-color: var(--color-gold); border-color: var(--color-gold); }
        .setting-toggle input:checked + .toggle-slider:before { right: 25px; }
        .setting-title { display: block; font-size: 0.95rem; font-weight: 600; color: #fff; margin-bottom: 0.25rem; }
        .setting-desc { display: block; font-size: 0.8rem; color: rgba(255, 255, 255, 0.65); line-height: 1.4; }
        .preferences-form { display: flex; flex-direction: column; gap: 0.75rem; }
        .help-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.5rem; }
        .help-list li { padding: 0.75rem; padding-right: 1rem; background: rgba(196, 150, 58, 0.08); border-radius: 6px; border-right: 3px solid var(--color-gold); font-size: 0.85rem; color: rgba(255, 255, 255, 0.85); line-height: 1.5; }
        @media (max-width: 1024px) { .hero-section,.main-content,.header-bar { grid-template-columns: 1fr; } .hero-visual { min-height: 260px; } .main-content { grid-template-columns: 1fr; } }
        @media (max-width: 768px) { .notifications-page { padding: 1.5rem 1rem 3rem; } .hero-buttons,.header-actions,.notifications-toolbar { justify-content: stretch; width: 100%; } .toolbar-btn,.btn { width: 100%; } }

        /* Light mode surgical contrast fixes (requested areas only) */
        html[data-theme="light"] .back-navigation,
        body[data-theme="light"] .back-navigation {
            background: linear-gradient(135deg, #ffffff 0%, #f8f4ec 100%) !important;
            border-color: #cdb48d !important;
            color: #1f2f46 !important;
            font-weight: 800 !important;
            box-shadow: 0 6px 18px rgba(141, 114, 82, 0.14) !important;
        }

        html[data-theme="light"] .back-navigation i,
        body[data-theme="light"] .back-navigation i {
            color: #8d7252 !important;
        }

        html[data-theme="light"] .back-navigation:hover,
        body[data-theme="light"] .back-navigation:hover {
            background: linear-gradient(135deg, #fffaf1 0%, #f3e7d2 100%) !important;
            border-color: #b99662 !important;
            color: #16253a !important;
        }

        html[data-theme="light"] .hero-buttons .btn.btn-secondary,
        body[data-theme="light"] .hero-buttons .btn.btn-secondary {
            background: linear-gradient(135deg, #ffffff 0%, #f7f2e8 100%) !important;
            border: 1px solid #c8ad83 !important;
            color: #22324a !important;
            box-shadow: 0 6px 18px rgba(141, 114, 82, 0.16) !important;
            font-weight: 800 !important;
        }

        html[data-theme="light"] .hero-buttons .btn.btn-secondary i,
        body[data-theme="light"] .hero-buttons .btn.btn-secondary i {
            color: #8d7252 !important;
        }

        html[data-theme="light"] .hero-buttons .btn.btn-secondary:hover,
        body[data-theme="light"] .hero-buttons .btn.btn-secondary:hover {
            background: linear-gradient(135deg, #fffaf1 0%, #f1e3ca 100%) !important;
            border-color: #b99662 !important;
            color: #16253a !important;
        }

        html[data-theme="light"] .notifications-toolbar .toolbar-btn,
        body[data-theme="light"] .notifications-toolbar .toolbar-btn {
            background: #ffffff !important;
            border-color: #d7dee8 !important;
            color: #334155 !important;
        }

        html[data-theme="light"] .notifications-toolbar .toolbar-btn.active,
        html[data-theme="light"] .notifications-toolbar .toolbar-btn:hover,
        body[data-theme="light"] .notifications-toolbar .toolbar-btn.active,
        body[data-theme="light"] .notifications-toolbar .toolbar-btn:hover {
            background: #f5efe4 !important;
            border-color: #c6a675 !important;
            color: #8d7252 !important;
        }

        html[data-theme="light"] .notifications-sidebar .sidebar-widget,
        body[data-theme="light"] .notifications-sidebar .sidebar-widget {
            background: #ffffff !important;
            border-color: #d7dee8 !important;
            color: #1f2f46 !important;
        }

        html[data-theme="light"] .notifications-sidebar .widget-header,
        body[data-theme="light"] .notifications-sidebar .widget-header {
            border-bottom-color: #e5ebf2 !important;
        }

        html[data-theme="light"] .notifications-sidebar .widget-header h3,
        html[data-theme="light"] .notifications-sidebar .setting-title,
        html[data-theme="light"] .notifications-sidebar .stat-row span,
        html[data-theme="light"] .notifications-sidebar .help-list li,
        body[data-theme="light"] .notifications-sidebar .widget-header h3,
        body[data-theme="light"] .notifications-sidebar .setting-title,
        body[data-theme="light"] .notifications-sidebar .stat-row span,
        body[data-theme="light"] .notifications-sidebar .help-list li {
            color: #334155 !important;
        }

        html[data-theme="light"] .notifications-sidebar .widget-header h3,
        body[data-theme="light"] .notifications-sidebar .widget-header h3 {
            font-weight: 800 !important;
        }

        html[data-theme="light"] .notifications-sidebar .setting-desc,
        body[data-theme="light"] .notifications-sidebar .setting-desc {
            color: #64748b !important;
        }

        html[data-theme="light"] .notifications-sidebar .stat-row,
        html[data-theme="light"] .notifications-sidebar .setting-item,
        html[data-theme="light"] .notifications-sidebar .help-list li,
        body[data-theme="light"] .notifications-sidebar .stat-row,
        body[data-theme="light"] .notifications-sidebar .setting-item,
        body[data-theme="light"] .notifications-sidebar .help-list li {
            background: #f8fafc !important;
            border-color: #e2e8f0 !important;
        }

        html[data-theme="light"] .notifications-sidebar .help-list li,
        body[data-theme="light"] .notifications-sidebar .help-list li {
            background: linear-gradient(135deg, #ffffff 0%, #f8f5ef 100%) !important;
            border-right-color: #c6a675 !important;
            color: #1f2f46 !important;
            font-weight: 700 !important;
            letter-spacing: 0.01em;
        }

        html[data-theme="light"] .notification-card,
        body[data-theme="light"] .notification-card {
            background: #ffffff !important;
            border-color: #d7dee8 !important;
            color: #1f2f46 !important;
        }

        html[data-theme="light"] .notification-card.unread,
        body[data-theme="light"] .notification-card.unread {
            background: #fffaf1 !important;
            border-color: #d9c099 !important;
        }

        html[data-theme="light"] .notification-head h3,
        html[data-theme="light"] .notification-body p,
        html[data-theme="light"] .notification-head span,
        html[data-theme="light"] .meta-status,
        body[data-theme="light"] .notification-head h3,
        body[data-theme="light"] .notification-body p,
        body[data-theme="light"] .notification-head span,
        body[data-theme="light"] .meta-status {
            color: #334155 !important;
        }

        html[data-theme="light"] .meta-status,
        body[data-theme="light"] .meta-status {
            background: #f1f5f9 !important;
            border-color: #d7dee8 !important;
        }
    </style>
@endsection

@section('scripts')
<script>
window.addEventListener('load', function() {
  const icons = document.querySelectorAll('.notification-icon i');
  icons.forEach((icon) => {
    setTimeout(() => {
      const rect = icon.getBoundingClientRect();
      if (rect.width === 0 || icon.textContent === '') {
        const fallback = icon.getAttribute('data-fallback') || '📬';
        icon.textContent = fallback;
        icon.style.fontFamily = 'inherit';
        icon.style.fontSize = '1.8rem';
      }
    }, 100);
  });
});
function setupNotificationFilters() {
  const filterButtons = document.querySelectorAll('.notifications-toolbar .toolbar-btn');
  const notificationCards = document.querySelectorAll('.notification-card');
  if (!filterButtons.length || !notificationCards.length) return;
  filterButtons.forEach(button => {
    button.addEventListener('click', function () {
      filterButtons.forEach(btn => btn.classList.remove('active'));
      button.classList.add('active');
      const filter = button.textContent.trim();
      notificationCards.forEach(card => {
        const isUnread = card.classList.contains('unread');
        if (filter === 'الكل') card.style.display = '';
        else if (filter === 'غير المقروءة') card.style.display = isUnread ? '' : 'none';
        else if (filter === 'المقروءة') card.style.display = isUnread ? 'none' : '';
      });
    });
  });
}
function setupPreferencesForm() {
  const form = document.getElementById('notification-preferences-form');
  const successMessage = document.getElementById('preferences-success');
  if (!form) return;
  form.addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(form);
    const data = {
      notify_enrollment: !!formData.get('notify_enrollment'),
      notify_inquiry: !!formData.get('notify_inquiry'),
      notify_message: !!formData.get('notify_message'),
      notify_system: !!formData.get('notify_system'),
    };
    try {
      const response = await fetch('{{ route('notifications.preferences') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json',
        },
        body: JSON.stringify(data),
      });
      const result = await response.json();
      if (result.success) {
        successMessage.style.display = 'block';
        setTimeout(() => { successMessage.style.display = 'none'; }, 3000);
      }
    } catch (error) {
      console.error('Error saving preferences:', error);
    }
  });
}
document.addEventListener('DOMContentLoaded', function() {
  setupNotificationFilters();
  setupPreferencesForm();
});
</script>
@endsection
