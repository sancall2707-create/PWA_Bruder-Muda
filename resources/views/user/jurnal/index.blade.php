@extends('layouts.user-app')

@php
    $backUrl = route('dashboard');
    $pageTitle = 'Jurnal Pengembangan Diri';
@endphp

@section('content')
<!-- Header Summary Card -->
<div class="card" style="padding: 16px 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <div style="font-size: 11px; font-weight: 700; color: var(--fic-text-muted); text-transform: uppercase; letter-spacing: 0.5px;">
                Refleksi &amp; Formasi
            </div>
            <div style="font-size: 14px; font-weight: 700; color: var(--fic-text-primary); margin-top: 2px;">
                Catatan Pembinaan Berkelanjutan
            </div>
        </div>
        <span class="badge badge-done">Pertumbuhan Diri</span>
    </div>
</div>

@if(session('success'))
    <div style="background-color: var(--fic-green-soft); border: 1px solid rgba(53, 168, 102, 0.3); color: #027A48; padding: 12px 16px; border-radius: 12px; font-size: 13px; font-weight: 600;">
        {{ session('success') }}
    </div>
@endif

<!-- Primary CTA -->
<a href="{{ route('jurnal.create') }}" class="btn-primary" style="background-color: var(--fic-green);">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
    </svg>
    TAMBAH JURNAL BARU
</a>

<!-- List of Journals -->
<div style="display: flex; flex-direction: column; gap: 14px;">
    @forelse($journals as $journal)
        <div class="item-card">
            <div class="item-card-header">
                <div>
                    <div class="item-card-title">{{ $journal->activity_name }}</div>
                    <div style="font-size: 12px; color: var(--fic-text-secondary); margin-top: 2px;">
                        {{ $journal->organizer }} &bull; {{ $journal->speaker }}
                    </div>
                </div>
                <x-status-badge :status="$journal->status" />
            </div>

            <div class="item-card-meta">
                <div class="meta-row">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                    </svg>
                    <span>Tanggal: <strong>{{ $journal->activity_date }}</strong></span>
                </div>
            </div>

            @if(!empty($journal->reflection))
                <div class="item-card-preview" style="background-color: #F8FAF9; border-left: 3px solid var(--fic-green);">
                    <strong style="display: block; font-size: 11px; text-transform: uppercase; color: #027A48; margin-bottom: 4px;">
                        Refleksi Singkat:
                    </strong>
                    {{ $journal->reflection }}
                </div>
            @endif
        </div>
    @empty
        <x-empty-state
            title="Belum ada jurnal pengembangan diri"
            message="Dokumentasikan pelatihan, retret, dan refleksi rohani Anda."
        />
    @endforelse
</div>
@endsection
