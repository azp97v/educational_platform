@extends('layouts.admin')
@section('title', 'الصلاحيات RBAC')
@section('page_title', 'مصفوفة الأدوار والصلاحيات')
@section('page_subtitle', 'تحكم دقيق في الوصول لكل قسم داخل المنصة')

@section('content')
<section class="admin-card">
  <h2>نظام الصلاحيات</h2>
  <div class="rbac-grid">
    @foreach($matrix as $role => $perms)
      <article class="rbac-card">
        <header>
          <h3>{{ ucfirst($role) }}</h3>
          <span>{{ count($perms) }} صلاحيات</span>
        </header>
        <div class="rbac-chips">
          @foreach($perms as $perm)
            <span class="chip">{{ $perm }}</span>
          @endforeach
        </div>
      </article>
    @endforeach
  </div>
</section>
@endsection
