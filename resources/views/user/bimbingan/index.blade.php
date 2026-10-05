@extends('layouts.user-app')

@php
    $backUrl = route('dashboard');
    $pageTitle = 'Laporan Bimbingan Rohani';
@endphp

@section('content')
<!-- Header Summary Card -->
<div class="card" style="padding: 16px 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <div style="font-size: 11px; font-weight: 700; color: var(--fic-text-muted); text-transform: uppercase; letter-spacing: 0.5px;">
                Ringkasan Laporan
            </div>
            <div style="font-size: 14px; font-weight: 700; color: var(--fic-text-primary); margin-top: 2px;">
                {{ $summary['selesai'] ?? 1 }} Selesai &bull; {{ $summary['menunggu'] ?? 1 }} Terjadwal
            </div>
        </div>
        <span class="badge badge-scheduled">Tahun 2026</span>
    </div>
</div>

@if(session('success'))
    <div style="background-color: var(--fic-green-soft); border: 1px solid rgba(53, 168, 102, 0.3); color: #027A48; padding: 12px 16px; border-radius: 12px; font-size: 13px; font-weight: 600;">
        {{ session('success') }}
    </div>
@endif

<!-- Primary CTA -->
<a href="{{ route('bimbingan.create') }}" class="btn-primary">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
    </svg>
    + TAMBAH LAPORAN BIMBINGAN BARU
</a>

<!-- List of Reports -->
<div style="display: flex; flex-direction: column; gap: 14px;">
    @forelse($reports as $report)
        <div class="item-card">
            <div class="item-card-header">
                <div>
                    <div class="item-card-title">{{ $report->mentor_name }}</div>
                    <div style="font-size: 11px; color: var(--fic-text-muted);">Pembimbing Rohani</div>
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
            title="Belum ada laporan Bimbingan Rohani"
            message="Catat refleksi dan hasil bimbingan rohani Anda bersama pembimbing."
            actionText="+ Tambah Laporan Pertama"
            :actionUrl="route('bimbingan.create')"
        />
    @endforelse
</div>
@endsection
