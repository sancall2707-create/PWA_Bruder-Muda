@extends('layouts.super-admin')

@section('content')
<!-- Segmented Tab Controls -->
<div style="display: flex; background: #F2F4F7; border-radius: 12px; padding: 4px;">
    <a href="{{ route('superadmin.monitoring.index') }}" class="tab-btn {{ request()->routeIs('superadmin.monitoring.index') ? 'active' : '' }}" style="flex:1; text-align:center; padding:10px; border-radius:10px; font-weight:700; font-size:13px; text-decoration:none; {{ request()->routeIs('superadmin.monitoring.index') ? 'background:#fff;color:#1455C0;box-shadow:0 2px 6px rgba(0,0,0,0.08);' : 'color:#667085;' }}">
        Jurnal & Bimbingan
    </a>
    <a href="{{ route('superadmin.monitoring.agenda') }}" class="tab-btn {{ request()->routeIs('superadmin.monitoring.agenda') ? 'active' : '' }}" style="flex:1; text-align:center; padding:10px; border-radius:10px; font-weight:700; font-size:13px; text-decoration:none; {{ request()->routeIs('superadmin.monitoring.agenda') ? 'background:#fff;color:#1455C0;box-shadow:0 2px 6px rgba(0,0,0,0.08);' : 'color:#667085;' }}">
        Rekapitulasi Agenda
    </a>
</div>

<!-- Filter Bar -->
<div style="display: flex; gap: 8px; margin-top: 12px;">
    <select name="month_filter" class="form-select" style="flex:1; padding:10px 12px; border-radius:10px; border:1px solid #E4E7EC; background:#fff; font-size:13px;">
        <option value="">Semua Bulan</option>
        @for($i=1; $i<=12; $i++)
            <option value="{{ $i }}" {{ request('month_filter') == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}</option>
        @endfor
    </select>
    <select name="bruder_filter" class="form-select" style="flex:1; padding:10px 12px; border-radius:10px; border:1px solid #E4E7EC; background:#fff; font-size:13px;">
        <option value="">Semua Bruder</option>
        @foreach($bruders as $b)
            <option value="{{ $b->id }}" {{ request('bruder_filter') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
        @endforeach
    </select>
</div>

@if(session('success'))
    <div style="background:#ECFDF3; border:1px solid #ABEFC6; color:#027A48; padding:12px; border-radius:12px; font-size:13px; font-weight:700; margin-top:12px;">
        {{ session('success') }}
    </div>
@endif

<!-- Laporan List -->
<div style="display:flex; flex-direction:column; gap:10px; margin-top:12px;">
    @forelse($reports as $r)
        <div class="card" style="background:#fff; border:1px solid #E4E7EC; border-radius:14px; padding:14px; box-shadow:0 2px 6px rgba(16,24,40,0.03);">
            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                <div style="flex:1;">
                    <div style="font-weight:700; color:#101828;">{{ $r->user->name }}</div>
                    <div style="font-size:12px; color:#667085; margin-top:2px;">{{ $r->type_label }} &bull; {{ \Carbon\Carbon::parse($r->date)->translatedFormat('d M Y') }}</div>
                </div>
                <span class="badge {{ $r->status_badge }}" style="padding:4px 10px; border-radius:999px; font-size:10px; font-weight:700; background:{{ $r->status_bg }}; color:{{ $r->status_color }}; border:1px solid {{ $r->status_border }};">{{ $r->status_text }}</span>
            </div>
            @if(!empty($r->preview))
                <div style="margin-top:10px; padding:10px; background:#F8FAF9; border-left:3px solid #1455C0; border-radius:0 8px 8px 0; font-size:12px; color:#374151;">{{ $r->preview }}</div>
            @endif
        </div>
    @empty
        <div class="card" style="text-align:center; color:#667085; padding:24px;">Belum ada laporan sesuai filter.</div>
    @endforelse
</div>

<!-- Export FAB -->
<div style="margin-top:16px; text-align:center;">
    <a href="{{ route('superadmin.monitoring.export', request()->query()) }}" class="btn-primary" style="display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:14px 28px; background:#B57900; color:#fff; border-radius:14px; font-weight:700; text-decoration:none; box-shadow:0 4px 12px rgba(181,121,0,0.25);">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
        Ekspor Data ke CSV
    </a>
</div>
@endsection