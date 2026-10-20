@extends('layouts.user-app')

@php
    $title = 'Pesan Khusus';
    $headerGreetingLabel = 'Pesan Khusus Pimpinan/TPPBM';
@endphp

@section('content')
<div class="card" style="padding: 16px 18px; margin-bottom: 14px;">
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h2 style="font-size: 16px; font-weight: 800; color: #101828; margin: 0;">Kotak Pesan Masuk</h2>
            <p style="font-size: 12px; color: #667085; margin: 2px 0 0;">Arahan dan pesan khusus privat untuk Anda.</p>
        </div>
        <a href="{{ route('dashboard') }}" style="font-size: 12px; font-weight: 700; color: #1455C0; text-decoration: none;">← Ke Beranda</a>
    </div>
</div>

<div style="display: flex; flex-direction: column; gap: 10px;">
    @forelse($messages as $msg)
        <a href="{{ route('pesan.show', $msg) }}" class="card" style="display: block; text-decoration: none; color: inherit; padding: 14px 16px; border-left: 4px solid {{ $msg->read_at ? '#D0D5DD' : '#B57900' }};">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px;">
                <div>
                    <div style="font-weight: 800; font-size: 14px; color: #101828;">{{ $msg->title }}</div>
                    <div style="font-size: 11px; color: #667085; margin-top: 2px;">
                        Dari: <strong>{{ $msg->sender->name ?? 'Pimpinan / TPPBM' }}</strong> &bull; {{ $msg->created_at->translatedFormat('d M Y, H:i') }}
                    </div>
                </div>
                @if(!$msg->read_at)
                    <span style="font-size: 10px; font-weight: 800; background: #FEF3F2; color: #B42318; border: 1px solid #FECDCA; padding: 2px 8px; border-radius: 999px; white-space: nowrap;">
                        Baru
                    </span>
                @else
                    <span style="font-size: 10px; font-weight: 700; background: #F2F4F7; color: #667085; padding: 2px 8px; border-radius: 999px; white-space: nowrap;">
                        Dibaca
                    </span>
                @endif
            </div>
            <div style="font-size: 13px; color: #475467; margin-top: 8px; line-height: 1.45;">
                {{ \Illuminate\Support\Str::limit($msg->content, 110) }}
            </div>
        </a>
    @empty
        <div class="card" style="text-align: center; color: #667085; padding: 32px 16px;">
            <p style="margin: 0; font-size: 14px; font-weight: 600;">Belum ada pesan khusus untuk Anda.</p>
        </div>
    @endforelse
</div>

<div style="margin-top: 14px;">
    {{ $messages->links() }}
</div>
@endsection
