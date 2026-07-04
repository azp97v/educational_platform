<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>شهاداتي - إجلال</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    @include('components.account-theme-head')

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: var(--font-body), 'Tajawal', sans-serif; background: var(--bg); color: var(--text-primary); }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }

        .header {
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: white;
            padding: 40px 30px;
            border-radius: 14px;
            margin-bottom: 30px;
            text-align: center;
        }

        .header h1 { font-size: 32px; margin-bottom: 10px; }

        .certificates-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .certificate-card {
            background: var(--card-bg);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .certificate-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .certificate-header {
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: white;
            padding: 20px;
            text-align: center;
        }

        .certificate-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .certificate-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .certificate-number {
            font-size: 12px;
            opacity: 0.9;
            font-family: monospace;
        }

        .certificate-body {
            padding: 20px;
        }

        .certificate-course {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--text-primary);
        }

        .certificate-info {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 15px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
        }

        .info-label {
            color: var(--text-secondary);
        }

        .info-value {
            font-weight: 600;
            color: var(--text-primary);
        }

        .score-badge {
            background: linear-gradient(135deg, var(--success), #27AE60);
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .certificate-actions {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            flex: 1;
            padding: 10px;
            background: var(--card-bg);
            border: 2px solid #E5E5EA;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            color: var(--text-primary);
            text-align: center;
            font-weight: 600;
            transition: all 0.3s;
        }

        .action-btn:hover {
            background: var(--gold);
            color: white;
            border-color: var(--gold);
        }

        .action-btn.primary {
            background: var(--gold);
            color: white;
            border-color: var(--gold);
        }

        .action-btn.primary:hover {
            background: var(--gold-dark);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }

        .empty-state i {
            font-size: 64px;
            color: #AEAEB2;
            margin-bottom: 20px;
            display: block;
        }

        .verification-info {
            background: #D1ECCC;
            border-right: 4px solid var(--success);
            padding: 10px 12px;
            border-radius: 4px;
            font-size: 12px;
            color: #0F5132;
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .expiry-warning {
            background: #FFF8E7;
            border-right: 4px solid #FF9500;
            padding: 10px 12px;
            border-radius: 4px;
            font-size: 12px;
            color: #856404;
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        @media (max-width: 768px) {
            .certificates-grid {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 480px) {
            .container { padding: 10px; }
            .certificate-card { padding: 16px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="ri-medal-line"></i> شهاداتي</h1>
            <p>الشهادات التي حصلت عليها من دوراتك المكتملة</p>
        </div>

        @if($certificates->count() > 0)
            <div class="certificates-grid">
                @foreach($certificates as $certificate)
                    <div class="certificate-card">
                        <div class="certificate-header">
                            <div class="certificate-icon">🎓</div>
                            <div class="certificate-title">شهادة إتمام</div>
                            <div class="certificate-number">{{ $certificate->certificate_number }}</div>
                        </div>

                        <div class="certificate-body">
                            <div class="certificate-course">
                                {{ $certificate->course->name }}
                            </div>

                            <div class="score-badge">
                                النسبة: {{ $certificate->score }}%
                            </div>

                            <div class="certificate-info">
                                <div class="info-item">
                                    <span class="info-label"><i class="ri-calendar-line"></i> التاريخ:</span>
                                    <span class="info-value">{{ $certificate->issued_at->format('d/m/Y') }}</span>
                                </div>

                                <div class="info-item">
                                    <span class="info-label"><i class="ri-user-line"></i> الطالب:</span>
                                    <span class="info-value">{{ auth()->user()->name }}</span>
                                </div>

                                @if($certificate->expires_at)
                                    <div class="info-item">
                                        <span class="info-label"><i class="ri-time-line"></i> انتهاء:</span>
                                        <span class="info-value">{{ $certificate->expires_at->format('d/m/Y') }}</span>
                                    </div>
                                @endif
                            </div>

                            @if($certificate->is_verified)
                                <div class="verification-info">
                                    <i class="ri-check-line"></i> تم التحقق من الشهادة
                                </div>
                            @endif

                            @if($certificate->expires_at && $certificate->isExpired())
                                <div class="expiry-warning">
                                    <i class="ri-alert-line"></i> انتهت صلاحية الشهادة
                                </div>
                            @endif

                            <div class="certificate-actions">
                                <a href="{{ route('certificate.show', $certificate) }}" class="action-btn primary">
                                    <i class="ri-eye-line"></i> عرض
                                </a>
                                <a href="{{ route('certificate.download', $certificate) }}" class="action-btn">
                                    <i class="ri-download-line"></i> تحميل
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="ri-inbox-line"></i>
                <h3>لا توجد شهادات بعد</h3>
                <p>أكمل الدورات والاختبارات لتحصل على شهادات إجلال 🎓</p>
                <a href="{{ route('student.index') }}" style="display: inline-block; margin-top: 20px; padding: 10px 20px; background: var(--gold); color: white; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    العودة للدروس
                </a>
            </div>
        @endif

        <!-- Verification Section -->
        <div style="margin-top: 40px; background: var(--card-bg); border-radius: 14px; padding: 30px; box-shadow: var(--shadow);">
            <h2 style="margin-bottom: 20px; font-size: 20px;">
                <i class="ri-shield-check-line"></i> التحقق من شهادة
            </h2>
            <p style="color: var(--text-secondary); margin-bottom: 20px;">
                أدخل رقم الشهادة للتحقق من صحتها
            </p>

            <form action="{{ route('certificate.verify.check') }}" method="POST" style="display: flex; gap: 10px;">
                @csrf
                <input type="text" name="certificate_number" placeholder="مثال: CERT-20260401-ABCD1234"
                       required style="flex: 1; padding: 12px; border: 2px solid var(--border); border-radius: 8px; font-family: var(--font-number), monospace;">
                <button type="submit" style="padding: 12px 30px; background: var(--gold); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                    تحقق
                </button>
            </form>
        </div>
    </div>
    @include('components.account-theme-foot')
</body>
</html>
