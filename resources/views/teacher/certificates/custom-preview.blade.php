<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $template->name }}</title>
    @if(!($forPdf ?? false))
        @include('components.account-theme-head')
        {{-- Google Fonts: فقط في وضع المعاينة، لا في PDF لأن mPDF يحاول تنزيلها --}}
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    @endif
    <style>
        @page { 
            margin: 0; 
            @if(($forPdf ?? false) && ($pdfBgImage ?? null))
            background-image: url("{{ $pdfBgImage }}");
            background-image-resize: 6;
            @endif
        }
        * { box-sizing: border-box; }
        body { 
            margin: 0; 
            background: {{ ($forPdf ?? false) ? '#ffffff' : 'var(--theme-page-bg)' }}; 
            font-family: 'Tajawal', sans-serif; 
            color: {{ ($forPdf ?? false) ? '#000000' : 'var(--text-primary)' }}; 
            @if(!($forPdf ?? false))
            min-height: 100vh; padding: 20px; 
            @else
            overflow: hidden; padding: 0;
            @endif
        }
        .btn-row { display: flex; justify-content: center; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 20px; border-radius: 12px; font-weight: 700;
            font-size: 14px; text-decoration: none; cursor: pointer;
            border: none; transition: 0.3s; font-family: 'Tajawal', sans-serif;
        }
        .btn-primary { background: #C6A675; color: #000; }
        .btn-primary:hover { background: #997722; }
        .btn-outline { background: rgba(0,0,0,0.04); color: #5E6675; border: 1px solid #DFE5EC; }
        .btn-outline:hover { background: rgba(198,166,117,0.16); color: #222B3D; }
        .btn-issue { background: linear-gradient(135deg, #34C759, #2FA84F); color: #fff; }
        .btn-issue:hover { background: linear-gradient(135deg, #2FA84F, #248A41); }
        .issued-badge { display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; border-radius: 12px; font-weight: 700; font-size: 14px; background: rgba(52,199,89,0.12); color: #34C759; border: 1px solid rgba(52,199,89,0.3); }
        .alert-success { background: rgba(52,199,89,0.12); border: 1px solid rgba(52,199,89,0.3); color: #34C759; padding: 10px 16px; border-radius: 10px; font-weight: 600; margin-bottom: 12px; text-align: center; }

        .cert-card {
            position: relative; 
            @if(!($forPdf ?? false))
            max-width: 1000px; margin: 0 auto; min-height: 620px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.4);
            border-radius: 30px; overflow: hidden;
            border: 4px solid rgba(196,150,58,0.55);
            @else
            width: 100%; height: 100%; margin: 0; padding: 0;
            border: none;
            @endif
        }
        .cert-card .background-layer {
            position: absolute; top: 0; right: 0; bottom: 0; left: 0; z-index: 0;
            background-position: center; background-repeat: no-repeat;
        }
        .cert-card .overlay {
            position: absolute; top: 0; right: 0; bottom: 0; left: 0; z-index: 1;
            background: rgba(0,0,0,0.08); pointer-events: none;
        }
        .cert-inner {
            position: relative; z-index: 2;
            padding: 48px 56px; 
            color: white;
            @if(!($forPdf ?? false))
            display: flex; flex-direction: column;
            justify-content: space-between;
            min-height: 620px;
            @else
            height: 100%;
            @endif
        }
        .stamp {
            position: absolute; top: 36px; left: 36px;
            border-radius: 50%;
            border: 3px solid rgba(255,255,255,0.7);
            display: grid; place-items: center;
            font-weight: bold; font-size: 0.95rem;
            background: rgba(255,255,255,0.12);
        }
        .logo-box {
            border-radius: 24px; background: rgba(255,255,255,0.16);
            display: grid; place-items: center; padding: 12px;
            margin-bottom: 20px;
        }
        .logo-box img { max-width: 90px; max-height: 90px; object-fit: contain; }
        .text-block {
            position: absolute; 
            width: 78%; 
            left: 50%; right: auto;
            margin-left: -39%; /* mPDF fallback for translateX(-50%) */
            z-index: 3;
            white-space: pre-wrap; 
            word-break: break-word;
        }
        .title { font-size: 2.4rem; font-weight: 800; margin-bottom: 0.35rem; }
        .subtitle { font-size: 1.1rem; opacity: 0.92; }
        .student-name { font-size: 2rem; font-weight: 700; margin: 16px 0 8px; border-bottom: 2px solid rgba(255,255,255,0.55); padding-bottom: 8px; display: inline-block; }
        .body-text { font-size: 1rem; line-height: 1.8; max-width: 720px; margin-top: 10px; }
        .footer { 
            @if(!($forPdf ?? false))
            display: flex; justify-content: space-between; align-items: center; 
            margin-top: 40px; 
            @else
            position: absolute; bottom: 48px; left: 56px; right: 56px;
            @endif
            border-top: 1px solid rgba(255,255,255,0.35); padding-top: 16px; 
        }
        @if($forPdf ?? false)
        .footer > div { display: inline-block; width: 32%; text-align: center; font-size: 0.9rem; }
        @else
        .footer > div { font-size: 0.9rem; }
        @endif
        .pdf-bg-img { display: block; position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: 0; }
    </style>
</head>
<body>
    @if(($preview ?? true))
        @if(session('success'))
            <div class="alert-success"><i class="ri-checkbox-circle-line"></i> {{ session('success') }}</div>
        @endif
        <div class="btn-row">
            @if(auth()->id() === $template->user_id)
                <a href="{{ route('teacher.certificates.custom.download', [$student, $template]) }}" class="btn btn-primary"><i class="ri-download-line"></i> تحميل PDF</a>
                <a href="{{ route('teacher.certificates.custom.edit', [$student, $template]) }}" class="btn btn-outline"><i class="ri-edit-line"></i> تعديل القالب</a>
                @if($template->is_issued)
                    <span class="issued-badge"><i class="ri-checkbox-circle-fill"></i> تم الإصدار للطالب</span>
                @else
                    <form method="POST" action="{{ route('teacher.certificates.custom.issue', [$student, $template]) }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-issue" id="issueBtn"><i class="ri-send-plane-line"></i> إصدار للطالب</button>
                    </form>
                @endif
                <a href="{{ route('teacher.certificates.gallery', $student) }}" class="btn btn-outline"><i class="ri-arrow-right-line"></i> رجوع</a>
            @else
                <a href="{{ route('student.certificates.custom.download', [$student, $template]) }}" class="btn btn-primary"><i class="ri-download-line"></i> تحميل PDF</a>
                <a href="{{ route('student.achievements') }}" class="btn btn-outline"><i class="ri-arrow-right-line"></i> العودة للإنجازات</a>
            @endif
        </div>
    @endif

    @php
        $overlayOpacity = ($template->overlay_opacity ?? 15) / 100;

        // Determine background rendering approach
        $usePdfImg = ($forPdf ?? false) && ($pdfBgImage ?? null);
        $useCssBg = !$usePdfImg;

        if ($usePdfImg) {
            // mPDF: use <img> tag (reliable for all background types)
            $backgroundLayerStyle = '';
        } elseif ($template->background_type === 'image' && $template->background_image) {
            $backgroundLayerStyle = "background-image: url('" . asset('storage/' . $template->background_image) . "');"
                . " background-size: " . ($template->background_size ?? 100) . "% " . ($template->background_size ?? 100) . "%;"
                . " background-position: " . ($template->background_position_x ?? 50) . "% " . ($template->background_position_y ?? 50) . "%;"
                . " background-repeat: no-repeat; background-color: transparent;";
        } elseif ($template->background_type === 'solid') {
            $backgroundLayerStyle = "background-color: {$template->primary_color}; background-image: none;";
        } elseif ($forPdf ?? false) {
            // mPDF: CSS gradients fail → flat color fallback
            $backgroundLayerStyle = "background-color: {$template->primary_color}; background-image: none;";
        } else {
            $backgroundLayerStyle = "background-image: linear-gradient(135deg, {$template->primary_color} 0%, {$template->secondary_color} 50%, {$template->accent_color} 100%); background-color: transparent;";
        }
    @endphp

    <div class="cert-card" style="{{ ($forPdf ?? false) ? '' : '--primary: ' . $template->primary_color . '; --secondary: ' . $template->secondary_color . '; --accent: ' . $template->accent_color . ';' }}border-radius: {{ $template->border_radius }}px;">
        @if(!$usePdfImg)
            <div class="background-layer" style="{{ $backgroundLayerStyle }}"></div>
        @endif
        <div class="overlay" style="opacity: {{ $overlayOpacity }};"></div>
        <div class="cert-inner" style="font-family: '{{ $template->font_family }}', sans-serif;">
            @if($template->show_stamp)
                <div class="stamp" style="width: {{ $template->stamp_size }}px; height: {{ $template->stamp_size }}px; transform: rotate({{ $template->stamp_rotation ?? 0 }}deg);">شهادة</div>
            @endif
            <div>
                @if($template->show_logo)
                    <div class="logo-box" style="position: absolute; right: 30px; top: 30px; transform: translate({{ $template->logo_x }}px, {{ $template->logo_y }}px) rotate({{ $template->logo_rotation ?? 0 }}deg); width: {{ $template->logo_width }}px; height: {{ $template->logo_width }}px;">
                        @if($forPdf ?? false)
                            @if(!empty($logoBase64))
                                <img src="{{ $logoBase64 }}" alt="logo" style="width:100%;height:100%;object-fit:contain;">
                            @endif
                        @else
                            <img src="{{ $template->logo_image ? asset('storage/' . $template->logo_image) : asset('image/logono.png') }}" alt="logo" style="width:100%;height:100%;object-fit:contain;">
                        @endif
                    </div>
                @endif
                <div class="title text-block" style="transform: translate({{ $template->title_x }}px, {{ $template->title_y }}px) rotate({{ $template->title_rotation ?? 0 }}deg); font-size: {{ $template->title_size }}px; color: {{ $template->title_color }}; text-align: {{ $template->text_align }};">{{ $template->title }}</div>
                <div class="subtitle text-block" style="transform: translate({{ $template->subtitle_x }}px, {{ $template->subtitle_y }}px) rotate({{ $template->subtitle_rotation ?? 0 }}deg); font-size: {{ $template->subtitle_size }}px; color: {{ $template->subtitle_color }}; text-align: {{ $template->text_align }};">{{ $template->subtitle }}</div>
                <div class="student-name text-block" style="transform: translate({{ $template->name_x }}px, {{ $template->name_y }}px) rotate({{ $template->name_rotation ?? 0 }}deg); font-size: {{ $template->name_size }}px; color: {{ $template->name_color }}; text-align: {{ $template->text_align }};">{{ $template->recipient_name ?? $student->name }}</div>
                <div class="body-text text-block" style="transform: translate({{ $template->body_x }}px, {{ $template->body_y }}px) rotate({{ $template->body_rotation ?? 0 }}deg); font-size: {{ $template->body_size }}px; color: {{ $template->body_color }}; text-align: {{ $template->text_align }};">{{ $template->body_text }}</div>
            </div>
            <div class="footer" style="color: {{ $template->body_color }};">
                <div><strong>البرنامج:</strong> {{ $student->course }}</div>
                <div><strong>الدرجة:</strong> {{ $student->degree }}</div>
                <div><strong>منصة:</strong> إجلال التعليمية</div>
            </div>
        </div>
    </div>
    @if(!($forPdf ?? false))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var f = document.getElementById('issueBtn')?.closest('form');
                if (f) {
                    f.addEventListener('submit', function (e) {
                        if (!confirm('هل تريد إصدار هذه الشهادة للطالب؟ سيتم إشعاره فوراً.')) {
                            e.preventDefault();
                        }
                    });
                }
            });
        </script>
        @include('components.account-theme-foot')
    @endif
</body>
</html>
