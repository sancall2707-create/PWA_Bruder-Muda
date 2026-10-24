@extends('layouts.user-app')

@php
    $backUrl = route('dashboard');
    $pageTitle = 'Agenda Pribadi';
@endphp

@section('content')
<style>
    .agenda-add {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 8px;
        margin-top: 14px;
    }
    .agenda-add a {
        min-height: 62px;
        border-radius: 12px;
        padding: 8px 6px;
        text-align: center;
        font-size: 11px;
        font-weight: 800;
        line-height: 1.25;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 2px;
        box-sizing: border-box;
    }
    .agenda-add .plus {
        font-size: 16px;
        line-height: 1;
    }
    .agenda-add .bimbingan { background: #EFF4FF; color: #1455C0; }
    .agenda-add .wawanhati { background: #E0F7FA; color: #0E7490; }
    .agenda-add .lainnya { background: #FFF6E8; color: #B57900; }
    .agenda-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 12px;
        padding-top: 10px;
        border-top: 1px solid #EAECF0;
    }
    .agenda-actions a,
    .agenda-actions button {
        width: auto;
        min-height: 32px;
        padding: 0 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        font-family: inherit;
        cursor: pointer;
        box-sizing: border-box;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }
    .agenda-actions a {
        background: #fff;
        color: #344054;
        border: 1px solid #D0D5DD;
    }
    .agenda-actions button {
        background: #FEF3F2;
        color: #B42318;
        border: 1px solid #FECDCA;
    }
</style>

<div class="card" style="padding: 16px;">
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px;">
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

    <div class="agenda-add">
        <a href="{{ route('agenda.bimbingan.create') }}" class="bimbingan">
            <span class="plus">+</span>
            <span>Bimbingan Rohani</span>
        </a>
        <a href="{{ route('agenda.wawanhati.create') }}" class="wawanhati">
            <span class="plus">+</span>
            <span>Wawanhati Piko</span>
        </a>
        <a href="{{ route('agenda.lainnya.create') }}" class="lainnya">
            <span class="plus">+</span>
            <span>Agenda Lainnya</span>
        </a>
    </div>
</div>

@if(session('success'))
    <div style="background-color: var(--fic-green-soft); border: 1px solid rgba(53, 168, 102, 0.3); color: #027A48; padding: 12px 16px; border-radius: 12px; font-size: 13px; font-weight: 600;">
        {{ session('success') }}
    </div>
@endif

<div style="display: flex; flex-direction: column; gap: 12px;">
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

            <div class="agenda-actions">
                <a href="{{ route('agenda.edit', ['type' => $agenda->category, 'schedule' => $agenda->id]) }}">Edit</a>
                <form method="POST" action="{{ route('agenda.destroy', ['type' => $agenda->category, 'schedule' => $agenda->id]) }}" onsubmit="return confirm('Hapus agenda ini dari jadwal Anda?');" style="margin: 0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Hapus</button>
                </form>
            </div>
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
