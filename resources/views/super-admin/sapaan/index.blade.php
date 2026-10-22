@extends('layouts.super-admin')

@section('content')
@php
    $editing = $editing ?? null;
    $isAnnouncement = old('kind', $editing->kind ?? 'sapaan') === 'pengumuman';
@endphp

@if($errors->any())
    <div style="background:#FEF3F2; border:1px solid #FECDCA; color:#B42318; padding:12px 14px; border-radius:12px; font-size:13px; font-weight:700;">
        {{ $errors->first() }}
    </div>
@endif

<form method="POST" action="{{ $editing ? route('superadmin.sapaan.update', $editing) : route('superadmin.sapaan.store') }}" enctype="multipart/form-data" style="display:flex; flex-direction:column; gap:12px; background:#fff; border:1px solid #E4E7EC; border-radius:16px; padding:16px;">
    @csrf
    @if($editing)
        @method('PUT')
    @endif

    <label style="display:flex; flex-direction:column; gap:6px; font-size:12px; font-weight:700; color:#475467;">
        Jenis publikasi
        <select name="kind" aria-label="Jenis publikasi" style="padding:12px 14px; border-radius:12px; border:1px solid #D0D5DD; font-size:14px; color:#101828;">
            <option value="sapaan" {{ old('kind', $editing->kind ?? 'sapaan') === 'sapaan' ? 'selected' : '' }}>Sapaan rohani</option>
            <option value="pengumuman" {{ $isAnnouncement ? 'selected' : '' }}>Pengumuman / jadwal bersama</option>
        </select>
    </label>

    <label style="display:flex; flex-direction:column; gap:6px; font-size:12px; font-weight:700; color:#475467;">
        Judul
        <input name="title" value="{{ old('title', $editing->title ?? '') }}" required maxlength="255" style="padding:12px 14px; border-radius:12px; border:1px solid #D0D5DD; font-size:14px;">
    </label>

    <label style="display:flex; flex-direction:column; gap:6px; font-size:12px; font-weight:700; color:#475467;">
        Nama penulis
        <input name="author_name" value="{{ old('author_name', $editing->author_name ?? 'Br. Agustinus Giwal Santoso, FIC') }}" required maxlength="255" style="padding:12px 14px; border-radius:12px; border:1px solid #D0D5DD; font-size:14px;">
    </label>

    <label style="display:flex; flex-direction:column; gap:6px; font-size:12px; font-weight:700; color:#475467;">
        Peran
        <select name="author_role" aria-label="Peran" style="padding:12px 14px; border-radius:12px; border:1px solid #D0D5DD; font-size:14px; color:#101828;">
            @foreach(['Pemimpin Provinsial FIC Indonesia', 'TPPBM'] as $role)
                <option value="{{ $role }}" {{ old('author_role', $editing->author_role ?? 'Pemimpin Provinsial FIC Indonesia') === $role ? 'selected' : '' }}>{{ $role }}</option>
            @endforeach
        </select>
    </label>

    <label style="display:flex; flex-direction:column; gap:6px; font-size:12px; font-weight:700; color:#475467;">
        Foto (opsional, jpg/png, maksimal 5 MB)
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
    </label>
    @if($editing?->image)
        <div style="background:#F8F9FC; border:1px solid #E4E7EC; border-radius:12px; padding:12px; display:flex; flex-direction:column; gap:8px;">
            <div style="font-size:12px; font-weight:700; color:#344054;">Foto Publikasi Saat Ini:</div>
            <img src="{{ route('sapaan-provinsial.foto', $editing) }}" alt="Foto publikasi saat ini" style="width:100%; height:auto; max-height:320px; object-fit:contain; border-radius:8px; display:block; background:#EEF2F6;">
            <div style="font-size:11px; color:#667085;">Pilih berkas baru pada formulir foto di atas untuk mengganti foto ini.</div>
            <label style="display:inline-flex; align-items:center; gap:8px; font-size:12px; font-weight:700; color:#B42318; cursor:pointer; margin-top:4px;">
                <input type="checkbox" name="remove_image" value="1" {{ old('remove_image') ? 'checked' : '' }}>
                Hapus foto saat ini (jadikan sapaan tanpa foto lampiran)
            </label>
        </div>
    @endif

    <div style="display:flex; gap:8px;">
        <label style="flex:1; display:flex; flex-direction:column; gap:6px; font-size:12px; font-weight:700; color:#475467;">
            Tanggal kegiatan
            <input type="date" name="tanggal_kegiatan" value="{{ old('tanggal_kegiatan', optional($editing?->starts_at)->format('Y-m-d')) }}" style="padding:12px 14px; border-radius:12px; border:1px solid #D0D5DD; font-size:14px;">
        </label>
        <label style="width:120px; display:flex; flex-direction:column; gap:6px; font-size:12px; font-weight:700; color:#475467;">
            Waktu
            <input type="time" name="waktu_kegiatan" value="{{ old('waktu_kegiatan', optional($editing?->starts_at)->format('H:i')) }}" style="padding:12px 14px; border-radius:12px; border:1px solid #D0D5DD; font-size:14px;">
        </label>
    </div>
    <div style="font-size:12px; color:#667085; margin-top:-6px;">Tanggal dipakai untuk pengumuman dan masuk ke pengingat bruder.</div>

    <label style="display:flex; flex-direction:column; gap:6px; font-size:12px; font-weight:700; color:#475467;">
        Pesan
        <textarea name="content" rows="6" required style="padding:12px 14px; border-radius:12px; border:1px solid #D0D5DD; font-size:14px; font-family:inherit;">{{ old('content', $editing->content ?? '') }}</textarea>
    </label>

    <div style="display:flex; gap:10px;">
        <button type="submit" style="flex:1; border:none; border-radius:14px; padding:15px; font-weight:800; font-size:15px; background:#1455C0; color:#fff; cursor:pointer;">
            {{ $editing ? 'Simpan & Publikasikan' : 'Publikasikan' }}
        </button>
        @if($editing)
            <a href="{{ route('superadmin.sapaan.index') }}" style="padding:15px 20px; border-radius:14px; border:1px solid #D0D5DD; background:#fff; color:#475467; font-weight:700; font-size:14px; text-decoration:none; text-align:center; display:flex; align-items:center; justify-content:center;">Batal</a>
        @endif
    </div>
