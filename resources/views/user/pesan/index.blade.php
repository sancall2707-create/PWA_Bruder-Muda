@extends('layouts.user-app')

@php
    $backUrl = route('dashboard');
    $pageTitle = 'Kotak Pesan Masuk';
    $title = 'Kotak Pesan Masuk';
@endphp

@section('content')
<div class="card" style="padding: 14px 18px; margin-bottom: 14px; text-align: center;">
    <p style="font-size: 13px; color: #475467; margin: 0; line-height: 1.45;">
        Kotak pesan privat dan arahan khusus langsung dari Pimpinan / TPPBM untuk Anda.
    </p>
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
        <div class="card" style="text-align: center; color: #667085; padding: 36px 16px;">
            <div style="width:48px; height:48px; border-radius:14px; background:#F2F4F7; color:#98A2B3; display:flex; align-items:center; justify-content:center; margin:0 auto 10px;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            </div>
            <p style="margin: 0; font-size: 14px; font-weight: 700; color:#344054;">Belum ada pesan khusus untuk Anda.</p>
            <p style="margin: 4px 0 0; font-size: 12px; color:#98A2B3;">Pesan privat dari Super Admin atau Admin TPPBM akan muncul di sini.</p>
        </div>
    @endforelse
</div>

<div style="margin-top: 14px;">
    {{ $messages->links() }}
</div>
@endsection
