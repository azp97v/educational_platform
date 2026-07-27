<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    @include('components.account-theme-head')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Rewind | إجلال</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Josefin+Slab:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --gold: #C4963A;
            --dark-gold: #A07A28;
            --light-gold: rgba(196,150,58,0.15);
            --bg-dark: #050505;
            --bg-card: rgba(255,255,255,0.04);
            --bg-card-hover: rgba(255,255,255,0.08);
            --border: rgba(196,150,58,0.25);
            --border-hover: rgba(196,150,58,0.5);
            --text-muted: #888;
            --success: #06a77d;
            --danger: #e53935;
            --warning: #f59e0b;
        }
        html, body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, var(--bg-dark) 0%, #191A1C 100%);
            color: white;
            min-height: 100vh;
        }
        .page-layout {
            display: flex;
            min-height: 100vh;
        }
        .main-content {
            flex: 1;
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        /* ─── Header ─── */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .page-title {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .page-title-icon {
            width: 56px; height: 56px;
            background: linear-gradient(135deg, var(--gold), var(--dark-gold));
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem;
        }
        .page-title h1 {
            font-family: 'Josefin Slab', serif;
            font-size: 1.8rem;
            color: var(--gold);
        }
        .page-title p { font-size: 0.9rem; color: var(--text-muted); margin-top: 2px; }

        /* ─── Stats Bar ─── */
        .stats-bar {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }
        @media (max-width: 768px) { .stats-bar { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 480px) { .stats-bar { grid-template-columns: 1fr 1fr; } }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1.2rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        .stat-card:hover { background: var(--bg-card-hover); transform: translateY(-3px); }
        .stat-card .stat-icon { font-size: 1.6rem; margin-bottom: 0.4rem; }
        .stat-card .stat-value {
            font-size: 1.8rem; font-weight: 700;
            font-family: 'Josefin Slab', serif;
            color: var(--gold);
        }
        .stat-card .stat-label { font-size: 0.8rem; color: var(--text-muted); margin-top: 2px; }
        .stat-card.pending .stat-icon { color: var(--warning); }
        .stat-card.watched .stat-icon { color: #60a5fa; }
        .stat-card.mastered .stat-icon { color: var(--success); }

        /* ─── Filter Tabs ─── */
        .filter-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        .filter-tab {
            padding: 0.5rem 1.2rem;
            border-radius: 50px;
            border: 1px solid var(--border);
            background: transparent;
            color: var(--text-muted);
            font-family: 'Cairo', sans-serif;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .filter-tab:hover { border-color: var(--gold); color: var(--gold); }
        .filter-tab.active { background: var(--gold); border-color: var(--gold); color: #000; font-weight: 600; }

        /* ─── Rewind Cards Grid ─── */
        .rewinds-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.2rem;
        }
        @media (max-width: 480px) { .rewinds-grid { grid-template-columns: 1fr; } }

        .rewind-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.4rem;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
            position: relative;
            overflow: hidden;
        }
        .rewind-card::before {
            content: '';
            position: absolute;
            top: 0; right: 0;
            width: 4px; height: 100%;
            border-radius: 0 16px 16px 0;
        }
        .rewind-card.pending::before { background: var(--warning); }
        .rewind-card.watched::before { background: #60a5fa; }
        .rewind-card.mastered::before { background: var(--success); }
        .rewind-card:hover {
            background: var(--bg-card-hover);
            border-color: var(--border-hover);
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(196,150,58,0.1);
        }

        .card-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.3rem 0.8rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 0.8rem;
        }
        .badge-pending { background: rgba(245,158,11,0.15); color: var(--warning); border: 1px solid rgba(245,158,11,0.3); }
        .badge-watched { background: rgba(96,165,250,0.15); color: #60a5fa; border: 1px solid rgba(96,165,250,0.3); }
        .badge-mastered { background: rgba(6,167,125,0.15); color: var(--success); border: 1px solid rgba(6,167,125,0.3); }

        .card-question {
            font-size: 0.95rem;
            font-weight: 500;
            line-height: 1.5;
            margin-bottom: 0.8rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .card-meta {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 0.8rem;
            color: var(--text-muted);
            flex-wrap: wrap;
        }
        .card-meta-item { display: flex; align-items: center; gap: 0.3rem; }
        .card-meta-item i { color: var(--gold); font-size: 0.9rem; }

        .timestamp-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            background: rgba(196,150,58,0.1);
            border: 1px solid rgba(196,150,58,0.2);
            border-radius: 50px;
            padding: 0.2rem 0.7rem;
            font-size: 0.75rem;
            color: var(--gold);
            margin-top: 0.8rem;
        }

        /* ─── Empty State ─── */
        .empty-state {
            text-align: center;
            padding: 5rem 2rem;
            color: var(--text-muted);
        }
        .empty-state i { font-size: 4rem; color: var(--border); margin-bottom: 1rem; }
        .empty-state h3 { font-size: 1.3rem; color: #555; margin-bottom: 0.5rem; }
        .empty-state p { font-size: 0.9rem; }

        /* ─── Pagination ─── */
        .pagination-wrap {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
            gap: 0.5rem;
        }
        .pagination-wrap a, .pagination-wrap span {
            padding: 0.5rem 0.9rem;
            border-radius: 8px;
            border: 1px solid var(--border);
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.2s;
        }
        .pagination-wrap .active span {
            background: var(--gold);
            border-color: var(--gold);
            color: #000;
            font-weight: 700;
        }
        .pagination-wrap a:hover { border-color: var(--gold); color: var(--gold); }
    </style>
</head>
<body>
<div class="page-layout">
    @include('components.sidebar')

    <div class="main-content">

        {{-- Header --}}
        <div class="page-header">
            <div class="page-title">
                <div class="page-title-icon">⏪</div>
                <div>
                    <h1>Smart Rewind</h1>
                    <p>مراجعة النقاط التي تحتاج تركيزاً أكثر</p>
                </div>
            </div>
        </div>

        {{-- Stats Bar --}}
        <div class="stats-bar">
            <div class="stat-card" data-filter="all" onclick="filterCards('all')">
                <div class="stat-icon">📚</div>
                <div class="stat-value">{{ $stats['total'] }}</div>
                <div class="stat-label">إجمالي</div>
            </div>
            <div class="stat-card pending" data-filter="pending" onclick="filterCards('pending')">
                <div class="stat-icon"><i class="ri-time-line"></i></div>
                <div class="stat-value">{{ $stats['pending'] }}</div>
                <div class="stat-label">بانتظار المراجعة</div>
            </div>
            <div class="stat-card watched" data-filter="watched" onclick="filterCards('watched')">
                <div class="stat-icon"><i class="ri-eye-line"></i></div>
                <div class="stat-value">{{ $stats['watched'] }}</div>
                <div class="stat-label">تمت المشاهدة</div>
            </div>
            <div class="stat-card mastered" data-filter="mastered" onclick="filterCards('mastered')">
                <div class="stat-icon"><i class="ri-award-line"></i></div>
                <div class="stat-value">{{ $stats['mastered'] }}</div>
                <div class="stat-label">تم الإتقان ✨</div>
            </div>
        </div>

        {{-- Filter Tabs --}}
        <div class="filter-tabs">
            <button class="filter-tab active" onclick="filterCards('all')" data-tab="all">الكل</button>
            <button class="filter-tab" onclick="filterCards('pending')" data-tab="pending">⏳ بانتظار المراجعة</button>
            <button class="filter-tab" onclick="filterCards('watched')" data-tab="watched">👁 تمت المشاهدة</button>
            <button class="filter-tab" onclick="filterCards('mastered')" data-tab="mastered">🏆 تم الإتقان</button>
        </div>

        {{-- Cards --}}
        @if($rewinds->isEmpty())
            <div class="empty-state">
                <i class="ri-checkbox-circle-line"></i>
                <h3>لا توجد مراجعات بعد</h3>
                <p>عندما تُخطئ في سؤال، سيُضاف تلقائياً هنا للمراجعة</p>
            </div>
        @else
            <div class="rewinds-grid" id="rewindsGrid">
                @foreach($rewinds as $rewind)
                    <a href="{{ route('student.smart-rewind.show', $rewind) }}"
                       class="rewind-card {{ $rewind->status }}"
                       data-status="{{ $rewind->status }}">

                        <div class="card-badge badge-{{ $rewind->status }}">
                            @if($rewind->status === 'pending') <i class="ri-time-line"></i> بانتظار المراجعة
                            @elseif($rewind->status === 'watched') <i class="ri-eye-line"></i> تمت المشاهدة
                            @else <i class="ri-award-line"></i> تم الإتقان
                            @endif
                        </div>

                        <div class="card-question">
                            {{ $rewind->question->question_text ?? 'سؤال محذوف' }}
                        </div>

                        <div class="card-meta">
                            @if($rewind->exam?->lesson)
                                <span class="card-meta-item">
                                    <i class="ri-book-line"></i>
                                    {{ Str::limit($rewind->exam->lesson->title ?? $rewind->exam->lesson->name, 30) }}
                                </span>
                            @endif
                            @if($rewind->watch_count > 0)
                                <span class="card-meta-item">
                                    <i class="ri-play-circle-line"></i>
                                    شوهد {{ $rewind->watch_count }} مرة
                                </span>
                            @endif
                        </div>

                        @if($rewind->video_timestamp > 0)
                            <div class="timestamp-pill">
                                <i class="ri-time-line"></i>
                                {{ gmdate('i:s', $rewind->video_timestamp) }}
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($rewinds->hasPages())
                <div class="pagination-wrap">
                    {{ $rewinds->links() }}
                </div>
            @endif
        @endif

    </div>
</div>

<script>
function filterCards(status) {
    // Update tab styles
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    document.querySelector(`[data-tab="${status}"]`).classList.add('active');

    // Filter cards
    document.querySelectorAll('.rewind-card').forEach(card => {
        if (status === 'all' || card.dataset.status === status) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}
</script>
</body>
</html>
