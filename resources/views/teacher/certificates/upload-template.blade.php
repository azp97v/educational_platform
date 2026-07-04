<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رفع قالب شهادة</title>
    @include('components.account-theme-head')
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        *{box-sizing:border-box;margin:0;padding:0;}
        body {
            font-family:'Tajawal',sans-serif;
            background: radial-gradient(circle at top left, var(--theme-gold-soft), transparent 22%),
                        linear-gradient(180deg, var(--theme-page-bg) 0%, var(--theme-surface) 40%, var(--theme-surface-2) 100%);
            color:var(--text-primary);min-height:100vh;
            display:flex;align-items:center;justify-content:center;padding:40px;
        }
        .card {
            background: var(--theme-card-bg, var(--theme-surface));
            backdrop-filter:blur(24px);
            border:1px solid var(--theme-border);border-radius:22px;
            padding:36px;box-shadow:0 18px 50px rgba(0,0,0,0.35);
            width:100%;max-width:620px;
        }
        h1{font-size:24px;font-weight:800;color:var(--theme-gold);margin-bottom:6px;}
        .sub{color:var(--text-secondary);font-size:13px;margin-bottom:24px;}
        .info-box{
            background:var(--theme-gold-soft);border:1px solid var(--theme-border-strong);
            border-radius:14px;padding:16px 20px;margin-bottom:24px;
        }
        .info-box h6{color:var(--theme-gold);font-size:14px;margin-bottom:8px;}
        .info-box ul{padding-right:18px;color:var(--text-secondary);font-size:13px;line-height:1.8;}
        .field{margin-bottom:18px;}
        label{display:block;font-size:13px;font-weight:700;color:var(--text-secondary);margin-bottom:6px;}
        input{
            width:100%;padding:12px 16px;border-radius:12px;border:1px solid var(--theme-border);
            background:var(--theme-input-bg, rgba(255,255,255,0.06));color:var(--text-primary);font-size:14px;
            font-family:'Tajawal',sans-serif;transition:0.3s;outline:none;
        }
        input:focus{border-color:var(--theme-gold);box-shadow:0 0 0 3px var(--theme-gold-soft);}
        input[type="file"]{padding:10px;}
        .actions{display:flex;gap:12px;margin-top:24px;}
        .btn{
            flex:1;display:inline-flex;align-items:center;justify-content:center;gap:8px;
            padding:14px;border-radius:14px;font-weight:700;font-size:14px;
            text-decoration:none;cursor:pointer;border:none;transition:0.3s;
            font-family:'Tajawal',sans-serif;
        }
        .btn-primary{background:var(--theme-gold);color:#000;}
        .btn-primary:hover{background:var(--theme-gold-dark);transform:translateY(-2px);}
        .btn-outline{background:var(--theme-surface-2);color:var(--text-secondary);border:1px solid var(--theme-border);}
        .btn-outline:hover{background:var(--theme-gold-soft);color:var(--text-primary);}
    </style>
</head>
<body>
    <div class="card">
        <h1><i class="ri-upload-cloud-line"></i> رفع قالب شهادة</h1>
        <div class="sub">للمستفيد: <strong style="color:var(--theme-gold);">{{ $student->name }}</strong></div>

        <div class="info-box">
            <h6>شروط القالب المرفوع</h6>
            <ul>
                <li>الصيغ المدعومة: JPG, PNG, SVG, WebP</li>
                <li>يفضل أن تكون الصورة واضحة وبحجم مناسب للشهادة</li>
                <li>النصوص الأساسية (عنوان، عنوان فرعي، اسم، نص) قابلة للتعديل لاحقاً</li>
            </ul>
        </div>

        <form action="{{ route('teacher.certificates.custom.upload', $student) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="field">
                <label>اسم القالب</label>
                <input type="text" name="name" placeholder="مثال: قالب رياضي" required>
            </div>
            <div class="field">
                <label>اختر صورة القالب</label>
                <input type="file" name="template_image" accept="image/*" required>
            </div>
            <div class="actions">
                <button type="submit" class="btn btn-primary"><i class="ri-upload-line"></i> رفع القالب</button>
                <a href="{{ route('teacher.certificates.gallery', $student) }}" class="btn btn-outline"><i class="ri-arrow-right-line"></i> رجوع</a>
            </div>
        </form>
    </div>
    @include('components.account-theme-foot')
</body>
</html>
