/* ─────────────── LEADERBOARD DATA ─────────────── */
const lbData = [
  { rank:1, av:'SQ', name:'سعد القحطاني',     pts:'4,250', streak:'متميز - 45 يوم', isMe:false },
  { rank:2, av:'AM', name:'عبدالرحمن محمد',     pts:'3,890', streak:'متقدم - 17 يوم', isMe:false },
  { rank:3, av:'AA', name:'أنت (أحمد علي)',     pts:'3,150', streak:'متقدم - 18 يوم', isMe:true },
  { rank:4, av:'YM', name:'ياسين منصور',       pts:'2,900', streak:'متقدم - 17 يوم', isMe:false },
];

/* ─────────────── RENDER LEADERBOARD ─────────────── */
document.addEventListener('DOMContentLoaded', function() {
  const lbEl = document.getElementById('lbList');
  if (lbEl) {
    lbData.forEach(l => {
      const meClass = l.isMe ? 'me' : '';
      lbEl.innerHTML += `
        <div class="lb-item ${meClass}">
          <div class="lb-pts-box">
            <div class="lb-pts">${l.pts}</div>
            <div class="lb-pts-lbl">XP</div>
          </div>
          
          <div class="lb-info">
            <div class="lb-name">${l.name}</div>
            <div class="lb-streak"><span>${l.streak}</span> <i class="ri-fire-line" style="color:#FF9500"></i></div>
          </div>

          <div class="lb-right-part">
            <div class="lb-av">${l.av}</div>
            <div class="lb-rank">#${l.rank}</div>
          </div>
        </div>`;
    });
  }
});

/* ─────────────── EXAM LOGIC ─────────────── */
function startExam() {
  document.getElementById('exam-list-view').style.display = 'none';
  document.getElementById('exam-flow-views').style.display = 'block';
  document.getElementById('exam-question-view').style.display = 'block';
  document.getElementById('exam-success-view').style.display = 'none';
  document.querySelectorAll('.exam-opt').forEach(el => el.classList.remove('selected'));
  document.getElementById('btnSubmitQ').style.display = 'none';
}

function selectOpt(btn) {
  document.querySelectorAll('.exam-opt').forEach(el => el.classList.remove('selected'));
  btn.classList.add('selected');
  document.getElementById('btnSubmitQ').style.display = 'inline-block';
}

function finishExam() {
  document.getElementById('exam-question-view').style.display = 'none';
  document.getElementById('exam-success-view').style.display = 'block';
}

function returnFromExam() {
  gotoPage('home');
  setTimeout(() => {
    document.getElementById('exam-flow-views').style.display = 'none';
    document.getElementById('exam-list-view').style.display = 'block';
  }, 400);
}

/* ─────────────── PAGE NAVIGATION ─────────────── */
const pages = ['home','academy','exams','competition','achievements'];

function gotoPage(name) {
  if (document.getElementById('page-lesson')) {
    document.getElementById('page-lesson').classList.remove('active');
  }
  
  pages.forEach(p => {
    const pageEl = document.getElementById('page-'+p);
    const btnEl = document.getElementById('nb-'+p);
    if (pageEl) pageEl.classList.remove('active');
    if (btnEl) btnEl.classList.remove('active');
  });

  if (name === 'lesson') {
    const lessonPage = document.getElementById('page-lesson');
    if (lessonPage) lessonPage.classList.add('active');
    const academyBtn = document.getElementById('nb-academy');
    if (academyBtn) academyBtn.classList.add('active');
  } else {
    const pageEl = document.getElementById('page-'+name);
    const btnEl = document.getElementById('nb-'+name);
    if (pageEl) pageEl.classList.add('active');
    if (btnEl) btnEl.classList.add('active');
  }
}

function switchCompTab(tab) {
  const lbEl = document.getElementById('comp-lb');
  const podiumEl = document.getElementById('comp-podium');
  const tabLbEl = document.getElementById('tab-lb');
  const tabPodiumEl = document.getElementById('tab-podium');
  
  if (lbEl) lbEl.style.display = tab==='lb' ? 'block' : 'none';
  if (podiumEl) podiumEl.style.display = tab==='podium' ? 'block' : 'none';
  if (tabLbEl) tabLbEl.classList.toggle('active', tab==='lb');
  if (tabPodiumEl) tabPodiumEl.classList.toggle('active', tab==='podium');
}

/* ─────────────── DARK MODE ─────────────── */
let dark = false;
function toggleDark() {
  dark = !dark;
  document.documentElement.setAttribute('data-theme', dark ? 'dark' : '');
  const darkIcon = document.getElementById('darkIcon');
  if (darkIcon) {
    darkIcon.className = dark ? 'ri-sun-line' : 'ri-moon-line';
  }
  // Save to localStorage
  localStorage.setItem('student-theme', dark ? 'dark' : 'light');
}

// Load saved theme on page load
document.addEventListener('DOMContentLoaded', function() {
  const savedTheme = localStorage.getItem('student-theme');
  if (savedTheme === 'dark') {
    dark = true;
    document.documentElement.setAttribute('data-theme', 'dark');
    const darkIcon = document.getElementById('darkIcon');
    if (darkIcon) darkIcon.className = 'ri-sun-line';
  }

  // تحديث حالة الشعلة بناءً على الـ streak
  updateStreakFlameState();
});

/* ─────────────── STREAK FLAME ANIMATION ─────────────── */
function updateStreakFlameState() {
  // بطاقة الـ streak الكبيرة
  const streakCard = document.querySelector('.st-streak-card');
  const streakValue = document.querySelector('.st-streak-val');
  
  if (streakCard && streakValue) {
    const currentStreak = parseInt(streakValue.textContent) || 0;
    
    if (currentStreak > 0) {
      // الـ streak مستمر - شعلة حية مع وميض
      streakCard.classList.add('active');
      streakCard.classList.remove('inactive');
    } else {
      // الـ streak منقطع - شعلة رمادية هادئة
      streakCard.classList.add('inactive');
      streakCard.classList.remove('active');
    }
  }

  // شارة الـ streak في الـ topbar
  const streakBadge = document.querySelector('.g-streak');
  const streakSpan = document.querySelector('.g-streak span');
  
  if (streakBadge && streakSpan) {
    const badgeStreak = parseInt(streakSpan.textContent) || 0;
    
    if (badgeStreak > 0) {
      streakBadge.classList.add('active');
      streakBadge.classList.remove('inactive');
    } else {
      streakBadge.classList.add('inactive');
      streakBadge.classList.remove('active');
    }
  }
}
