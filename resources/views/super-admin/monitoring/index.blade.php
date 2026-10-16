@extends('layouts.super-admin')

@section('content')
@include('super-admin.monitoring._toolbar', ['exportKind' => 'laporan'])

<div style="display:flex; flex-direction:column; gap:10px; margin-top:14px;">
    @forelse($reports as $report)
        <div style="background:#fff; border:1px solid #E4E7EC; border-radius:14px; padding:14px; box-shadow:0 2px 6px rgba(16,24,40,0.03);">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:8px;">
                <div style="flex:1;">
                    <div style="font-weight:700; color:#101828;">{{ $report->bruder }}</div>
                    <div style="font-size:12px; color:#667085; margin-top:2px;">{{ $report->type }} &bull; {{ $report->date_label }}</div>
                </div>
                <span style="padding:4px 10px; border-radius:999px; font-size:10px; font-weight:700; white-space:nowrap; {{ $report->status_done ? 'background:#ECFDF3;color:#027A48;border:1px solid #ABEFC6;' : 'background:#EFF8FF;color:#1455C0;border:1px solid #B2DDFF;' }}">{{ $report->status_label }}</span>
            </div>
            @if($report->counterpart !== '')
                <div style="margin-top:8px; font-size:12px; color:#344054;">{{ $report->counterpart }}</div>
            @endif
            @if($report->preview !== '')
                <div style="margin-top:8px; padding:10px; background:#F8FAF9; border-left:3px solid #1455C0; border-radius:0 8px 8px 0; font-size:12px; color:#374151;">{{ $report->preview }}</div>
            @endif
        </div>
    @empty
        <div style="text-align:center; color:#667085; padding:24px; background:#fff; border:1px solid #E4E7EC; border-radius:14px;">Belum ada laporan sesuai filter.</div>
    @endforelse
</div>
@endsection
