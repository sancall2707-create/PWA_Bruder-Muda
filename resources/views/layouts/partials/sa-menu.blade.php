@php
    $isAdmin = in_array(auth()->user()->role, ['admin', 'admin_ketua_tppbm']);
    $homeUrl = $isAdmin ? route('admin.dashboard') : route('superadmin.dashboard');
@endphp
<style>
    .sa-menu-toggle {
        position: static;
        flex-shrink: 0;
        margin-left: auto;
        width: 40px;
        height: 40px;
        padding: 0;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.35);
        background: rgba(255, 255, 255, 0.16);
        color: #ffffff;
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 5px;
        cursor: pointer;
    }

    .sa-menu-toggle span {
        display: block;
        width: 16px;
        height: 2px;
        border-radius: 999px;
        background: currentColor;
    }

    .sa-menu-overlay,
    .sa-menu-panel {
        position: fixed;
        top: 0;
        bottom: 0;
        z-index: 140;
    }

    .sa-menu-overlay {
        left: 50%;
        width: 100%;
        max-width: 480px;
        transform: translateX(-50%);
        background: rgba(16, 24, 40, 0.45);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s ease;
    }

    .sa-menu-panel {
        right: max(0px, calc(50vw - 240px));
        width: min(320px, 86vw);
        overflow: hidden;
        pointer-events: none;
        background: transparent;
    }

    .sa-menu-sheet {
        height: 100%;
        background: rgba(255, 255, 255, 0.72);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-left: 1px solid rgba(255, 255, 255, 0.84);
        box-shadow: -8px 0 24px rgba(0, 62, 151, 0.12);
        transform: translateX(110%);
        transition: transform 0.2s ease;
        display: flex;
        flex-direction: column;
        padding: 18px 16px 20px;
        box-sizing: border-box;
    }

    .sa-menu.is-open .sa-menu-overlay {
        opacity: 1;
        pointer-events: auto;
    }

    .sa-menu.is-open .sa-menu-panel {
        pointer-events: auto;
    }

    .sa-menu.is-open .sa-menu-sheet {
        transform: translateX(0);
    }

    .sa-menu-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }

    .sa-menu-title {
        font-size: 16px;
        font-weight: 800;
        color: #101828;
    }

    .sa-menu-close {
        width: 36px;
        height: 36px;
        border: 1px solid rgba(255, 255, 255, 0.84);
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.55);
        color: #344054;
        font-size: 18px;
        line-height: 1;
        cursor: pointer;
    }

    .sa-menu-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
        flex: 1;
        overflow: auto;
    }

    .sa-menu-link {
        display: flex;
        align-items: center;
        min-height: 44px;
        padding: 10px 12px;
        border-radius: 12px;
        text-decoration: none;
        color: #101828;
        font-size: 14px;
        font-weight: 700;
        background: rgba(255, 255, 255, 0.5);
        border: 1px solid rgba(255, 255, 255, 0.8);
    }

    .sa-menu-link.active {
        background: rgba(239, 244, 255, 0.82);
        border-color: rgba(178, 221, 255, 0.9);
        color: #1455C0;
    }

    .sa-menu-logout {
        margin-top: auto;
    }

    .sa-menu-logout button {
        width: 100%;
        box-sizing: border-box;
        min-height: 36px;
        padding: 4px 12px;
        border-radius: 12px;
        border: 1px solid #b91c1c;
        background: #dc2626;
        color: #ffffff;
        font-size: 12px;
        font-weight: 800;
        line-height: 1.2;
        cursor: pointer;
    }
</style>
<div class="sa-menu" data-sa-menu>
    <div class="sa-menu-overlay" data-sa-menu-close></div>
    <nav class="sa-menu-panel" id="sa-menu-panel" aria-label="Menu {{ $isAdmin ? 'TPPBM' : 'Super Admin' }}">
        <div class="sa-menu-sheet">
            <div class="sa-menu-head">
                <div class="sa-menu-title">{{ $isAdmin ? 'Menu TPPBM' : 'Menu Super Admin' }}</div>
                <button type="button" class="sa-menu-close" data-sa-menu-close aria-label="Tutup menu">×</button>
            </div>
            <div class="sa-menu-list">
                <a class="sa-menu-link {{ request()->routeIs('admin.dashboard') || request()->routeIs('superadmin.dashboard') ? 'active' : '' }}" href="{{ $homeUrl }}">Beranda</a>
                <a class="sa-menu-link {{ request()->routeIs('superadmin.profil*') ? 'active' : '' }}" href="{{ route('superadmin.profil') }}">Profil &amp; Edit Akun Saya</a>
                @if ($isAdmin)
                    <a class="sa-menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Beranda Bruder Muda</a>
                @endif
                <a class="sa-menu-link {{ request()->routeIs('superadmin.users*') ? 'active' : '' }}" href="{{ route('superadmin.users.index') }}">{{ $isAdmin ? 'Direktori & Rekam Jejak Bruder' : 'Kelola Data Bruder' }}</a>
                <a class="sa-menu-link {{ request()->routeIs('superadmin.monitoring.raport') ? 'active' : '' }}" href="{{ route('superadmin.monitoring.raport') }}">Raport Pendampingan Bruder</a>
                <a class="sa-menu-link {{ request()->routeIs('superadmin.monitoring.index') ? 'active' : '' }}" href="{{ route('superadmin.monitoring.index') }}">{{ $isAdmin ? 'Monitoring & Ekspor Laporan' : 'Monitoring & Ekspor Data' }}</a>
                <a class="sa-menu-link {{ request()->routeIs('superadmin.monitoring.agenda') ? 'active' : '' }}" href="{{ route('superadmin.monitoring.agenda') }}">Rekapitulasi Agenda &amp; Kalender</a>
                <a class="sa-menu-link {{ request()->routeIs('superadmin.sapaan*') ? 'active' : '' }}" href="{{ route('superadmin.sapaan.index') }}">{{ $isAdmin ? 'Rilis Sapaan & Pengumuman Bersama' : 'Buat Sapaan Provinsial' }}</a>
            </div>
            <form class="sa-menu-logout" action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit">LOGOUT</button>
            </form>
        </div>
    </nav>
</div>
<script>
    (function () {
        var root = document.querySelector('[data-sa-menu]');
        var toggle = document.querySelector('[data-sa-menu-toggle]');
        if (!root || !toggle) return;
        function setOpen(open) {
            root.classList.toggle('is-open', open);
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            toggle.setAttribute('aria-label', open ? 'Tutup menu' : 'Buka menu');
        }
        toggle.addEventListener('click', function () {
            setOpen(!root.classList.contains('is-open'));
        });
        root.querySelectorAll('[data-sa-menu-close]').forEach(function (el) {
            el.addEventListener('click', function () { setOpen(false); });
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') setOpen(false);
        });
    })();
</script>
