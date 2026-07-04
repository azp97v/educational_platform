@extends('layouts.admin')
@section('title', 'مركز Live Ops')
@section('page_title', 'مركز المراقبة الحية')
@section('page_subtitle', 'متابعة تشغيلية لحظية للمنصة')
@section('content')
<section class="admin-grid">
  <article class="metric"><div class="k">متصلون الآن</div><div class="v">{{ $ops['online_users'] }}</div></article>
  <article class="metric"><div class="k">طلبات معلقة</div><div class="v">{{ $ops['pending_enrollments'] }}</div></article>
  <article class="metric"><div class="k">رسائل غير مقروءة</div><div class="v">{{ $ops['unread_messages'] }}</div></article>
  <article class="metric"><div class="k">مستخدمون جدد 24س</div><div class="v">{{ $ops['new_users_24h'] }}</div></article>
</section>
<section class="admin-card" style="margin-top:12px;">
  <h2>التنبيهات التشغيلية</h2>
  @if($alerts->isEmpty())
    <div class="admin-alert success">لا توجد تنبيهات حرجة حاليًا.</div>
  @else
    <div style="display:grid;gap:8px;">
      @foreach($alerts as $alert)
        <div class="admin-alert error">{{ $alert }}</div>
      @endforeach
    </div>
  @endif
</section>
@endsection
