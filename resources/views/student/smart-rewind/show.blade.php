<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    @include('components.account-theme-head')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Smart Rewind | {{ Str::limit($rewind->question->question_text ?? 'مراجعة', 40) }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Josefin+Slab:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --gold: #C4963A;
            --dark-gold: #A07A28;
            --bg-dark: #050505;
            --bg-card: rgba(255,255,255,0.04);
            --bg-card-hover: rgba(255,255,255,0.08);
            --border: rgba(196,150,58,0.25);
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
        .page-layout { display: flex; min-height: 100vh; }
        .main-content {
            flex: 1;
            padding: 2rem;
            max-width: 900px;
            margin: 0 auto;
            width: 100%;
        }

        /* ─── Back Button ─── */
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            transition: color 0.2s;
        }
        .back-btn:hover { color: var(--gold); }

        /* ─── Status Banner ─── */
        .status-banner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 0.8rem;
        }
        .status-banner.pending { background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.3); }
        .status-banner.watched { background: rgba(96,165,250,0.1); border: 1px solid rgba(96,165,250,0.3); }
        .status-banner.mastered { background: rgba(6,167,125,0.1); border: 1px solid rgba(6,167,125,0.3); }

        .status-text { font-size: 0.9rem; font-weight: 600; }
        .status-banner.pending .status-text { color: var(--warning); }
        .status-banner.watched .status-text { color: #60a5fa; }
        .status-banner.mastered .status-text { color: var(--success); }

        /* ─── Question Card ─── */
        .question-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 1.5rem;
        }
        .section-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--gold);
            margin-bottom: 0.8rem;
            font-weight: 600;
        }
        .question-text {
            font-size: 1.15rem;
            font-weight: 600;
            line-height: 1.7;
            margin-bottom: 1.5rem;
        }

        /* ─── Answers ─── */
        .answers-list { display: flex; flex-direction: column; gap: 0.7rem; }
        .answer-item {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.9rem 1.2rem;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: rgba(255,255,255,0.02);
            font-size: 0.95rem;
        }
        .answer-item.correct {
            background: rgba(6,167,125,0.1);
            border-color: rgba(6,167,125,0.4);
            color: #4ade80;
        }
        .answer-item.correct .answer-icon { color: var(--success); }
        .answer-item .answer-icon { font-size: 1.1rem; flex-shrink: 0; color: var(--text-muted); }

        /* ─── Explanation Card ─── */
        .explanation-card {
            background: rgba(196,150,58,0.05);
            border: 1px solid rgba(196,150,58,0.2);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .explanation-text {
            font-size: 0.95rem;
            line-height: 1.8;
            color: #e0d5c0;
        }

        /* ─── Video Timestamp Card ─── */
        .video-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .timestamp-display {
            font-family: 'Josefin Slab', serif;
            font-size: 3rem;
            color: var(--gold);
            margin: 0.5rem 0;
        }
        .timestamp-sub { font-size: 0.85rem; color: var(--text-muted); }

        /* ─── Action Buttons ─── */
        .actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 2rem;
        }
        .btn {
            flex: 1;
            min-width: 140px;
            padding: 0.9rem 1.5rem;
            border-radius: 12px;
            border: none;
            font-family: 'Cairo', sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s;
            text-decoration: none;
        }
        .btn-watch {
            background: rgba(96,165,250,0.15);
            border: 1px solid rgba(96,165,250,0.4);
            color: #60a5fa;
        }
        .btn-watch:hover { background: rgba(96,165,250,0.25); transform: translateY(-2px); }
        .btn-mastered {
            background: linear-gradient(135deg, var(--gold), var(--dark-gold));
            color: #000;
        }
        .btn-mastered:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(196,150,58,0.3); }
        .btn-mastered:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
        .btn-back {
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border);
            color: var(--text-muted);
        }
        .btn-back:hover { background: rgba(255,255,255,0.1); color: white; }

        /* ─── Related ─── */
        .related-section { margin-top: 2rem; }
        .related-section h3 { font-size: 1rem; color: var(--text-muted); margin-bottom: 1rem; }
        .related-list { display: flex; flex-direction: column; gap: 0.6rem; }
        .related-item {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.9rem 1.2rem;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 10px;
            text-decoration: none;
            color: inherit;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        .related-item:hover { background: var(--bg-card-hover); border-color: var(--gold); }
        .related-badge {
            width: 8px; height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .badge-pending { background: var(--warning); }
        .badge-watched { background: #60a5fa; }

        /* ─── Toast ─── */
        .toast {
            position: fixed;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: #1a1a1a;
            border: 1px solid var(--gold);
            border-radius: 12px;
            padding: 0.9rem 1.5rem;
            font-size: 0.9rem;
            z-index: 1000;
            transition: transform 0.4s cubic-bezier(.34,1.56,.64,1);
            white-space: nowrap;
        }
        .toast.show { transform: translateX(-50%) translateY(0); }
        .toast.success { border-color: var(--success); color: #4ade80; }
        .toast.error { border-color: var(--danger); color: #f87171; }
    </style>
</head>
<body>
<div class="page-layout">
    @include('components.sidebar')

    <div class="main-content">

        <a href="{{ route('student.smart-rewind.index') }}" class="back-btn">
            <i class="ri-arrow-right-line"></i> العودة إلى قائمة المراجعات
        </a>

        {{-- Status Banner --}}
        <div class="status-banner {{ $rewind->status }}">
            <div class="status-text">
                @if($rewind->status === 'pending')
                    ⏳ بانتظار المراجعة — شاهد الفيديو وأتقن النقطة
                @elseif($rewind->status === 'watched')
                    👁 تمت المشاهدة {{ $rewind->watch_count }} {{ $rewind->watch_count == 1 ? 'مرة' : 'مرات' }} — هل أتقنت النقطة؟
                @else
                    🏆 تم الإتقان! أحسنت — +5 نقاط مكسوبة
                @endif
            </div>
            @if($rewind->status !== 'mastered')
                <span style="font-size:0.8rem;color:var(--text-muted);">
                    شوهد {{ $rewind->watch_count }} {{ $rewind->watch_count == 1 ? 'مرة' : 'مرات' }}
                </span>
            @endif
        </div>

        {{-- Question --}}
        <div class="question-card">
            <div class="section-label">السؤال</div>
            <div class="question-text">{{ $rewind->question->question_text ?? 'السؤال غير متاح' }}</div>

            @if($rewind->question?->answers && $rewind->question->answers->count() > 0)
                <div class="section-label" style="margin-top:1rem;">الإجابات</div>
                <div class="answers-list">
                    @foreach($rewind->question->answers as $answer)
                        <div class="answer-item {{ $answer->is_correct ? 'correct' : '' }}">
                            <i class="answer-icon ri-{{ $answer->is_correct ? 'checkbox-circle-fill' : 'checkbox-blank-circle-line' }}"></i>
                            {{ $answer->answer_text }}
                            @if($answer->is_correct)
                                <span style="margin-right:auto;font-size:0.75rem;opacity:0.7;">✓ الإجابة الصحيحة</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Explanation --}}
        <div class="explanation-card">
            <div class="section-label">الشرح</div>
            <div class="explanation-text">{{ $rewind->explanation }}</div>
        </div>

        {{-- Video Timestamp --}}
        @if($rewind->video_timestamp > 0)
            <div class="video-card">
                <div class="section-label">الجزء المطلوب مراجعته في الفيديو</div>
                <div class="timestamp-display">{{ gmdate('i:s', $rewind->video_timestamp) }}</div>
                <div class="timestamp-sub">انتقل إلى هذه اللحظة في الفيديو لمراجعة الشرح</div>
                @if($rewind->exam?->lesson)
                    <a href="{{ route('student.lesson.show', $rewind->exam->lesson->id) }}?t={{ $rewind->video_timestamp }}"
                       class="btn btn-watch" style="margin: 1rem auto 0; max-width: 250px;" id="watchBtn">
                        <i class="ri-play-circle-line"></i>
                        افتح الدرس عند هذه اللحظة
                    </a>
                @endif
            </div>
        @endif

        {{-- Action Buttons --}}
        <div class="actions">
            @if($rewind->video_timestamp > 0 && $rewind->status === 'pending')
                <button class="btn btn-watch" onclick="recordWatch()">
                    <i class="ri-eye-line"></i> تسجيل المشاهدة
                </button>
            @endif

            <button class="btn btn-mastered"
                    id="masteredBtn"
                    onclick="markMastered()"
                    {{ $rewind->status === 'mastered' ? 'disabled' : '' }}>
                <i class="ri-award-line"></i>
                {{ $rewind->status === 'mastered' ? 'تم الإتقان ✓' : 'أتقنت هذه النقطة! +5 نقاط' }}
            </button>

            <a href="{{ route('student.smart-rewind.index') }}" class="btn btn-back">
                <i class="ri-arrow-right-line"></i> العودة
            </a>
        </div>

        {{-- Related Rewinds --}}
        @if($related->isNotEmpty())
            <div class="related-section">
                <h3>مراجعات أخرى من نفس الاختبار</h3>
                <div class="related-list">
                    @foreach($related as $rel)
                        <a href="{{ route('student.smart-rewind.show', $rel) }}" class="related-item">
                            <div class="related-badge badge-{{ $rel->status }}"></div>
                            <span>{{ Str::limit($rel->question->question_text ?? 'سؤال', 70) }}</span>
                            @if($rel->video_timestamp > 0)
                                <span style="margin-right:auto;font-size:0.75rem;color:var(--gold);">
                                    {{ gmdate('i:s', $rel->video_timestamp) }}
                                </span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>

<div class="toast" id="toast"></div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const rewindId = {{ $rewind->id }};

function showToast(msg, type = 'success') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = `toast ${type} show`;
    setTimeout(() => t.classList.remove('show'), 3000);
}

async function recordWatch() {
    try {
        const r = await fetch(`/student/smart-rewind/${rewindId}/watch`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        const data = await r.json();
        if (data.success) {
            showToast('✓ تم تسجيل المشاهدة');
            setTimeout(() => location.reload(), 1200);
        }
    } catch (e) {
        showToast('حدث خطأ', 'error');
    }
}

async function markMastered() {
    const btn = document.getElementById('masteredBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="ri-loader-line"></i> جاري الحفظ...';
    try {
        const r = await fetch(`/student/smart-rewind/${rewindId}/mastered`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        const data = await r.json();
        if (data.success) {
            showToast(data.already ? 'تم الإتقان مسبقاً' : '🏆 أتقنت النقطة! +5 نقاط');
            btn.innerHTML = '<i class="ri-award-line"></i> تم الإتقان ✓';
            setTimeout(() => location.reload(), 1500);
        }
    } catch (e) {
        btn.disabled = false;
        btn.innerHTML = '<i class="ri-award-line"></i> أتقنت هذه النقطة! +5 نقاط';
        showToast('حدث خطأ', 'error');
    }
}
</script>
</body>
</html>
