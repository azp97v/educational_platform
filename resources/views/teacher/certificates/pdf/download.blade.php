<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 0;
            @if($backgroundImage)
            background-image: url("{{ $backgroundImage }}");
            background-image-resize: 6;
            @endif
        }
        body {
            margin: 0; padding: 0; direction: rtl;
            font-family: 'sans-serif'; text-align: center;
            color: #1e1b4b;
        }
        .cert-paper { width: 297mm; height: 210mm; position: relative; }
        .main-title { font-size: 48pt; font-weight: bold; padding-top: 60pt; margin-bottom: 10pt; margin-top: 20px; }
        .statement { font-size: 18pt; color: #475569; margin: 15pt 0; }
        .name { font-weight: bold; }
        .course-text { font-size: 19pt; line-height: 1.6; color: #1e293b; margin: 0 60pt 30pt; }
        .course-name { font-weight: bold; }
        .footer-table { width: 85%; margin: 40pt auto 0; border-collapse: collapse; }
        .footer-cell { width: 33.3%; vertical-align: bottom; text-align: center; }
        .sig-line { border-top: 1.5pt solid #1e293b; width: 160pt; margin-bottom: 8pt; margin-left: auto; margin-right: auto; }
        .label-text { font-size: 12pt; color: #64748b; margin-bottom: 5pt; }
        .name-text { font-weight: bold; font-size: 16pt; color: #1e293b; }
        .seal-box {
            width: 85pt; height: 85pt;
            border: 2pt double #C4963A; border-radius: 50%;
            line-height: 85pt; text-align: center;
            color: #C4963A; font-size: 11pt; font-weight: bold;
            margin: 0 auto; transform: rotate(-15deg);
        }
    </style>
</head>
<body>
    <div class="cert-paper">
        <div class="main-title">شهادة إجتياز</div>
        <div class="statement">
            يشـهد معهد {{ auth()->user()->name }} بأن المتدرب/ـة : <span class="name">{{ $student->name }}</span>
        </div>
        <div class="course-text">
            قد اجتاز بنجاح الدورة التدريبية بعنوان :
            <span class="course-name">"{{ $student->course }}"</span>
            التي أقيمت في مركزنا التدريبي<br><br>
            والمنعقدة بتاريخ {{ $student->course_date->format('Y-m-d') }} م وقد حصل على تقدير عام : {{ $student->degree }}
        </div>
        <div class="course-text">بناءً عليه، مُنحت له هذه الشهادة تقديراً لجهوده وتمنياتنا له بمزيد من التوفيق والنجاح.</div>
        <div style="text-align:center;font-size:14pt;color:#475569;margin-bottom:10pt;">
            صدرت في: {{ now()->format('Y-m-d') }}
        </div>
        <table class="footer-table" style="width:100%;border-collapse:collapse;margin-top:10px;">
            <tr>
                <td class="footer-cell" style="width:33%;text-align:center;">
                    <div class="sig-line"></div>
                    <div class="label-text">الجهة</div>
                    <div class="name-text">{{ auth()->user()->name }}</div>
                </td>
                <td class="footer-cell" style="width:33%;text-align:center;vertical-align:middle;">
                    <div class="seal-box" style="margin:0 auto;">الختم الرسمي</div>
                </td>
                <td class="footer-cell" style="width:33%;text-align:center;">
                    <div class="sig-line"></div>
                    <div class="label-text">اعتماد التوقيع</div>
                    <div class="name-text">رقمي</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
