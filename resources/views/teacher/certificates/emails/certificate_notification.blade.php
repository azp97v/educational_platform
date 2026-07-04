<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <style>
        body { background-color: #f8fafc; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, sans-serif; }
        .main-card { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 10px 15px rgba(0,0,0,0.05); overflow: hidden; }
        .header { background: linear-gradient(135deg, #C4963A, #A07A28); padding: 30px; text-align: center; }
        .content { padding: 40px 30px; text-align: center; }
        h1 { color: #1e1b4b; font-size: 24px; margin-bottom: 20px; }
        .highlight { color: #C4963A; font-weight: bold; }
        p { color: #475569; font-size: 16px; line-height: 1.6; }
        .footer { padding: 20px; background: #f1f5f9; text-align: center; font-size: 12px; color: #64748b; }
        .details-card { margin: 24px 0; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; text-align: right; }
        .details-row { display: flex; justify-content: space-between; padding: 12px 18px; font-size: 14px; }
        .details-row:nth-child(even) { background: #f8fafc; }
        .details-row .label { color: #64748b; font-weight: 600; }
        .details-row .value { color: #1e1b4b; font-weight: 700; }
    </style>
</head>
<body>
    <div class="main-card">
        <div class="header">
            <h1 style="color:#fff;margin:0;">منصة إجلال التعليمية</h1>
        </div>
        <div class="content">
            <h1>تهانينا يا <span class="highlight">{{ $student->name }}</span>!</h1>
            <p>يسعدنا في <strong>منصة إجلال التعليمية</strong> أن نرسل لك شهادة الاجتياز الخاصة بك.</p>
            <p>لقد أتممت متطلباتك بنجاح، وتجد مرفقاً مع هذا البريد نسخة من الشهادة جاهزة للتحميل والطباعة.</p>

            <div class="details-card">
                <div class="details-row"><span class="label">الدورة / المسار</span><span class="value">{{ $student->course }}</span></div>
                <div class="details-row"><span class="label">تاريخ الإتمام</span><span class="value">{{ $student->course_date?->format('Y-m-d') }}</span></div>
                <div class="details-row"><span class="label">التقدير</span><span class="value">{{ $student->degree }}</span></div>
            </div>

            <p>نتمنى لك مزيداً من التوفيق والنجاح في مسيرتك العلمية والعملية.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} منصة إجلال التعليمية. جميع الحقوق محفوظة.
        </div>
    </div>
</body>
</html>
