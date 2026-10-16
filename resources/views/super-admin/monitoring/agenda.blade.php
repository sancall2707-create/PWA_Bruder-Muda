@extends('layouts.super-admin')

@section('content')
@include('super-admin.monitoring._toolbar', ['exportKind' => 'agenda', 'tampilan' => $tampilan])

@php
    $switchQuery = array_filter([
        'bulan' => $filters['bulan'],
        'tahun' => $filters['tahun'],
        'bruder' => $filters['bruder'],
    ], fn ($value) => $value !== null && $value !== '');
@endphp

<div style="display:flex; gap:8px; margin-top:12px;">
    <a href="{{ route('superadmin.monitoring.agenda', array_merge($switchQuery, ['tampilan' => 'daftar'])) }}" style="flex:1; text-align:center; padding:8px; border-radius:10px; font-size:12px; font-weight:700; text-decoration:none; {{ $tampilan === 'daftar' ? 'background:#1455C0;color:#fff;' : 'background:#fff;color:#344054;border:1px solid #E4E7EC;' }}">Daftar</a>
    <a href="{{ route('superadmin.monitoring.agenda', array_merge($switchQuery, ['tampilan' => 'kalender'])) }}" style="flex:1; text-align:center; padding:8px; border-radius:10px; font-size:12px; font-weight:700; text-decoration:none; {{ $tampilan === 'kalender' ? 'background:#1455C0;color:#fff;' : 'background:#fff;color:#344054;border:1px solid #E4E7EC;' }}">Kalender</a>
</div>

@if($tampilan === 'kalender')
    <div class="calendar-card" style="margin-top:12px;">
        <div class="calendar-header">
            <a href="{{ route('superadmin.monitoring.agenda', array_merge($switchQuery, ['tampilan' => 'kalender', 'bulan' => $calendar['prev_bulan'], 'tahun' => $calendar['prev_tahun']])) }}" aria-label="Bulan sebelumnya" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;text-decoration:none;color:inherit;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </a>
            <span class="calendar-month">{{ $calendar['month_label'] }}</span>
            <a href="{{ route('superadmin.monitoring.agenda', array_merge($switchQuery, ['tampilan' => 'kalender', 'bulan' => $calendar['next_bulan'], 'tahun' => $calendar['next_tahun']])) }}" aria-label="Bulan berikutnya" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;text-decoration:none;color:inherit;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </a>
        </div>
        <div class="calendar-grid">
            <div class="calendar-day-label" style="color: var(--fic-danger);">Min</div>
            <div class="calendar-day-label">Sen</div>
            <div class="calendar-day-label">Sel</div>
            <div class="calendar-day-label">Rab</div>
            <div class="calendar-day-label">Kam</div>
            <div class="calendar-day-label">Jum</div>
            <div class="calendar-day-label">Sab</div>
            @for ($blank = 0; $blank < $calendar['leading']; $blank++)
                <div class="calendar-day empty"></div>
            @endfor
            @for ($day = 1; $day <= $calendar['days']; $day++)
                @php
                    $dayDate = sprintf('%04d-%02d-%02d', $filters['tahun'], $filters['bulan'], $day);
                @endphp
                <a href="{{ route('superadmin.monitoring.agenda', array_merge($switchQuery, ['tampilan' => 'kalender', 'tanggal' => $dayDate])) }}" class="calendar-day {{ $calendar['selected_day'] === $day ? 'selected' : '' }}" style="text-decoration:none;color:inherit;">
                    <span>{{ $day }}</span>
                    <div class="calendar-dots">
                        @if(!empty($calendar['dots'][$day]['bimbingan']))
                            <span class="dot blue"></span>
                        @endif
                        @if(!empty($calendar['dots'][$day]['wawanhati']))
                            <span class="dot cyan"></span>
                        @endif
                        @if(!empty($calendar['dots'][$day]['lainnya']))
                            <span class="dot gold"></span>
                        @endif
                    </div>
                </a>
            @endfor
        </div>
    </div>
    <div style="margin-top:12px; font-size:13px; font-weight:700; color:#101828;">{{ $calendar['selected_label'] }}</div>
    @include('super-admin.monitoring._agenda-list', ['agendas' => $calendar['selected_items']])
@else
    @include('super-admin.monitoring._agenda-list', ['agendas' => $agendas])
@endif
@endsection
