@extends('layouts.user-app')

@php
    $backUrl = route('profil');
    $pageTitle = 'Catatan Pembinaan TPPBM';

    $categories = [
        'Catatan Hidup Pribadi' => '1. Hidup Pribadi',
        'Catatan Hidup Rohani/Doa' => '2. Hidup Rohani / Doa',
        'Catatan Hidup Komunitas' => '3. Hidup Komunitas',
        'Catatan Hidup Kerasulan' => '4. Hidup Kerasulan',
        'Catatan Hidup Triprasetia' => '5. Hidup Triprasetia (Kaul)',
    ];
@endphp

@section('content')
<div class="card" style="padding: 16px 18px; margin-bottom: 14px;">
    <div style="font-size: 11px; font-weight: 700; color: var(--fic-text-muted); text-transform: uppercase; letter-spacing: 0.5px;">
        Evaluasi &amp; Arahan Formatif
    </div>
    <h2 style="font-size: 16px; font-weight: 800; color: var(--fic-text-primary); margin: 4px 0 2px;">
        Catatan Pembinaan Bruder Muda
    </h2>
    <p style="font-size: 12px; color: var(--fic-text-secondary); margin: 0;">
        Umpan balik, arahan, dan catatan resmi dari Tim Pendamping Pembinaan Bruder Muda (TPPBM).
    </p>
</div>

<div style="display: flex; flex-direction: column; gap: 14px;">
    @forelse($categories as $categoryKey => $categoryLabel)
        @php
            $groupNotes = $notes->where('category', $categoryKey);
        @endphp
        <div class="card" style="padding: 16px;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #F2F4F7; padding-bottom: 8px; margin-bottom: 10px;">
                <h3 style="font-size: 13px; font-weight: 800; color: #1455C0; margin: 0; text-transform: uppercase;">
                    {{ $categoryLabel }}
                </h3>
                <span style="font-size: 11px; font-weight: 700; color: {{ $groupNotes->isNotEmpty() ? '#027A48' : '#98A2B3' }};">
                    {{ $groupNotes->count() }} catatan
                </span>
            </div>

            @forelse($groupNotes as $note)
                <div style="background: #F8F9FC; border-left: 3px solid #1455C0; border-radius: 8px; padding: 10px 12px; margin-bottom: 8px;">
                    <div style="font-size: 13px; color: #101828; line-height: 1.5; white-space: pre-line;">
                        {{ $note->content }}
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 6px; font-size: 11px; color: #667085;">
                        <span>Oleh: <strong>{{ $note->author->name ?? 'Tim Pendamping' }}</strong></span>
                        <span>{{ $note->created_at->locale('id')->translatedFormat('d M Y') }}</span>
                    </div>
                </div>
            @empty
                <div style="font-size: 12px; color: #98A2B3; font-style: italic; padding: 4px 0;">
                    Belum ada catatan untuk aspek ini.
                </div>
            @endforelse
        </div>
    @empty
        <x-empty-state
            title="Belum ada catatan pembinaan"
            message="Catatan dari tim pendamping akan muncul di sini setelah sesi evaluasi berkala."
        />
    @endforelse
</div>
@endsection
