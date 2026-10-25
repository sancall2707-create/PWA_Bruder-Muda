<div style="display:flex; flex-direction:column; gap:10px; margin-top:12px;">
    @forelse($agendas as $agenda)
        <div style="background:#fff; border:1px solid #E4E7EC; border-radius:14px; padding:14px; box-shadow:0 2px 6px rgba(16,24,40,0.03);">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:8px;">
                <div style="font-weight:700; color:#101828;">{{ $agenda->bruder }}</div>
                <span style="padding:4px 10px; border-radius:999px; font-size:10px; font-weight:700; white-space:nowrap; {{ $agenda->status_done ? 'background:#ECFDF3;color:#027A48;border:1px solid #ABEFC6;' : 'background:#EFF8FF;color:#1455C0;border:1px solid #B2DDFF;' }}">{{ $agenda->status_label }}</span>
            </div>
            <div style="font-size:12px; color:#667085; margin-top:4px;">{{ $agenda->type }} &bull; {{ $agenda->date_label }} &bull; {{ $agenda->time_label }}</div>
            <div style="font-size:12px; color:#344054; margin-top:6px;">{{ $agenda->counterpart }}@if($agenda->location !== '') &bull; {{ $agenda->location }}@endif</div>
            @if(!empty($agenda->summary))
                <div style="margin-top:6px; font-size:12px; color:#667085; font-style:italic;">{{ $agenda->summary }}</div>
            @endif

            @if(!empty($agenda->id) && $agenda->id > 0)
                <div style="display:flex; justify-content:flex-end; margin-top:10px; padding-top:8px; border-top:1px dashed #E4E7EC;">
                    <form method="POST" action="{{ route('superadmin.monitoring.agenda.destroySingle', ['category' => $agenda->category, 'id' => $agenda->id]) }}" onsubmit="return confirm('Hapus agenda ini?');" style="margin:0;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="display:inline-flex; align-items:center; gap:5px; background:#FEF2F2; color:#B42318; border:1px solid #FECDCA; border-radius:8px; padding:5px 10px; font-size:11px; font-weight:700; cursor:pointer; font-family:inherit;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6l-1 14H6L5 6"></path>
                                <path d="M10 11v6"></path><path d="M14 11v6"></path>
                                <path d="M9 6V4h6v2"></path>
                            </svg>
                            Hapus
                        </button>
                    </form>
                </div>
            @endif
        </div>
    @empty
        <div style="text-align:center; color:#667085; padding:24px; background:#fff; border:1px solid #E4E7EC; border-radius:14px;">Belum ada agenda sesuai filter.</div>
    @endforelse
</div>
