@extends('layouts.admin')
@section('title', 'مصفوفة الصلاحيات')
@section('page_title', 'مصفوفة الأدوار والصلاحيات')
@section('page_subtitle', 'نظرة شاملة على ما يستطيع كل دور فعله داخل المنصة')
@section('content')

{{-- Role Summary Cards --}}
<section style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px;margin-bottom:20px;">
    @foreach($roles as $roleKey => $role)
    <article class="admin-card" style="padding:18px;border-top:3px solid {{ $role['color'] }};">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
            <div style="width:38px;height:38px;border-radius:10px;background:{{ $role['color'] }}22;display:flex;align-items:center;justify-content:center;">
                <i class="{{ $role['icon'] }}" style="font-size:18px;color:{{ $role['color'] }};"></i>
            </div>
            <div>
                <div style="font-weight:700;font-size:15px;color:var(--text);">{{ $role['label'] }}</div>
                <div style="font-size:12px;color:var(--text-muted);">{{ count($role['grants']) }} صلاحية ممنوحة</div>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:6px;">
            <div style="flex:1;height:6px;border-radius:3px;background:var(--border-light);overflow:hidden;">
                <div style="height:100%;border-radius:3px;background:{{ $role['color'] }};width:{{ round((count($role['grants']) / count($permissions)) * 100) }}%;"></div>
            </div>
            <span style="font-size:11px;color:var(--text-muted);white-space:nowrap;">{{ round((count($role['grants']) / count($permissions)) * 100) }}%</span>
        </div>
    </article>
    @endforeach
</section>

{{-- Permission Matrix Table --}}
<section class="admin-card" style="padding:0;overflow:hidden;">
    <div style="padding:16px 20px;border-bottom:1px solid var(--border-light);display:flex;align-items:center;justify-content:space-between;">
        <h2 style="margin:0;font-size:15px;">مصفوفة الصلاحيات الكاملة</h2>
        <span style="font-size:12px;color:var(--text-muted);">{{ count($permissions) }} صلاحية × {{ count($roles) }} أدوار</span>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;min-width:500px;">
            <thead>
                <tr style="background:var(--surface-2);">
                    <th style="padding:12px 16px;text-align:right;font-size:12px;color:var(--text-muted);font-weight:600;border-bottom:1px solid var(--border-light);width:50%;">الصلاحية</th>
                    @foreach($roles as $roleKey => $role)
                        <th style="padding:12px 16px;text-align:center;font-size:12px;font-weight:700;border-bottom:1px solid var(--border-light);color:{{ $role['color'] }};">
                            <i class="{{ $role['icon'] }}"></i> {{ $role['label'] }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($permissions as $perm)
                <tr style="border-bottom:1px solid var(--border-light);transition:background .15s;" onmouseover="this.style.background='var(--surface-2)'" onmouseout="this.style.background=''">
                    <td style="padding:11px 16px;font-size:13px;color:var(--text);">
                        <code style="font-size:11px;color:var(--text-muted);margin-left:6px;">{{ $perm['key'] }}</code>
                        {{ $perm['label'] }}
                    </td>
                    @foreach($roles as $roleKey => $role)
                        <td style="padding:11px 16px;text-align:center;">
                            @if(in_array($perm['key'], $role['grants']))
                                <span style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:{{ $role['color'] }}22;">
                                    <i class="ri-check-line" style="font-size:14px;color:{{ $role['color'] }};font-weight:700;"></i>
                                </span>
                            @else
                                <span style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;">
                                    <i class="ri-subtract-line" style="font-size:14px;color:var(--border-light);"></i>
                                </span>
                            @endif
                        </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

<div class="admin-alert" style="margin-top:14px;background:rgba(108,99,255,0.08);border:1px solid rgba(108,99,255,0.2);color:var(--text-muted);font-size:13px;padding:10px 16px;border-radius:8px;">
    <i class="ri-information-line" style="color:#6c63ff;margin-left:6px;"></i>
    هذه المصفوفة للعرض فقط — الصلاحيات مُطبَّقة عبر middleware داخل الكود ولا يمكن تعديلها من هنا.
</div>
@endsection
