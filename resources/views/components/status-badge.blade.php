@props(['status' => 'draft'])

@php
    $normalized = strtolower(trim($status));
    $badgeClass = match ($normalized) {
        'terjadwal', 'scheduled' => 'badge-scheduled',
        'draft' => 'badge-draft',
        'terkirim', 'submitted' => 'badge-terkirim',
        'selesai', 'completed', 'done' => 'badge-done',
        'dibatalkan', 'cancelled', 'batal' => 'badge-cancelled',
        'bimbingan' => 'badge-scheduled',
        'wawanhati' => 'badge-cyan',
        'lainnya', 'agenda' => 'badge-gold',
        default => 'badge-draft',
    };

    $label = ucfirst($status);
@endphp

<span class="badge {{ $badgeClass }}">
    {{ $label }}
</span>
