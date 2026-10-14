<div style="display:flex; flex-direction:column; gap:10px; margin-top:12px;">
    @forelse($agendas as $agenda)
        <div style="background:#fff; border:1px solid #E4E7EC; border-radius:14px; padding:14px;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:8px;">
                <div style="font-weight:700; color:#101828;">{{ $agenda->bruder }}</div>
                <span style="padding:4px 10px; border-radius:999px; font-size:10px; font-weight:700; white-space:nowrap; {{ $agenda->status_done ? 'background:#ECFDF3;color:#027A48;border:1px solid #ABEFC6;' : 'background:#EFF8FF;color:#1455C0;border:1px solid #B2DDFF;' }}">{{ $agenda->status_label }}</span>
            </div>
            <div style="font-size:12px; color:#667085; margin-top:4px;">{{ $agenda->type }} &bull; {{ $agenda->date_label }} &bull; {{ $agenda->time_label }}</div>
            <div style="font-size:12px; color:#344054; margin-top:6px;">{{ $agenda->counterpart }}@if($agenda->location !== '') &bull; {{ $agenda->location }}@endif</div>
        </div>
    @empty
        <div style="text-align:center; color:#667085; padding:24px; background:#fff; border:1px solid #E4E7EC; border-radius:14px;">Belum ada agenda sesuai filter.</div>
    @endforelse
</div>
