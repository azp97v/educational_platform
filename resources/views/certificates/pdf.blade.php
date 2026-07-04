<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>شهادة إكمال الدورة</title>
    <style>
        @page { margin: 0; size: A4 landscape; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            background: #ffffff;
            padding: 0;
            margin: 0;
            overflow: hidden;
        }
        .certificate-container {
            width: 297mm;
            height: 210mm;
            background: #ffffff;
            position: relative;
            border: 4px solid #C4963A;
            margin: 0; padding: 0;
        }
        .certificate {
            padding: 50px 60px;
            position: relative;
            height: calc(100% - 16px);
            border: 2px solid #d4af37;
            margin: 8px;
        }
        .certificate-content {
            text-align: center;
        }
        .certificate-header {
            margin-bottom: 30px;
        }
        .certificate-title {
            font-size: 40px;
            font-weight: bold;
            color: #764ba2;
            margin-bottom: 8px;
        }
        .certificate-subtitle {
            font-size: 18px;
            color: #555555;
            margin-bottom: 20px;
        }
        .certificate-body {
            margin: 30px 0;
        }
        .certificate-text {
            font-size: 15px;
            color: #333333;
            margin-bottom: 16px;
            line-height: 1.8;
        }
        .recipient-name {
            font-size: 28px;
            font-weight: bold;
            color: #764ba2;
            margin: 16px 0;
            display: inline-block;
            border-bottom: 2px solid #764ba2;
            padding-bottom: 8px;
            min-width: 280px;
        }
        .course-name {
            font-size: 18px;
            color: #667eea;
            font-weight: bold;
            margin: 16px 0;
        }
        .score-badge {
            display: inline-block;
            background: #764ba2;
            color: #ffffff;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 15px;
            font-weight: bold;
            margin: 12px 0;
        }
        .date {
            margin-top: 16px;
            font-size: 13px;
            color: #666666;
        }
        .valid-badge {
            margin-top: 10px;
            font-size: 13px;
            font-weight: bold;
        }
        .valid-badge.active { color: #28a745; }
        .valid-badge.expired { color: #dc3545; }
        table.footer-table {
            width: 100%;
            margin-top: 50px;
            border-collapse: collapse;
        }
        table.footer-table td {
            width: 33.33%;
            text-align: center;
            padding: 10px;
            font-size: 11px;
            color: #555555;
            vertical-align: top;
        }
        table.footer-table .sig-line {
            border-bottom: 1px solid #333333;
            height: 50px;
        }
        .certificate-number {
            position: absolute;
            top: 16px;
            left: 16px;
            font-size: 11px;
            color: #999999;
        }
        .qr-code {
            position: absolute;
            bottom: 16px;
            right: 16px;
            width: 80px;
            height: 80px;
        }
        .qr-code img {
            width: 80px;
            height: 80px;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="certificate">
            <div class="certificate-number">
                #{{ $certificate->certificate_number }}
            </div>

            <div class="certificate-content">
                <div class="certificate-header">
                    <div class="certificate-title">شهادة إكمال</div>
                    <div class="certificate-subtitle">Certificate of Completion</div>
                </div>

                <div class="certificate-body">
                    <p class="certificate-text">
                        تشهد هذه الوثيقة بأن
                    </p>

                    <div class="recipient-name">
                        {{ $certificate->user->name ?? '---' }}
                    </div>

                    <p class="certificate-text">
                        قد أكمل بنجاح الدورة التدريبية
                    </p>

                    <div class="course-name">
                        {{ $certificate->course->name ?? '---' }}
                    </div>

                    <div class="score-badge">
                        درجة النجاح: {{ $certificate->score }}%
                    </div>

                    <div class="date">
                        تاريخ الإصدار: {{ $certificate->issued_at ? $certificate->issued_at->format('d/m/Y') : '---' }}
                    </div>

                    @if(!$certificate->isExpired())
                    <div class="valid-badge active">
                        شهادة سارية المفعول
                    </div>
                    @else
                    <div class="valid-badge expired">
                        شهادة منتهية الصلاحية
                    </div>
                    @endif
                </div>

                <table class="footer-table">
                    <tr>
                        <td>
                            <div class="sig-line"></div>
                            توقيع المدير
                        </td>
                        <td>
                            منصة Iglal
                        </td>
                        <td>
                            <div class="sig-line"></div>
                            ختم المنصة
                        </td>
                    </tr>
                </table>
            </div>

            @if($certificate->qr_code)
            <div class="qr-code">
                @php
                    $qrPath = $certificate->qr_code;
                    if (!str_starts_with($qrPath, 'data:')) {
                        $absPath = storage_path('app/public/' . $qrPath);
                        if (file_exists($absPath)) {
                            // Use absolute path for mPDF to avoid memory/regex overflow (48-page bug)
                            $qrPath = str_replace('\\', '/', $absPath);
                        }
                    }
                @endphp
                <img src="{{ $qrPath }}" alt="QR Code">
            </div>
            @endif
        </div>
    </div>
</body>
</html>
