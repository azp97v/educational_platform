@extends('layouts.admin')
@section('title', 'الإدارة المالية')
@section('page_title', 'الإدارة المالية')
@section('page_subtitle', 'مؤشرات عائد وتكلفة وربحية تقديرية')
@section('content')
<section class="admin-grid">
  <article class="metric"><div class="k">طلاب</div><div class="v">{{ $students }}</div></article>
  <article class="metric"><div class="k">معلمون</div><div class="v">{{ $teachers }}</div></article>
  <article class="metric"><div class="k">الإيراد التقديري</div><div class="v">{{ number_format($projectedRevenue) }}</div></article>
  <article class="metric"><div class="k">التكاليف التقديرية</div><div class="v">{{ number_format($projectedCosts) }}</div></article>
  <article class="metric"><div class="k">صافي الربح التقديري</div><div class="v">{{ number_format($projectedProfit) }}</div></article>
</section>

<section class="admin-card" style="margin-top:12px;">
  <h2>مؤشرات تشغيل داعمة</h2>
  <div class="admin-form-grid">
    <div><label>المسارات</label><input value="{{ $totalCourses }}" disabled></div>
    <div><label>الدروس</label><input value="{{ $totalLessons }}" disabled></div>
    <div><label>الاختبارات</label><input value="{{ $totalExams }}" disabled></div>
    <div><label>الشهادات</label><input value="{{ $totalCertificates }}" disabled></div>
  </div>
</section>
@endsection
