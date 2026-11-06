@props([
    'name' => 'meeting_time',
    'value' => '10:00',
    'id' => null,
    'required' => true,
])

@php
    $inputId = $id ?? $name;
    $rawVal = old($name, $value ?? '10:00');
    // Pisahkan HH:mm
    $parts = explode(':', (string) $rawVal);
    $selectedHour = isset($parts[0]) && $parts[0] !== '' ? str_pad($parts[0], 2, '0', STR_PAD_LEFT) : '10';
    $selectedMinute = isset($parts[1]) && $parts[1] !== '' ? str_pad($parts[1], 2, '0', STR_PAD_LEFT) : '00';

    // Jam 00-23
    $hours = array_map(fn($h) => str_pad($h, 2, '0', STR_PAD_LEFT), range(0, 23));

    // Menit kelipatan 5 (00, 05, ..., 55)
    $minutes = array_map(fn($m) => str_pad($m, 2, '0', STR_PAD_LEFT), range(0, 55, 5));

    // Jika menit eksisting tidak pas kelipatan 5 (misal data lama 10:12), tambahkan agar tidak hilang
    if (!in_array($selectedMinute, $minutes, true)) {
        $minutes[] = $selectedMinute;
        sort($minutes);
    }
@endphp

<div class="time-picker-select-group" style="display:flex; align-items:center; gap:8px;">
    <!-- Hidden input yang menyimpan nilai gabungan HH:mm yang dikirim ke backend -->
    <input
        type="hidden"
        id="{{ $inputId }}"
        name="{{ $name }}"
        value="{{ $selectedHour }}:{{ $selectedMinute }}"
        @if($required) required @endif
    >

    <!-- Dropdown Jam -->
    <div style="flex:1; position:relative;">
        <select
            id="{{ $inputId }}_hour"
            aria-label="Pilih Jam"
            class="form-input"
            style="width:100%; box-sizing:border-box; padding:12px 14px; border-radius:12px; border:1px solid #D0D5DD; font-size:14px; font-weight:600; color:#101828; background:#fff; cursor:pointer;"
            onchange="window.updateTimePickerValue('{{ $inputId }}')"
        >
            @foreach($hours as $h)
                <option value="{{ $h }}" {{ $h === $selectedHour ? 'selected' : '' }}>
                    Jam {{ $h }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Pemisah Titik Dua -->
    <span style="font-weight:800; font-size:18px; color:#667085; user-select:none;">:</span>

    <!-- Dropdown Menit (interval 5 menit) -->
    <div style="flex:1; position:relative;">
        <select
            id="{{ $inputId }}_minute"
            aria-label="Pilih Menit"
            class="form-input"
            style="width:100%; box-sizing:border-box; padding:12px 14px; border-radius:12px; border:1px solid #D0D5DD; font-size:14px; font-weight:600; color:#101828; background:#fff; cursor:pointer;"
            onchange="window.updateTimePickerValue('{{ $inputId }}')"
        >
            @foreach($minutes as $m)
                <option value="{{ $m }}" {{ $m === $selectedMinute ? 'selected' : '' }}>
                    Menit {{ $m }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Keterangan Zona Waktu -->
    <span style="font-size:12px; font-weight:700; color:#667085; padding:0 4px; user-select:none;">WIB</span>
</div>

@once
    <script>
        window.updateTimePickerValue = function(inputId) {
            const h = document.getElementById(inputId + '_hour');
            const m = document.getElementById(inputId + '_minute');
            const target = document.getElementById(inputId);
            if (h && m && target) {
                target.value = h.value + ':' + m.value;
            }
        };
    </script>
@endonce
