@extends('layouts.user-app')

@php
    $title = 'Profil Pengguna';
@endphp

@section('content')
@if(session('success'))
    <div style="background-color: var(--fic-green-soft); border: 1px solid rgba(53, 168, 102, 0.3); color: #027A48; padding: 12px 16px; border-radius: 12px; font-size: 13px; font-weight: 600;">
        {{ session('success') }}
    </div>
@endif

<div class="card" style="text-align: center; padding: 28px 20px;">
    <!-- Large Avatar with Initials -->
    @php
        $initials = 'BM';
        if ($user && !empty($user->name)) {
            $words = explode(' ', trim($user->name));
            $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
        }
    @endphp
    @if ($photoUrl)
        <img src="{{ $photoUrl }}" alt="Foto profil {{ $user->name }}" width="76" height="76" style="width: 76px; height: 76px; border-radius: 50%; object-fit: cover; display: block; margin: 0 auto 14px; box-shadow: 0 4px 12px rgba(20, 85, 192, 0.25); border: 3px solid #ffffff;">
    @else
        <div style="width: 76px; height: 76px; border-radius: 50%; background: linear-gradient(135deg, var(--fic-primary) 0%, var(--fic-dark-blue) 100%); color: #ffffff; font-size: 26px; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; box-shadow: 0 4px 12px rgba(20, 85, 192, 0.25); border: 3px solid #ffffff;">
            {{ $initials }}
        </div>
    @endif

    <!-- User Information -->
    <h2 style="font-size: 18px; font-weight: 800; color: var(--fic-text-primary); margin-bottom: 2px;">
        {{ $user->name }}
    </h2>
    <div style="font-size: 13px; color: var(--fic-text-secondary); margin-bottom: 10px;">
        {{ '@' . $user->username }}
    </div>

    <div>
        <span class="badge badge-scheduled" style="font-size: 12px; padding: 6px 14px;">
            {{ $user->role === 'super_admin' ? 'Super Admin' : ($user->role === 'admin' ? 'Administrator' : 'Bruder Muda') }}
        </span>
    </div>

    <!-- Detail List -->
    <div style="margin-top: 24px; text-align: left; border-top: 1px solid var(--fic-border); padding-top: 16px;">
        <div style="display: flex; justify-content: space-between; gap: 12px; padding: 8px 0; font-size: 13px; border-bottom: 1px solid #F2F4F7;">
            <span style="color: var(--fic-text-muted); font-weight: 600;">Email Terdaftar:</span>
            <span style="font-weight: 600; color: var(--fic-text-primary); text-align: right;">{{ $user->email ?? 'Belum disetel' }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; gap: 12px; padding: 8px 0; font-size: 13px; border-bottom: 1px solid #F2F4F7;">
            <span style="color: var(--fic-text-muted); font-weight: 600;">Komunitas:</span>
            <span style="font-weight: 600; color: var(--fic-text-primary); text-align: right;">{{ $user->community_location ?: 'Belum disetel' }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 8px 0; font-size: 13px; border-bottom: 1px solid #F2F4F7;">
            <span style="color: var(--fic-text-muted); font-weight: 600;">Status Keanggotaan:</span>
            <span style="font-weight: 700; color: #027A48;">Aktif</span>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 8px 0; font-size: 13px;">
            <span style="color: var(--fic-text-muted); font-weight: 600;">Kongregasi:</span>
            <span style="font-weight: 600; color: var(--fic-text-primary);">Bruder FIC Indonesia</span>
        </div>
    </div>
</div>

<div class="card" style="display: flex; flex-direction: column; gap: 10px;">
    <a href="{{ route('profil.edit') }}" class="btn-secondary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
        </svg>
        <span>Edit Profil</span>
    </a>

    <a href="{{ route('profil.kata-sandi') }}" class="btn-secondary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
        </svg>
        <span>Ubah Kata Sandi</span>
    </a>
</div>

<!-- Form Logout POST -->
<div class="card" style="padding: 16px;">
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn-danger">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
            LOGOUT DARI APLIKASI
        </button>
    </form>
</div>
@endsection
