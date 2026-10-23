@extends('layouts.user-app')

@php
    $pageTitle = $message ? $message->label : 'Sapaan';
    $title = $pageTitle;
    $headerGreetingLabel = $pageTitle;
    $hideBottomNav = true;
@endphp

@section('content')
<div class="card" style="padding: 24px 20px;">
    @if($message)
        <div style="background: linear-gradient(135deg, rgba(0, 62, 151, 0.9) 0%, rgba(20, 85, 192, 0.78) 100%); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.28); border-radius: 14px; padding: 24px 16px; text-align: center; color: #ffffff; margin-bottom: 20px;">
            @if($message->image_url)
                <img src="{{ $message->image_url }}" alt="Foto sapaan" style="width: 100%; height: auto; max-height: 640px; object-fit: contain; border-radius: 12px; margin-bottom: 14px; display: block;">
            @else
                <img src="/images/logo-fic.png" alt="Lambang FIC" width="52" height="52" style="width: 52px; height: 52px; object-fit: contain; margin: 0 auto 12px; display: block;">
            @endif
            <span style="font-size: 11px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; color: var(--fic-gold);">
                Pesan Penggembalaan
            </span>
            <h2 style="font-size: 17px; font-weight: 800; line-height: 1.3; margin-top: 6px;">
                {{ $message->title }}
            </h2>
        </div>

        <div style="display: flex; align-items: center; gap: 12px; padding-bottom: 16px; border-bottom: 1px solid var(--fic-border); margin-bottom: 16px;">
            <div style="width: 44px; height: 44px; border-radius: 50%; background-color: var(--fic-soft-blue); color: var(--fic-primary); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 15px; flex-shrink: 0;">
                {{ $message->initials }}
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

        <div style="display: flex; flex-direction: column; gap: 14px; font-size: 14px; color: #374151; line-height: 1.6; margin-bottom: 8px;">
            @foreach($message->content as $paragraph)
                <p style="margin: 0;">{{ $paragraph }}</p>
            @endforeach
        </div>
    @else
        <div style="text-align: center; color: var(--fic-text-secondary); padding: 12px 0 20px;">
            <div style="font-weight: 800; color: var(--fic-text-primary); margin-bottom: 6px;">Belum ada sapaan</div>
            <div style="font-size: 13px;">Sapaan provinsial akan tampil di sini setelah dipublikasikan.</div>
        </div>
    @endif

    @if($announcements->isNotEmpty())
        <div style="margin-top: 18px; padding-top: 16px; border-top: 1px solid var(--fic-border);">
            <div style="font-size: 12px; font-weight: 800; letter-spacing: 0.4px; color: var(--fic-gold); text-transform: uppercase; margin-bottom: 10px;">Pengumuman</div>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                @foreach($announcements as $announcement)
                    <div style="background: #FFF8E1; border: 1px solid #FFD966; border-radius: 12px; padding: 12px;">
                        <div style="font-weight: 800; color: #101828;">{{ $announcement->title }}</div>
                        <div style="font-size: 12px; color: #667085; margin-top: 2px;">
                            {{ $announcement->author_name }}
                            @if($announcement->scheduleLabel())
                                &bull; {{ $announcement->scheduleLabel() }}
                            @endif
                        </div>
                        <div style="font-size: 13px; color: #374151; margin-top: 8px; white-space: pre-line;">{{ $announcement->content }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div style="margin-top: 20px; padding-bottom: 8px;">
        <a href="{{ route('dashboard') }}" class="btn-primary">
            MASUK KE BERANDA
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>
    </div>
</div>
@endsection
