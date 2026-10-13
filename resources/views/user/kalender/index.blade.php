@extends('layouts.user-app')

@php
    $title = 'Kalender Kegiatan';
@endphp

@section('content')
<!-- Calendar Card -->
<div class="calendar-card">
    <div class="calendar-header">
        <a href="{{ route('kalender', ['month' => $prevMonth]) }}" class="btn-back" style="width: 32px; height: 32px;" aria-label="Bulan Sebelumnya">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
        </a>

        <span class="calendar-month">{{ $monthLabel }}</span>

        <a href="{{ route('kalender', ['month' => $nextMonth]) }}" class="btn-back" style="width: 32px; height: 32px;" aria-label="Bulan Berikutnya">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>
    </div>

    <!-- Day Headers: Min, Sen, Sel, Rab, Kam, Jum, Sab -->
    <div class="calendar-grid">
        <div class="calendar-day-label" style="color: var(--fic-danger);">Min</div>
        <div class="calendar-day-label">Sen</div>
        <div class="calendar-day-label">Sel</div>
        <div class="calendar-day-label">Rab</div>
        <div class="calendar-day-label">Kam</div>
        <div class="calendar-day-label">Jum</div>
        <div class="calendar-day-label">Sab</div>

        @for ($blank = 0; $blank < $leadingBlanks; $blank++)
            <div class="calendar-day empty"></div>
        @endfor

        @for ($day = 1; $day <= $daysInMonth; $day++)
            @php
                $dayDate = $month->copy()->day($day)->toDateString();
                $isSelected = $selectedDate->day === $day;
            @endphp
            <a href="{{ route('kalender', ['month' => $monthKey, 'date' => $dayDate]) }}" class="calendar-day {{ $isSelected ? 'selected' : '' }}" style="text-decoration: none; color: inherit;">
                <span>{{ $day }}</span>
                <div class="calendar-dots">
                    @if(!empty($dots['bimbingan'][$day]))
                        <span class="dot blue"></span>
                    @endif
                    @if(!empty($dots['wawanhati'][$day]))
                        <span class="dot cyan"></span>
                    @endif
                    @if(!empty($dots['lainnya'][$day]))
                        <span class="dot gold"></span>
                    @endif
                </div>
            </a>
        @endfor
    </div>

    <!-- Legend -->
    <div style="display: flex; justify-content: center; gap: 14px; margin-top: 18px; padding-top: 14px; border-top: 1px solid var(--fic-border); font-size: 11px;">
        <div style="display: flex; align-items: center; gap: 4px;">
            <span class="dot blue" style="width: 6px; height: 6px;"></span>
            <span>Bimbingan</span>
        </div>
        <div style="display: flex; align-items: center; gap: 4px;">
            <span class="dot cyan" style="width: 6px; height: 6px;"></span>
            <span>Wawanhati</span>
        </div>
        <div style="display: flex; align-items: center; gap: 4px;">
            <span class="dot gold" style="width: 6px; height: 6px;"></span>
            <span>Agenda Lain</span>
        </div>
    </div>
</div>

<!-- Agenda Tanggal Terpilih -->
<div style="display: flex; flex-direction: column; gap: 12px; margin-top: 4px;">
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0 4px;">
        <span style="font-size: 13px; font-weight: 700; color: var(--fic-text-primary);">
            Agenda: {{ $selectedLabel }}
        </span>
        <span class="badge badge-scheduled">{{ $selectedDateAgendas->count() }} Kegiatan</span>
    </div>

    @forelse($selectedDateAgendas as $agenda)
        @php
            $borderColor = match ($agenda->category) {
                'bimbingan' => 'var(--fic-primary)',
                'wawanhati' => 'var(--fic-cyan)',
                default => 'var(--fic-gold)',
            };
        @endphp
        <div class="item-card" style="border-left: 4px solid {{ $borderColor }};">
            <div class="item-card-header">
                <div>
                    <div class="item-card-title">{{ $agenda->title }}</div>
                    <div style="font-size: 12px; color: var(--fic-text-secondary); margin-top: 2px;">
                        {{ $agenda->person }}
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
                    <span>{{ $agenda->time_formatted }}</span>
                </div>
                <div class="meta-row">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    <span>{{ $agenda->location }}</span>
                </div>
            </div>
        </div>
    @empty
        <x-empty-state
            title="Tidak ada kegiatan pada tanggal ini"
            message="Jadwal yang Anda simpan akan muncul di tanggal yang dipilih."
        />
    @endforelse
</div>
@endsection
