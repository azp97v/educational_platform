<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($student) ? 'تعديل' : 'إضافة' }} مستفيد</title>
    @include('components.account-theme-head')
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        *{box-sizing:border-box;margin:0;padding:0;}
        body {
            font-family:'Tajawal',sans-serif;
            background: radial-gradient(circle at top left, rgba(198,166,117,0.18), transparent 22%),
                        linear-gradient(180deg, var(--theme-page-bg) 0%, var(--theme-surface) 40%, var(--theme-surface-2) 100%);
            color:var(--text-primary);min-height:100vh;
            display:flex;align-items:center;justify-content:center;padding:40px;
        }
        .card {
            background: var(--theme-surface);backdrop-filter:blur(24px);
            border:1px solid var(--theme-border);border-radius:22px;
            padding:36px;box-shadow:0 18px 50px rgba(0,0,0,0.35);
            width:100%;max-width:620px;
        }
        h1{font-size:24px;font-weight:800;color:var(--theme-gold);text-align:center;margin-bottom:4px;}
        .sub{color:var(--text-secondary);font-size:13px;text-align:center;margin-bottom:28px;}
        .field{margin-bottom:18px;position:relative;}
        label{display:block;font-size:13px;font-weight:700;color:var(--text-secondary);margin-bottom:6px;}
        input,select{
            width:100%;padding:12px 16px;border-radius:12px;border:1px solid var(--theme-border);
            background:var(--theme-input-bg, rgba(255,255,255,0.06));color:var(--text-primary);font-size:14px;
            font-family:'Tajawal',sans-serif;transition:0.3s;outline:none;
        }
        input:focus,select:focus{border-color:var(--theme-gold);box-shadow:0 0 0 3px var(--theme-gold-soft);}
        select option{background:var(--theme-surface);color:var(--text-primary);}
        .row{display:flex;gap:14px;}
        .row .field{flex:1;}
        .actions{display:flex;gap:12px;margin-top:28px;}
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
        .btn-success{background:var(--theme-success);color:#000;}
        .error{color:var(--theme-danger);font-size:12px;margin-top:4px;}
        .flash{padding:14px 20px;border-radius:12px;margin-bottom:20px;font-size:14px;font-weight:600;}
        .flash-error{background:rgba(255,59,48,0.12);color:var(--theme-danger);border:1px solid rgba(255,59,48,0.2);}
        .current-img{display:block;margin-top:8px;max-width:80px;border-radius:8px;}

        .autocomplete-wrap{position:relative;}
        .autocomplete-wrap input{padding-left:36px;padding-right:36px;cursor:pointer;}
        .autocomplete-wrap .search-icon{
            position:absolute;left:14px;top:50%;transform:translateY(-50%);
            color:var(--text-secondary);font-size:16px;pointer-events:none;
        }
        .autocomplete-wrap .dropdown-caret{
            position:absolute;right:14px;top:50%;transform:translateY(-50%);
            color:var(--text-secondary);font-size:18px;pointer-events:none;
        }
        .autocomplete-dropdown .item.already-added{opacity:.7;}
        .autocomplete-dropdown .item .item-added-badge{
            display:inline-flex;align-items:center;gap:4px;
            background:var(--theme-gold-soft);color:var(--theme-gold);
            padding:2px 8px;border-radius:999px;font-size:11px;font-weight:700;
            margin-right:6px;
        }
        .autocomplete-dropdown{
            position:absolute;top:100%;right:0;left:0;z-index:50;
            background:var(--theme-surface);border:1px solid var(--theme-border);
            border-radius:12px;margin-top:4px;max-height:200px;overflow-y:auto;
            display:none;box-shadow:0 12px 40px rgba(0,0,0,0.5);
        }
        .autocomplete-dropdown.show{display:block;}
        .autocomplete-dropdown .item{
            padding:10px 16px;cursor:pointer;transition:0.15s;
            border-bottom:1px solid var(--theme-border-light);
        }
        .autocomplete-dropdown .item:last-child{border-bottom:none;}
        .autocomplete-dropdown .item:hover{background:var(--theme-gold-soft);}
        .autocomplete-dropdown .item .item-name{color:var(--text-primary);font-size:14px;font-weight:600;}
        .autocomplete-dropdown .item .item-sub{color:var(--text-secondary);font-size:12px;}
        .autocomplete-dropdown .item .item-icon{color:var(--theme-gold);margin-left:8px;}

        .smart-badge{
            display:inline-flex;align-items:center;gap:6px;
            padding:4px 12px;border-radius:999px;font-size:12px;font-weight:600;
            margin-top:6px;
        }
        .smart-badge.found{background:var(--theme-success-soft, rgba(52,199,89,0.12));color:var(--theme-success);}
        .smart-badge.not-found{background:rgba(255,59,48,0.12);color:var(--theme-danger);}
        .smart-badge i{font-size:14px;}

        .hint{color:var(--text-secondary);font-size:12px;margin-top:4px;}

        .tag-pill{
            display:inline-flex;align-items:center;gap:6px;
            background:var(--theme-gold-soft);color:var(--theme-gold);
            padding:6px 14px;border-radius:999px;font-size:13px;font-weight:600;
            margin:4px 4px 0 0;
        }
        @keyframes spin { from { transform:rotate(0deg); } to { transform:rotate(360deg); } }

        /* Avatar preview */
        .avatar-preview-wrap {
            display:none;align-items:center;gap:12px;
            margin-top:10px;padding:10px 14px;
            background:var(--theme-surface-2);border:1px solid var(--theme-border);
            border-radius:12px;
        }
        .avatar-preview-wrap.show { display:flex; }
        .avatar-circle {
            width:44px;height:44px;border-radius:50%;
            overflow:hidden;flex-shrink:0;
            background:var(--theme-gold);
            display:flex;align-items:center;justify-content:center;
            font-size:20px;font-weight:800;color:#000;
        }
        .avatar-circle img { width:100%;height:100%;object-fit:cover;display:none; }
        .avatar-circle .initial { display:flex; }
        .avatar-info { font-size:13px; }
        .avatar-info .av-name { font-weight:700;color:var(--text-primary); }
        .avatar-info .av-email { color:var(--text-secondary);font-size:12px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>{{ isset($student) ? 'تعديل بيانات المستفيد' : 'إضافة مستفيد جديد' }}</h1>
        <div class="sub">بيانات المستفيد للحصول على شهادة إنجاز</div>

        @if($errors->any())
            <div class="flash flash-error">
                @foreach($errors->all() as $e) {{ $e }}<br> @endforeach
            </div>
        @endif

        <form method="POST" action="{{ isset($student) ? route('teacher.certificates.students.update', $student) : route('teacher.certificates.students.store') }}" enctype="multipart/form-data" id="studentForm">
            @csrf
            @isset($student) @method('PATCH') @endisset

            <div class="field">
                <label>اسم المستفيد</label>
                <div class="autocomplete-wrap">
                    <i class="ri-user-search-line search-icon"></i>
                    <input type="text" name="name" id="nameInput" value="{{ old('name', $student->name ?? '') }}" required placeholder="اضغط للاختيار من قائمة الطلاب أو اكتب اسماً جديداً..." autocomplete="off">
                    <i class="ri-arrow-down-s-line dropdown-caret"></i>
                </div>
                <div class="autocomplete-dropdown" id="nameDropdown"></div>
                <div id="userSmartBadge"></div>

                {{-- Avatar preview shown after selecting a system user --}}
                <div class="avatar-preview-wrap" id="avatarPreviewWrap">
                    <div class="avatar-circle" id="avatarCircle">
                        <img id="avatarImg" src="" alt="">
                        <span class="initial" id="avatarInitial"></span>
                    </div>
                    <div class="avatar-info">
                        <div class="av-name" id="avatarName"></div>
                        <div class="av-email" id="avatarEmail"></div>
                    </div>
                </div>

                <div class="hint">اضغط على الحقل لعرض كل الطلاب المسجلين في النظام، أو اكتب للبحث</div>
            </div>

            <div class="field">
                <label>البريد الإلكتروني</label>
                <div class="autocomplete-wrap">
                    <i class="ri-mail-line search-icon"></i>
                    <input type="email" name="email" id="emailInput" value="{{ old('email', $student->email ?? '') }}" required placeholder="example@email.com" autocomplete="off">
                </div>
            </div>

            @isset($student)
            {{-- حقل المسار يظهر في وضع التعديل فقط --}}
            <div class="field">
                <label>المسار/الدورة</label>
                <div class="autocomplete-wrap">
                    <i class="ri-book-2-line search-icon"></i>
                    <input type="text" name="course" id="courseInput" value="{{ old('course', $student->course ?? '') }}" placeholder="ابحث أو اكتب اسم المسار..." autocomplete="off">
                </div>
                <div class="autocomplete-dropdown" id="courseDropdown"></div>
                <div class="hint">سيتم اقتراح مساراتك المنشورة تلقائياً</div>
            </div>
            @endisset

            <div class="field">
                <label>اسم المستخدم (اختياري)</label>
                <div style="position:relative;">
                    <input type="text" name="username" id="usernameInput" value="{{ old('username', $student->username ?? '') }}" placeholder="example_user" autocomplete="off" style="padding-left:40px;">
                    <span id="usernameStatus" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:18px;"></span>
                </div>
                <div id="usernameHint" class="hint">يجب أن يكون فريداً — حروف إنجليزية وأرقام و _ فقط</div>
            </div>

            <div class="row">
                <div class="field">
                    <label>تاريخ الدورة</label>
                    <input type="date" name="course_date" id="courseDateInput" value="{{ old('course_date', isset($student) && $student->course_date ? $student->course_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                </div>
                <div class="field">
                    <label>التقدير</label>
                    <select name="degree" required>
                        <option value="">اختر</option>
                        @foreach(['ممتاز','جيد جداً','جيد','مقبول'] as $d)
                            <option value="{{ $d }}" {{ (old('degree', $student->degree ?? '') == $d) ? 'selected' : '' }}>{{ $d }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="field" id="imageUploadField">
                <label>الصورة (اختياري)</label>
                <input type="file" name="image" accept="image/*">
                @if(isset($student) && $student->image)
                    <div style="display:flex;align-items:center;gap:10px;margin-top:8px;">
                        <img src="{{ asset('storage/'.$student->image) }}" class="current-img">
                        <label style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--theme-danger);cursor:pointer;">
                            <input type="checkbox" name="remove_image" value="1">
                            حذف الصورة الحالية
                        </label>
                    </div>
                @endif
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line"></i> {{ isset($student) ? 'تحديث' : 'حفظ' }}
                </button>
                <a href="{{ route('teacher.certificates.students') }}" class="btn btn-outline">
                    <i class="ri-arrow-right-line"></i> رجوع
                </a>
            </div>
        </form>
    </div>

    <script>
    const systemUsers = @json($systemUsers ?? []);
    const systemCourses = @json($courses ?? []);
    const addedEntries = @json($addedEntries ?? []); // now email-only strings

    const nameInput = document.getElementById('nameInput');
    const emailInput = document.getElementById('emailInput');
    const nameDropdown = document.getElementById('nameDropdown');
    const userSmartBadge = document.getElementById('userSmartBadge');
    const avatarPreviewWrap = document.getElementById('avatarPreviewWrap');
    const avatarImg = document.getElementById('avatarImg');
    const avatarInitial = document.getElementById('avatarInitial');
    const avatarName = document.getElementById('avatarName');
    const avatarEmail = document.getElementById('avatarEmail');
    let selectedUserId = null;

    // Course elements may not exist (create mode hides them)
    const courseInput = document.getElementById('courseInput');
    const courseDropdown = document.getElementById('courseDropdown');

    function isAlreadyAdded(email) {
        return addedEntries.includes(email.toLowerCase());
    }

    function showDropdown(dropdown, items, inputVal, renderFn) {
        const filtered = items.filter(renderFn.filter).slice(0, 30);
        if (filtered.length === 0) { dropdown.classList.remove('show'); return; }
        dropdown.innerHTML = filtered.map(renderFn.template).join('');
        dropdown.classList.add('show');
    }

    function hideDropdowns() {
        nameDropdown.classList.remove('show');
        if (courseDropdown) courseDropdown.classList.remove('show');
    }

    function setAvatarPreview(user) {
        const imageUploadField = document.getElementById('imageUploadField');
        if (!user) {
            avatarPreviewWrap.classList.remove('show');
            if (imageUploadField) imageUploadField.style.display = '';
            return;
        }
        avatarName.textContent = user.name;
        avatarEmail.textContent = user.email;
        if (user.avatar_url) {
            const src = user.avatar_url.startsWith('http') ? user.avatar_url : '/storage/' + user.avatar_url;
            avatarImg.src = src;
            avatarImg.style.display = 'block';
            avatarInitial.style.display = 'none';
        } else {
            avatarImg.style.display = 'none';
            avatarInitial.style.display = 'flex';
            avatarInitial.textContent = (user.name || '?').trim()[0];
        }
        avatarPreviewWrap.classList.add('show');
        if (imageUploadField) imageUploadField.style.display = 'none';
    }

    function renderUserDropdown(val) {
        showDropdown(nameDropdown, systemUsers, val, {
            filter: u => u.name.toLowerCase().includes(val.toLowerCase()) || u.email.toLowerCase().includes(val.toLowerCase()),
            template: u => {
                const added = isAlreadyAdded(u.email);
                return `<div class="item${added ? ' already-added' : ''}" data-id="${u.id}" data-name="${u.name}" data-email="${u.email}">
                    <div><i class="ri-user-line item-icon"></i><span class="item-name">${u.name}</span>${added ? '<span class="item-added-badge"><i class="ri-checkbox-circle-fill"></i> مُضاف مسبقاً</span>' : ''}</div>
                    <div class="item-sub">${u.email}</div>
                </div>`;
            }
        });
    }

    // ── User Autocomplete / Dropdown ──
    nameInput.addEventListener('focus', function () {
        renderUserDropdown(this.value.trim());
    });

    nameInput.addEventListener('input', function () {
        selectedUserId = null;
        setAvatarPreview(null);
        renderUserDropdown(this.value.trim());
    });

    nameDropdown.addEventListener('click', function (e) {
        const item = e.target.closest('.item');
        if (!item) return;
        const u = systemUsers.find(u => u.id == item.dataset.id);
        nameInput.value = item.dataset.name;
        emailInput.value = item.dataset.email;
        selectedUserId = item.dataset.id;
        if (u) {
            if (u.username && u.username.trim() && !document.getElementById('usernameInput').value.trim()) {
                document.getElementById('usernameInput').value = u.username;
            }
            setAvatarPreview(u);
        }
        hideDropdowns();
        updateUserBadge();
    });

    function updateUserBadge() {
        if (selectedUserId) {
            userSmartBadge.innerHTML = `<span class="smart-badge found"><i class="ri-checkbox-circle-line"></i> مستخدم موجود في النظام</span>`;
        } else if (nameInput.value.trim() && emailInput.value.trim()) {
            const exists = systemUsers.some(u => u.email === emailInput.value.trim());
            userSmartBadge.innerHTML = exists
                ? `<span class="smart-badge found"><i class="ri-checkbox-circle-line"></i> البريد موجود في النظام</span>`
                : `<span class="smart-badge not-found"><i class="ri-user-add-line"></i> مستخدم جديد — سيتم إضافته للشهادات فقط</span>`;
        } else {
            userSmartBadge.innerHTML = '';
        }
    }

    emailInput.addEventListener('input', updateUserBadge);
    nameInput.addEventListener('blur', updateUserBadge);

    // ── Course Autocomplete / Dropdown (edit mode only) ──
    if (courseInput && courseDropdown) {
        function renderCourseDropdown(val) {
            showDropdown(courseDropdown, systemCourses, val, {
                filter: c => c.name.toLowerCase().includes(val.toLowerCase()),
                template: c => `<div class="item" data-name="${c.name}">
                    <div><i class="ri-book-2-line item-icon"></i><span class="item-name">${c.name}</span></div>
                    <div class="item-sub">مسار منشور</div>
                </div>`
            });
        }

        courseInput.addEventListener('focus', function () { renderCourseDropdown(this.value.trim()); });
        courseInput.addEventListener('input', function () { renderCourseDropdown(this.value.trim()); });

        courseDropdown.addEventListener('click', function (e) {
            const item = e.target.closest('.item');
            if (!item) return;
            courseInput.value = item.dataset.name;
            hideDropdowns();
        });
    }

    // ── Hide on click outside ──
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.autocomplete-wrap') && !e.target.closest('.autocomplete-dropdown')) {
            hideDropdowns();
        }
    });

    nameInput.addEventListener('keydown', function (e) { if (e.key === 'Escape') hideDropdowns(); });
    if (courseInput) courseInput.addEventListener('keydown', function (e) { if (e.key === 'Escape') hideDropdowns(); });

    // ── Init badge on edit ──
    if (nameInput.value.trim()) {
        setTimeout(updateUserBadge, 100);
    }

    // ── Username Availability Check ──
    const usernameInput = document.getElementById('usernameInput');
    const usernameStatus = document.getElementById('usernameStatus');
    const usernameHint = document.getElementById('usernameHint');
    let usernameCheckTimer = null;

    usernameInput.addEventListener('input', function () {
        clearTimeout(usernameCheckTimer);
        const val = this.value.trim();

        if (!val) {
            usernameStatus.innerHTML = '';
            usernameHint.style.color = 'var(--text-secondary)';
            usernameHint.textContent = 'يجب أن يكون فريداً — حروف إنجليزية وأرقام و _ فقط';
            return;
        }

        if (!/^[a-zA-Z0-9_]+$/.test(val)) {
            usernameStatus.innerHTML = '<i class="ri-close-circle-fill" style="color:var(--theme-danger);"></i>';
            usernameHint.style.color = 'var(--theme-danger)';
            usernameHint.textContent = 'يُسمح فقط بحروف إنجليزية وأرقام و _';
            return;
        }

        usernameStatus.innerHTML = '<i class="ri-loader-4-line" style="color:var(--text-muted);animation:spin 1s linear infinite;"></i>';

        usernameCheckTimer = setTimeout(function () {
            fetch('{{ url("teacher/certificates/students/check-username") }}/' + encodeURIComponent(val))
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.available) {
                        usernameStatus.innerHTML = '<i class="ri-checkbox-circle-fill" style="color:var(--theme-success);"></i>';
                        usernameHint.style.color = 'var(--theme-success)';
                        usernameHint.textContent = 'اسم المستخدم متوفر ✓';
                    } else {
                        usernameStatus.innerHTML = '<i class="ri-close-circle-fill" style="color:var(--theme-danger);"></i>';
                        usernameHint.style.color = 'var(--theme-danger)';
                        usernameHint.textContent = 'اسم المستخدم غير متاح — يُستخدم بالفعل';
                    }
                })
                .catch(function () {
                    usernameStatus.innerHTML = '';
                });
        }, 400);
    });
    </script>
    @include('components.account-theme-foot')
</body>
</html>
