@extends('layouts.user-app')

@php
    $title = 'Sapaan Provinsial';
    $headerGreetingLabel = 'Sapaan Provinsial';
    $hideBottomNav = true;
@endphp

@section('content')
<div class="card" style="padding: 24px 20px;">
    <!-- Hero Motif / Header Sapaan -->
    <div style="background: linear-gradient(135deg, var(--fic-dark-blue) 0%, var(--fic-primary) 100%); border-radius: 14px; padding: 24px 16px; text-align: center; color: #ffffff; margin-bottom: 20px;">
        <div style="width: 52px; height: 52px; border-radius: 50%; background: rgba(255, 255, 255, 0.2); border: 2px solid rgba(255, 255, 255, 0.4); margin: 0 auto 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 800;">
            FIC
        </div>
        <span style="font-size: 11px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; color: var(--fic-gold);">
            Pesan Penggembalaan
        </span>
        <h2 style="font-size: 17px; font-weight: 800; line-height: 1.3; margin-top: 6px;">
            {{ $message->title }}
        </h2>
    </div>

    <!-- Metadata Pengirim -->
    <div style="display: flex; align-items: center; gap: 12px; padding-bottom: 16px; border-bottom: 1px solid var(--fic-border); margin-bottom: 16px;">
        <div style="width: 44px; height: 44px; border-radius: 50%; background-color: var(--fic-soft-blue); color: var(--fic-primary); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 15px; flex-shrink: 0;">
            AS
        </div>
        <div>
            <div style="font-size: 14px; font-weight: 700; color: var(--fic-text-primary);">
                {{ $message->author_name }}
            </div>
            <div style="font-size: 12px; color: var(--fic-text-secondary);">
                {{ $message->author_role }} &bull; {{ $message->published_at }}
            </div>
        </div>
    </div>

    <!-- Isi Sapaan -->
    <div style="display: flex; flex-direction: column; gap: 14px; font-size: 14px; color: #374151; line-height: 1.6; margin-bottom: 24px;">
        @foreach($message->content as $paragraph)
            <p>{{ $paragraph }}</p>
        @endforeach
    </div>

    <!-- CTA Button ke Beranda -->
    <div style="margin-top: 8px; padding-bottom: 8px;">
        <a href="{{ route('dashboard') }}" class="btn-primary">
            MASUK KE BERANDA
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>
    </div>
</div>
@endsection
