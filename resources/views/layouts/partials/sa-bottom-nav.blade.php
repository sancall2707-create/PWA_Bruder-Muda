@php
    $isAdminRole = in_array(auth()->user()->role, ['admin', 'admin_ketua_tppbm']);
    $saHomeUrl = $isAdminRole ? route('admin.dashboard') : route('superadmin.dashboard');
@endphp

<style>
    .sa-bottom-nav {
        position: fixed;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 100%;
        max-width: 480px;
        min-height: 64px;
        padding-bottom: env(safe-area-inset-bottom, 0px);
        background: rgba(255, 255, 255, 0.88);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-top: 1px solid rgba(228, 231, 236, 0.9);
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        z-index: 100;
        box-shadow: 0 -4px 18px rgba(0, 62, 151, 0.06);
        box-sizing: border-box;
    }

    @if(!empty($wideLayout))
        @media (min-width: 860px) {
            .sa-bottom-nav {
                max-width: 880px;
            }
        }
    @endif

    .sa-nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 3px;
        text-decoration: none;
        color: #667085;
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 0.2px;
        padding: 6px 2px;
        transition: color 0.15s ease;
        min-height: 48px;
    }

    .sa-nav-item svg {
        width: 20px;
        height: 20px;
        transition: transform 0.15s ease;
        flex-shrink: 0;
    }

    .sa-nav-item.active {
        color: #1455C0;
        font-weight: 800;
    }

    .sa-nav-item.active svg {
        transform: translateY(-1px);
        stroke-width: 2.4;
    }

    @media print {
        .sa-bottom-nav {
            display: none !important;
        }
    }
</style>

<nav class="sa-bottom-nav" aria-label="Navigasi Menu Utama">
    <!-- Beranda -->
    <a href="{{ $saHomeUrl }}" class="sa-nav-item {{ request()->routeIs('superadmin.dashboard') || request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
        </svg>
        <span>Beranda</span>
    </a>

    <!-- Data Bruder -->
    <a href="{{ route('superadmin.users.index') }}" class="sa-nav-item {{ request()->routeIs('superadmin.users*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
        </svg>
        <span>Bruder</span>
    </a>

    <!-- Raport Pendampingan -->
    <a href="{{ route('superadmin.monitoring.raport') }}" class="sa-nav-item {{ request()->routeIs('superadmin.monitoring.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="16" y1="13" x2="8" y2="13"></line>
            <line x1="16" y1="17" x2="8" y2="17"></line>
        </svg>
        <span>Raport</span>
    </a>

    <!-- Sapaan & Pengumuman -->
    <a href="{{ route('superadmin.sapaan.index') }}" class="sa-nav-item {{ request()->routeIs('superadmin.sapaan*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
        <span>Sapaan</span>
    </a>

    <!-- Profil -->
    <a href="{{ route('superadmin.profil') }}" class="sa-nav-item {{ request()->routeIs('superadmin.profil*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
        </svg>
        <span>Profil</span>
    </a>
</nav>
