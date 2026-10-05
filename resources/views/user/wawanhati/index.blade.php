@extends('layouts.user-app')

@php
    $backUrl = route('dashboard');
    $pageTitle = 'Wawanhati Pimpinan Lokal';
@endphp

@section('content')
<!-- Header Summary Card -->
<div class="card" style="padding: 16px 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <div style="font-size: 11px; font-weight: 700; color: var(--fic-text-muted); text-transform: uppercase; letter-spacing: 0.5px;">
                Ringkasan Wawanhati
            </div>
            <div style="font-size: 14px; font-weight: 700; color: var(--fic-text-primary); margin-top: 2px;">
                {{ $summary['selesai'] ?? 1 }} Selesai &bull; {{ $summary['terkirim'] ?? 1 }} Terkirim
            </div>
        </div>
        <span class="badge badge-cyan">Pimpinan Lokal (Piko)</span>
    </div>
</div>

@if(session('success'))
    <div style="background-color: var(--fic-green-soft); border: 1px solid rgba(53, 168, 102, 0.3); color: #027A48; padding: 12px 16px; border-radius: 12px; font-size: 13px; font-weight: 600;">
        {{ session('success') }}
    </div>
@endif

<!-- Primary CTA -->
<a href="{{ route('wawanhati.create') }}" class="btn-primary" style="background-color: var(--fic-cyan);">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
    </svg>
    + TAMBAH LAPORAN WAWANHATI BARU
</a>

<!-- List of Reports -->
<div style="display: flex; flex-direction: column; gap: 14px;">
    @forelse($reports as $report)
        <div class="item-card">
            <div class="item-card-header">
                <div>
                    <div class="item-card-title">{{ $report->local_leader_name }}</div>
                    <div style="font-size: 11px; color: var(--fic-text-muted);">Pimpinan Komunitas / Lokal</div>
                </div>
                <x-status-badge :status="$report->status" />
            </div>

            <div class="item-card-meta">
                <div class="meta-row">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                    </svg>
                    <span>{{ $report->meeting_at }}</span>
                </div>
                <div class="meta-row">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    <span>{{ $report->location }}</span>
                </div>
            </div>

            @if(!empty($report->report_content))
                <div class="item-card-preview">
                    {{ $report->report_content }}
                </div>
            @endif
        </div>
    @empty
        <x-empty-state
            title="Belum ada laporan Wawanhati"
            message="Catat hasil wawanhati bersama pimpinan komunitas lokal Anda."
            actionText="+ Tambah Laporan Pertama"
            :actionUrl="route('wawanhati.create')"
        />
    @endforelse
</div>
@endsection
