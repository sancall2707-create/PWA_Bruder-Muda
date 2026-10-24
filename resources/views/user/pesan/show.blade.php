@extends('layouts.user-app')

@php
    $backUrl = route('pesan.index');
    $pageTitle = 'Pesan Khusus';
    $title = $message->title;
@endphp

@section('content')
<div class="card" style="padding: 20px 18px;">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #E4E7EC; padding-bottom: 12px; margin-bottom: 14px;">
        <span style="font-size: 11px; font-weight: 800; color: var(--fic-gold); text-transform: uppercase; letter-spacing: 0.5px;">
            Pesan Khusus
        </span>
        <span style="font-size: 11px; color: #667085;">
            {{ $message->created_at->translatedFormat('d F Y, H:i') }} WIB
        </span>
    </div>

    <h2 style="font-size: 18px; font-weight: 800; color: #101828; margin: 0 0 8px; line-height: 1.3;">
        {{ $message->title }}
    </h2>

    <div style="font-size: 12px; color: #475467; margin-bottom: 16px; padding: 8px 12px; background: #F8F9FC; border-radius: 8px;">
        Pengirim: <strong>{{ $message->sender->name ?? 'Pimpinan / TPPBM' }}</strong>
    </div>

    <div style="font-size: 14px; line-height: 1.65; color: #344054; white-space: pre-line; margin-bottom: 24px;">
        {{ $message->content }}
    </div>

    <div>
        <a href="{{ route('pesan.index') }}" class="btn-primary" style="display:block; text-align: center; text-decoration: none; padding: 12px 16px; font-size: 13px; font-weight: 700; border-radius: 12px;">
            ← Kembali ke Kotak Pesan
        </a>
    </div>
</div>
@endsection
