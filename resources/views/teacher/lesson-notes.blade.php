<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  @include('components.account-theme-head')
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>ملاحظات الطلاب - {{ $lesson->name }}</title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">
  <style>
    :root {
      --gold:#C4963A; --gold-light:rgba(196,150,58,0.12);
      --card-bg:#fff; --primary-light:#F4F5FA;
      --text-primary:#1a1a1a; --text-secondary:#666;
      --text-muted:#999; --border:rgba(0,0,0,0.06);
      --radius-lg:16px; --radius-md:12px;
      --shadow:0 4px 24px rgba(0,0,0,0.06);
      --transition:all 0.3s cubic-bezier(0.4,0,0.2,1);
      --topbar-h:64px;
    }
    body[data-theme="dark"],html[data-theme="dark"] {
      --card-bg:#1e1e1e; --primary-light:#121212;
      --text-primary:#f0f0f0; --text-secondary:#aaa;
      --border:rgba(255,255,255,0.08);
    }
    *{margin:0;padding:0;box-sizing:border-box;}
    body{font-family:'Tajawal',sans-serif;background:var(--primary-light);color:var(--text-primary);}

    /* TOP BAR */
    .topbar{
      height:var(--topbar-h);display:flex;align-items:center;gap:1rem;
      padding:0 1.5rem;background:var(--card-bg);
      border-bottom:1px solid var(--border);position:sticky;top:0;z-index:50;
      box-shadow:0 2px 8px rgba(0,0,0,0.04);
    }
    .topbar a{color:var(--gold);font-size:1.3rem;text-decoration:none;display:flex;align-items:center;}
    .topbar h1{font-size:1.1rem;font-weight:700;flex:1;}
    .topbar small{font-size:0.8rem;color:var(--text-secondary);white-space:nowrap;}

    /* MAIN */
    .main{max-width:900px;margin:0 auto;padding:1.5rem 1rem 3rem;}

    /* FILTER BAR */
    .filter-bar{
      display:flex;gap:0.7rem;flex-wrap:wrap;align-items:center;
      margin-bottom:1.2rem;
    }
    .filter-bar input{
      flex:1;min-width:180px;padding:0.6rem 1rem;
      border:1px solid var(--border);border-radius:10px;
      background:var(--card-bg);color:var(--text-primary);font-family:inherit;font-size:0.9rem;
    }
    .badge{
      padding:0.35rem 0.9rem;border-radius:20px;font-size:0.8rem;font-weight:600;
      background:var(--gold-light);color:var(--gold);
    }
    .refresh-btn{
      padding:0.5rem 1rem;border-radius:10px;border:1px solid var(--gold);
      background:transparent;color:var(--gold);cursor:pointer;font-family:inherit;font-size:0.85rem;
      display:flex;align-items:center;gap:0.4rem;transition:var(--transition);
    }
    .refresh-btn:hover{background:var(--gold);color:#fff;}

    /* NOTE CARD */
    .note-card{
      background:var(--card-bg);border-radius:var(--radius-md);
      padding:1rem 1.2rem;margin-bottom:0.9rem;
      box-shadow:var(--shadow);border:1px solid var(--border);
      transition:var(--transition);
    }
    .note-card:hover{box-shadow:0 6px 24px rgba(0,0,0,0.09);}
    .note-meta{display:flex;align-items:center;gap:0.7rem;margin-bottom:0.5rem;flex-wrap:wrap;}
    .note-avatar{
      width:34px;height:34px;border-radius:50%;
      background:linear-gradient(135deg,var(--gold),#a07a28);
      display:flex;align-items:center;justify-content:center;
      font-size:0.85rem;font-weight:700;color:#fff;flex-shrink:0;
    }
    .note-student{font-weight:700;font-size:0.9rem;}
    .note-time{font-size:0.75rem;color:var(--text-muted);margin-right:auto;}
    .note-body{font-size:0.9rem;line-height:1.65;color:var(--text-primary);white-space:pre-wrap;word-break:break-word;}
    .note-edited{font-size:0.72rem;color:var(--text-muted);margin-top:0.4rem;}

    /* EMPTY */
    .empty{text-align:center;padding:3rem 1rem;color:var(--text-muted);}
    .empty i{font-size:3rem;opacity:0.3;display:block;margin-bottom:0.8rem;}

    /* LIVE DOT */
    .live-dot{
      width:8px;height:8px;border-radius:50%;background:#34C759;
      display:inline-block;animation:pulse 1.8s infinite;
    }
    @keyframes pulse{0%,100%{opacity:1;}50%{opacity:0.3;}}

    /* responsive */
    @media(max-width:600px){
      .note-time{margin-right:0;width:100%;}
    }
  </style>
</head>
<body>

@include('components.account-theme-body-class')

<div class="topbar">
  <a href="{{ url()->previous() }}" title="رجوع"><i class="ri-arrow-right-line"></i></a>
  <h1><i class="ri-sticky-note-2-line" style="color:var(--gold);margin-left:6px;"></i>ملاحظات الطلاب — {{ Str::limit($lesson->name, 40) }}</h1>
  <small id="liveStatus"><span class="live-dot"></span> مباشر</small>
</div>

<div class="main">

  <div class="filter-bar">
    <input type="text" id="searchInput" placeholder="ابحث في الملاحظات أو بالاسم..." oninput="filterNotes()">
    <span class="badge" id="countBadge">{{ $notes->count() }} ملاحظة</span>
    <button class="refresh-btn" onclick="fetchNotes()"><i class="ri-refresh-line"></i> تحديث</button>
  </div>

  <div id="notesList">
    @forelse($notes as $note)
      <div class="note-card" data-student="{{ $note->user->name ?? '' }}" data-text="{{ $note->text }}">
        <div class="note-meta">
          <div class="note-avatar">{{ mb_substr($note->user->name ?? '?', 0, 1) }}</div>
          <span class="note-student">{{ $note->user->name ?? 'طالب' }}</span>
          <span class="note-time">{{ $note->created_at->diffForHumans() }}</span>
        </div>
        <div class="note-body">{{ $note->text }}</div>
        @if($note->updated_at && $note->updated_at->ne($note->created_at))
          <div class="note-edited"><i class="ri-edit-line"></i> تم التعديل {{ $note->updated_at->diffForHumans() }}</div>
        @endif
      </div>
    @empty
      <div class="empty" id="emptyState">
        <i class="ri-sticky-note-2-line"></i>
        لا توجد ملاحظات من الطلاب على هذا الدرس بعد.
      </div>
    @endforelse
  </div>

</div>

<script>
const LESSON_ID = {{ $lesson->id }};
const CSRF      = document.querySelector('meta[name="csrf-token"]').content;
let   allNotes  = [];

// Seed from server-side render
document.querySelectorAll('.note-card').forEach(card => {
  allNotes.push({ student: card.dataset.student, text: card.dataset.text, el: card.outerHTML });
});

function filterNotes() {
  const q     = document.getElementById('searchInput').value.trim().toLowerCase();
  const cards = document.querySelectorAll('#notesList .note-card');
  let   shown = 0;
  cards.forEach(c => {
    const match = !q || c.dataset.student.toLowerCase().includes(q) || c.dataset.text.toLowerCase().includes(q);
    c.style.display = match ? '' : 'none';
    if (match) shown++;
  });
  document.getElementById('emptyState')?.remove();
  if (shown === 0) {
    document.getElementById('notesList').insertAdjacentHTML('beforeend',
      '<div class="empty" id="emptyState"><i class="ri-search-line"></i>لا توجد نتائج للبحث.</div>');
  }
}

async function fetchNotes() {
  try {
    const res  = await fetch(`/teacher/lessons/${LESSON_ID}/student-notes`, {
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
    });
    const data = await res.json();
    if (!data.notes) return;

    const list = document.getElementById('notesList');
    list.innerHTML = '';

    if (data.notes.length === 0) {
      list.innerHTML = '<div class="empty" id="emptyState"><i class="ri-sticky-note-2-line"></i>لا توجد ملاحظات بعد.</div>';
      document.getElementById('countBadge').textContent = '0 ملاحظة';
      return;
    }

    document.getElementById('countBadge').textContent = data.notes.length + ' ملاحظة';

    data.notes.forEach(n => {
      const initial  = (n.user?.name || '?')[0];
      const name     = n.user?.name || 'طالب';
      const timeAgo  = new Date(n.created_at).toLocaleString('ar-SA');
      const edited   = n.updated_at && n.updated_at !== n.created_at
        ? `<div class="note-edited"><i class="ri-edit-line"></i> معدّل</div>` : '';
      list.insertAdjacentHTML('beforeend', `
        <div class="note-card" data-student="${escHtml(name)}" data-text="${escHtml(n.text)}">
          <div class="note-meta">
            <div class="note-avatar">${escHtml(initial)}</div>
            <span class="note-student">${escHtml(name)}</span>
            <span class="note-time">${timeAgo}</span>
          </div>
          <div class="note-body">${escHtml(n.text)}</div>
          ${edited}
        </div>
      `);
    });
    filterNotes();
  } catch (e) { /* silent */ }
}

function escHtml(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Auto-refresh every 30 seconds
setInterval(fetchNotes, 30000);
</script>

@include('components.account-theme-script')
</body>
</html>
