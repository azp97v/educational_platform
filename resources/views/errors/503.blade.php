<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>503 — الصيانة | إجلال</title>
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<script>
(function(){var t=function(e,k){try{return e.getItem(k)}catch(e){return null}},s=function(e,k,v){try{e.setItem(k,v)}catch(e){}};var th=t(localStorage,'app-theme')||t(localStorage,'theme')||t(sessionStorage,'app-theme')||t(sessionStorage,'theme')||'light';th=th==='dark'?'dark':'light';s(localStorage,'app-theme',th);s(localStorage,'theme',th);s(sessionStorage,'app-theme',th);s(sessionStorage,'theme',th);document.documentElement.setAttribute('data-theme',th)})();
</script>
<style>
*{margin:0;padding:0;box-sizing:border-box}
:root{--bg:#F4F6F8;--card:#FFFFFF;--text:#222B3D;--text-soft:#5E6675;--muted:#7D8797;--gold:#C6A675;--gold-dark:#997722;--gold-soft:rgba(198,166,117,0.16);--border:#DFE5EC;--shadow:0 4px 24px rgba(0,0,0,0.04)}
[data-theme="dark"]{--bg:#050505;--card:#0F0F10;--text:#F5F7FA;--text-soft:#C7CED9;--muted:#A2ACBC;--gold:#C6A675;--gold-dark:#997722;--gold-soft:rgba(198,166,117,0.22);--border:#26282C;--shadow:0 8px 24px rgba(0,0,0,0.32)}
html,body{height:100%;font-family:'Tajawal',sans-serif;background:var(--bg);color:var(--text);display:flex;align-items:center;justify-content:center;padding:20px}
.error-card{width:min(92vw,520px);background:var(--card);border:1px solid var(--border);border-radius:24px;padding:3.5rem 2.5rem;text-align:center;box-shadow:var(--shadow);animation:fadeIn .5s ease-out}
.error-icon{width:100px;height:100px;margin:0 auto 1.5rem;background:var(--gold-soft);border:2px solid var(--gold);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:3rem}
.error-code{font-size:4.5rem;font-weight:900;background:linear-gradient(135deg,var(--gold),var(--gold-dark));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;line-height:1;margin-bottom:.5rem}
h1{font-size:1.6rem;font-weight:700;margin-bottom:.75rem;color:var(--text)}
p{color:var(--text-soft);font-size:1.05rem;line-height:1.7;margin-bottom:2rem}
.error-actions{display:flex;flex-direction:column;gap:12px;align-items:center}
.btn{display:inline-flex;align-items:center;gap:8px;padding:14px 32px;border-radius:14px;font-family:'Tajawal',sans-serif;font-size:1rem;font-weight:700;text-decoration:none;cursor:pointer;transition:all .3s cubic-bezier(.4,0,.2,1);border:none}
.btn-primary{background:linear-gradient(135deg,var(--gold),var(--gold-dark));color:#fff;box-shadow:0 10px 30px rgba(198,166,117,0.3)}
.btn-primary:hover{transform:translateY(-3px);box-shadow:0 15px 40px rgba(198,166,117,0.45)}
.btn-secondary{background:transparent;color:var(--text-soft);border:2px solid var(--border)}
.btn-secondary:hover{border-color:var(--gold);color:var(--gold);transform:translateY(-2px)}
@keyframes fadeIn{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
@media(max-width:500px){.error-card{padding:2.5rem 1.5rem}.error-code{font-size:3.5rem}h1{font-size:1.3rem}}
</style>
</head>
<body>
<div class="error-card">
<div class="error-icon">🛠️</div>
<div class="error-code">503</div>
<h1>جاري الصيانة</h1>
<p>المنصة قيد الصيانة والتطوير حاليًا.<br>نعتذر عن الإزعاج، سنعود قريبًا بخدمة أفضل. شكرًا لتفهمك!</p>
<div class="error-actions">
<a href="{{ url('/') }}" class="btn btn-primary"><span>←</span> المحاولة لاحقًا</a>
</div>
</div>
</body>
</html>