</form>

<div style="display:flex; flex-direction:column; gap:10px; margin-top:16px;">
    @forelse($messages as $item)
        <div style="background:#fff; border:1px solid #E4E7EC; border-radius:14px; padding:14px;">
            <div style="display:flex; justify-content:space-between; gap:8px; align-items:flex-start;">
                <div>
                    <div style="font-weight:800; color:#101828;">{{ $item->title }}</div>
                    <div style="font-size:12px; color:#667085; margin-top:2px;">
                        {{ $item->kind === 'pengumuman' ? 'Pengumuman' : 'Sapaan' }}
                        &bull; {{ $item->author_name }}
                        &bull; {{ $item->publishedLabel() }}
                        @if($item->scheduleLabel())
                            &bull; {{ $item->scheduleLabel() }}
                        @endif
                    </div>
                </div>
                <span style="padding:4px 10px; border-radius:999px; font-size:10px; font-weight:700; white-space:nowrap; {{ $item->is_active ? 'background:#ECFDF3;color:#027A48;border:1px solid #ABEFC6;' : 'background:#F2F4F7;color:#667085;border:1px solid #E4E7EC;' }}">
                    {{ $item->is_active ? 'Tayang' : 'Ditarik' }}
                </span>
            </div>
            <div style="display:flex; gap:8px; margin-top:12px;">
                <a href="{{ route('superadmin.sapaan.edit', $item) }}" style="flex:1; text-align:center; padding:10px; border-radius:10px; background:#EFF8FF; color:#1455C0; font-weight:700; font-size:13px; text-decoration:none; display:flex; align-items:center; justify-content:center;">Ubah</a>
                <form method="POST" action="{{ route('superadmin.sapaan.status', $item) }}" style="flex:1; margin:0;">
                    @csrf
                    <button type="submit" style="width:100%; padding:10px; border-radius:10px; border:1px solid #E4E7EC; background:#fff; color:#344054; font-weight:700; font-size:13px; cursor:pointer;">
                        {{ $item->is_active ? 'Tarik' : 'Tayangkan' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('superadmin.sapaan.destroy', $item) }}" onsubmit="return confirm('Hapus publikasi ini secara permanen?');" style="flex:1; margin:0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="width:100%; padding:10px; border-radius:10px; border:1px solid #FECDCA; background:#FEF3F2; color:#B42318; font-weight:700; font-size:13px; cursor:pointer;">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div style="text-align:center; color:#667085; padding:20px; background:#fff; border:1px solid #E4E7EC; border-radius:14px;">Belum ada sapaan atau pengumuman.</div>
    @endforelse
</div>
@endsection
