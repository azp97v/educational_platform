@php
    $referrerUrl = $referrer ?? url()->previous(route('student.index'));
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@if($failed) خطأ في توليد PDF @else جاري تحضير شهادتك @endif — إجلال</title>
    @include('components.account-theme-head')
    <style>
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(circle at 14% 8%, rgba(198, 166, 117, 0.24) 0%, transparent 34%),
                radial-gradient(circle at 88% 84%, rgba(198, 166, 117, 0.12) 0%, transparent 38%);
            pointer-events: none;
            z-index: 0;
        }

        .pdf-wait-overlay {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            padding: 20px;
            z-index: 10;
        }

        .pdf-wait-card {
            background: var(--panel-1, var(--card-bg, rgba(255,255,255,0.05)));
            border: 1px solid var(--border, rgba(255,255,255,0.1));
            border-radius: 24px;
            padding: 48px 40px;
            max-width: 460px;
            width: 100%;
            text-align: center;
            box-shadow: 0 24px 64px rgba(0,0,0,0.35);
            backdrop-filter: blur(12px);
        }

        /* أيقونة الحالة */
        .pdf-status-icon {
            width: 88px; height: 88px;
            border-radius: 22px;
            margin: 0 auto 24px;
            display: flex; align-items: center; justify-content: center;
            font-size: 2.4rem;
        }
        .pdf-status-icon.is-loading {
            background: linear-gradient(135deg, var(--gold, #C6A675) 0%, #997722 100%);
            animation: pdf-pulse 1.8s ease-in-out infinite;
        }
        .pdf-status-icon.is-done    { background: linear-gradient(135deg, #22c55e, #16a34a); }
        .pdf-status-icon.is-error   { background: linear-gradient(135deg, #ef4444, #b91c1c); }

        @keyframes pdf-pulse {
            0%,100% { opacity:1; transform:scale(1); }
            50%      { opacity:.75; transform:scale(.93); }
        }

        /* العناوين */
        .pdf-wait-title {
            font-size: 1.45rem; font-weight: 800;
            color: var(--text, #f1f5f9);
            margin-bottom: 8px; line-height: 1.3;
        }
        .pdf-wait-sub {
            font-size: .93rem;
            color: var(--text-muted, var(--muted, #94a3b8));
            margin-bottom: 26px; line-height: 1.65;
        }

        /* شريط التقدم */
        .pdf-prog-wrap {
            background: var(--panel-2, rgba(255,255,255,0.07));
            border-radius: 999px; height: 10px;
            overflow: hidden; margin-bottom: 10px;
        }
        .pdf-prog-fill {
            height: 100%; border-radius: 999px;
            background: linear-gradient(90deg, var(--gold, #C6A675) 0%, #f0c070 100%);
            transition: width .6s cubic-bezier(.4,0,.2,1);
        }
        .pdf-prog-label {
            font-size: .8rem; font-weight: 600;
            color: var(--text-muted, #94a3b8);
            margin-bottom: 24px;
        }

        /* النقاط المتحركة */
        .pdf-dots { display:flex; justify-content:center; gap:8px; margin-bottom:26px; }
        .pdf-dot  { width:8px; height:8px; border-radius:50%; background:var(--panel-2,rgba(255,255,255,.12)); }
        .pdf-dot.is-active { background:var(--gold,#C6A675); animation:pdf-blink 1s ease-in-out infinite; }
        @keyframes pdf-blink { 0%,100%{opacity:1} 50%{opacity:.25} }

        /* الأزرار */
        .pdf-btn-download {
            display: inline-flex; align-items: center; gap: 10px;
            background: linear-gradient(135deg, var(--gold,#C6A675) 0%, #997722 100%);
            color: #fff; font-weight: 700; font-size: .98rem;
            padding: 13px 30px; border-radius: 14px;
            text-decoration: none; transition: .18s;
            border: none; cursor: pointer;
        }
        .pdf-btn-download:hover { opacity: .85; transform: translateY(-1px); }

        .pdf-btn-back {
            display: inline-flex; align-items: center; gap: 8px;
            color: var(--text-muted, #94a3b8); font-size: .88rem;
            text-decoration: none; margin-top: 16px; transition: .18s;
        }
        .pdf-btn-back:hover { color: var(--text, #f1f5f9); }

        /* رسالة الخطأ */
        .pdf-error-box {
            background: rgba(239,68,68,.1);
            border: 1px solid rgba(239,68,68,.3);
            border-radius: 12px; padding: 12px 14px;
            color: #f87171; font-size: .875rem;
            margin-bottom: 20px; text-align: right;
            word-break: break-word;
        }
    </style>
</head>
<body>
    <div class="pdf-wait-overlay">
        <div class="pdf-wait-card">

            @if($failed)
                {{-- ─── حالة الفشل ─── --}}
                <div class="pdf-status-icon is-error">❌</div>
                <div class="pdf-wait-title">فشل توليد PDF</div>
                <div class="pdf-wait-sub">حدث خطأ أثناء إنشاء ملف الشهادة.<br>يرجى المحاولة مرة أخرى.</div>
                @if($gen->error_message)
                    <div class="pdf-error-box">{{ $gen->error_message }}</div>
                @endif
                <a href="{{ $referrerUrl }}" class="pdf-btn-download" style="background:linear-gradient(135deg,var(--panel-2,#475569),var(--muted,#334155));">
                    <i class="ri-arrow-right-line"></i>
                    العودة
                </a>

            @elseif($gen->isDone())
                {{-- ─── جاهز للتنزيل ─── --}}
                <div class="pdf-status-icon is-done">✅</div>
                <div class="pdf-wait-title">شهادتك جاهزة!</div>
                <div class="pdf-wait-sub">تم توليد ملف PDF بنجاح.<br>اضغط الزر أدناه لتنزيله.</div>
                <div class="pdf-prog-wrap"><div class="pdf-prog-fill" style="width:100%;"></div></div>
                <div class="pdf-prog-label">اكتمل ✓</div>
                <a href="{{ route('pdf.download', ['token' => $token]) }}" class="pdf-btn-download">
                    <i class="ri-download-line"></i> تنزيل PDF
                </a>
                <br>
                <a href="{{ $referrerUrl }}" class="pdf-btn-back">
                    <i class="ri-arrow-right-line"></i> العودة
                </a>

            @else
                {{-- ─── جاري التوليد ─── --}}
                <div class="pdf-status-icon is-loading" id="pdfIcon">📄</div>
                <div class="pdf-wait-title" id="waitTitle">جاري تحضير شهادتك</div>
                <div class="pdf-wait-sub" id="waitSub">
                    نعمل على توليد ملف PDF عالي الجودة.<br>
                    هذا يستغرق بضع ثوان فقط...
                </div>
                <div class="pdf-prog-wrap">
                    <div class="pdf-prog-fill" id="progressFill" style="width:{{ $gen->progressPercent() }}%;"></div>
                </div>
                <div class="pdf-prog-label" id="progressLabel">{{ $gen->progressPercent() }}%</div>
                <div class="pdf-dots">
                    <div class="pdf-dot is-active" id="dot1"></div>
                    <div class="pdf-dot" id="dot2"></div>
                    <div class="pdf-dot" id="dot3"></div>
                </div>

                <div id="dlWrap" style="display:none;">
                    <a href="#" class="pdf-btn-download" id="dlBtn">
                        <i class="ri-download-line"></i> تنزيل PDF
                    </a>
                    <br>
                </div>

                <a href="{{ $referrerUrl }}" class="pdf-btn-back" id="backLink">
                    <i class="ri-arrow-right-line"></i> العودة
                </a>
            @endif

        </div>
    </div>

    @if(!$failed && !$gen->isDone())
    <script>
        const TOKEN      = @json($token);
        const STATUS_URL = @json(route('pdf.status'));
        const INTERVAL   = 2000; // 2 ثانية
        let dotIdx = 0;

        const E = {
            icon  : document.getElementById('pdfIcon'),
            title : document.getElementById('waitTitle'),
            sub   : document.getElementById('waitSub'),
            fill  : document.getElementById('progressFill'),
            label : document.getElementById('progressLabel'),
            dlWrap: document.getElementById('dlWrap'),
            dlBtn : document.getElementById('dlBtn'),
            back  : document.getElementById('backLink'),
            dots  : [document.getElementById('dot1'),document.getElementById('dot2'),document.getElementById('dot3')],
        };

        function animateDots() {
            E.dots.forEach((d, i) => d.classList.toggle('is-active', i === dotIdx));
            dotIdx = (dotIdx + 1) % 3;
        }

        async function poll() {
            try {
                const r    = await fetch(STATUS_URL + '?token=' + encodeURIComponent(TOKEN));
                const data = await r.json();

                // تحديث شريط التقدم
                const pct = data.progress || 0;
                E.fill.style.width  = pct + '%';
                E.label.textContent = pct + '%';
                animateDots();

                if (data.status === 'done' && data.download_url) {
                    E.icon.textContent  = '✅';
                    E.icon.className    = 'pdf-status-icon is-done';
                    E.title.textContent = 'شهادتك جاهزة!';
                    E.sub.textContent   = 'تم التوليد بنجاح. سيبدأ التنزيل تلقائياً...';
                    E.fill.style.width  = '100%';
                    E.label.textContent = 'اكتمل ✓';
                    E.dlBtn.href        = data.download_url;
                    E.dlWrap.style.display = 'block';
                    // تنزيل تلقائي
                    setTimeout(() => { window.location.href = data.download_url; }, 1200);
                    return;
                }

                if (data.status === 'failed') {
                    E.icon.textContent  = '❌';
                    E.icon.className    = 'pdf-status-icon is-error';
                    E.title.textContent = 'فشل توليد PDF';
                    E.sub.innerHTML     = (data.error || 'حدث خطأ. يرجى المحاولة مرة أخرى.');
                    E.fill.style.width  = '0%';
                    return;
                }

                // إذا كان processing أو pending — نستمر
                setTimeout(poll, INTERVAL);

            } catch (err) {
                console.warn('[PDF] Poll error:', err);
                setTimeout(poll, INTERVAL * 3);
            }
        }

        // ابدأ الـ polling بعد 2 ثانية (لإعطاء الـ Job وقت)
        setTimeout(poll, 2000);
    </script>
    @endif

    @include('components.account-theme-foot')
</body>
</html>
