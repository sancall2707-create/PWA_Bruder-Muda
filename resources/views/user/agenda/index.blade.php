@extends('layouts.user-app')

@php
    $backUrl = route('dashboard');
    $pageTitle = 'Agenda Pribadi';
@endphp

@section('content')
<!-- Header Month Selector & Legend -->
<div class="card" style="padding: 16px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
        <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--fic-text-muted); letter-spacing: 0.5px;">
            Periode Agenda
        </span>
        <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 700; color: var(--fic-primary);">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            <span>{{ $monthLabel }}</span>
        </div>
    </div>

    <!-- Category Indicators -->
    <div style="display: flex; flex-wrap: wrap; gap: 8px; font-size: 11px;">
        <span class="badge badge-scheduled" style="display: flex; align-items: center; gap: 4px;">
            <span class="dot blue"></span> Bimbingan Rohani
        </span>
        <span class="badge badge-cyan" style="display: flex; align-items: center; gap: 4px;">
            <span class="dot cyan"></span> Wawanhati Piko
        </span>
        <span class="badge badge-gold" style="display: flex; align-items: center; gap: 4px;">
            <span class="dot gold"></span> Agenda Lainnya
        </span>
    </div>
</div>

@if(session('success'))
    <div style="background-color: var(--fic-green-soft); border: 1px solid rgba(53, 168, 102, 0.3); color: #027A48; padding: 12px 16px; border-radius: 12px; font-size: 13px; font-weight: 600;">
        {{ session('success') }}
    </div>
@endif

<!-- 3 Action CTA Buttons -->
<div style="display: flex; flex-direction: column; gap: 8px;">
    <a href="{{ route('agenda.bimbingan.create') }}" class="btn-primary" style="font-size: 13px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        TAMBAH JADWAL BIMBINGAN ROHANI
    </a>

    <a href="{{ route('agenda.wawanhati.create') }}" class="btn-primary" style="background-color: var(--fic-cyan); font-size: 13px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        TAMBAH JADWAL WAWANHATI / PIKO
    </a>

    <a href="{{ route('agenda.lainnya.create') }}" class="btn-primary" style="background-color: #B57900; font-size: 13px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        TAMBAH AGENDA LAINNYA
    </a>
</div>

<!-- Chronological Agenda List -->
<div style="display: flex; flex-direction: column; gap: 12px; margin-top: 6px;">
    @forelse($agendas as $agenda)
        @php
            $leftBorderColor = match($agenda->category) {
                'bimbingan' => 'var(--fic-primary)',
                'wawanhati' => 'var(--fic-cyan)',
                default => 'var(--fic-gold)',
            };
        @endphp
        <div class="item-card" style="border-left: 4px solid {{ $leftBorderColor }};">
            <div class="item-card-header">
                <div>
                    <div class="item-card-title">{{ $agenda->title }}</div>
                    <div style="font-size: 12px; font-weight: 600; color: var(--fic-text-secondary); margin-top: 2px;">
                        {{ $agenda->target_person }}
                    </div>
                </div>
                <x-status-badge :status="$agenda->category" />
            </div>

            <div class="item-card-meta">
                <div class="meta-row">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <span><strong>{{ $agenda->date_formatted }}</strong> &bull; {{ $agenda->time_formatted }}</span>
                </div>
                <div class="meta-row">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    <span>{{ $agenda->location }}</span>
                </div>
            </div>

            @if(!empty($agenda->notes))
                <div class="item-card-preview" style="font-size: 12px;">
                    {{ $agenda->notes }}
                </div>
            @endif
        </div>
    @empty
        <x-empty-state
            title="Belum ada agenda bulan ini"
            message="Jadwalkan bimbingan rohani, wawanhati, atau kegiatan pribadi Anda."
            actionText="+ Tambah Agenda"
            :actionUrl="route('agenda.lainnya.create')"
        />
    @endforelse
</div>
@endsection
