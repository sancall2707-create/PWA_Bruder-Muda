<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    @include('partials.fic-icons')
    <title>{{ $title ?? 'Bruder FIC Indonesia' }} - PWA Pendampingan</title>

    <!-- Google Fonts: Plus Jakarta Sans & Inter with safe fallbacks -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- FIC PWA Core Design System -->
    <link rel="stylesheet" href="/css/fic-pwa.css">

    @stack('styles')
</head>
<body>
    <div class="app-container">
        @hasSection('header')
            @yield('header')
        @else
            <!-- Default User App Header -->
            <header class="app-header">
                <div class="header-top">
                    <div class="identity-brand">
                        <img src="/images/logo-fic.png?v=2" alt="Lambang FIC" class="identity-logo" width="38" height="38">
                        <div class="identity-titles">
                            <span class="identity-title-main">BRUDER FIC</span>
                            <span class="identity-title-sub">INDONESIA</span>
                        </div>
                    </div>
                    <a href="{{ route('profil') }}" class="header-user-avatar" title="Profil Pengguna">
                        @php
                            $user = auth()->user();
                            $initials = 'BM';
                            if ($user && !empty($user->name)) {
                                $words = explode(' ', trim($user->name));
                                $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                            }
                            $photoPath = $user->profile_photo ?? null;
                            $userPhotoVersion = $photoPath ? substr(md5($photoPath), 0, 8) : '0';
                            $hasProfilePhoto = is_string($photoPath)
                                && str_starts_with($photoPath, 'profil/')
                                && $photoPath !== 'profil/'
                                && ! str_contains($photoPath, '..')
                                && ! str_contains($photoPath, '\\')
                                && ! str_ends_with($photoPath, '/');
                        @endphp
                        @if ($hasProfilePhoto)
                            <img src="{{ route('profil.foto', ['v' => $userPhotoVersion]) }}" alt="Foto profil" class="header-user-photo" width="42" height="42">
                        @else
                            {{ $initials }}
                        @endif
                    </a>
                </div>
                <div class="header-greeting">
                    <span class="header-greeting-label">{{ $headerGreetingLabel ?? 'Beranda Bruder Muda' }}</span>
                    <h1 class="header-greeting-name">{{ auth()->user()->name ?? 'Bruder' }}</h1>
                </div>
            </header>
        @endif

        @if(in_array(auth()->user()?->role, ['admin', 'admin_ketua_tppbm']))
            <a href="{{ route('admin.dashboard') }}" class="tppbm-return">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
                Kembali ke Beranda TPPBM
            </a>
        @elseif(auth()->user()?->role === 'super_admin')
            <a href="{{ route('superadmin.dashboard') }}" class="tppbm-return">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
                Kembali ke Beranda Super Admin
            </a>
        @endif

        @if(isset($backUrl) || View::hasSection('subheader'))
            @hasSection('subheader')
                @yield('subheader')
            @else
                <div class="page-subheader">
                    <a href="{{ $backUrl ?? route('dashboard') }}" class="btn-back" aria-label="Kembali">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                    </a>
                    <span class="page-title">{{ $pageTitle ?? ($title ?? 'Kembali') }}</span>
                </div>
            @endif
        @endif

        @php
            $isNoNav = request()->routeIs('sapaan-provinsial') || (isset($hideBottomNav) && $hideBottomNav);
        @endphp

        <!-- Main Content -->
        <main class="app-content {{ $isNoNav ? 'no-nav' : '' }}">
            @yield('content')
        </main>

        @if(!$isNoNav)
        <!-- Bottom Navigation -->
        <nav class="bottom-nav" aria-label="Navigasi Utama">
            <!-- Beranda -->
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <span>Beranda</span>
            </a>

            <!-- Kalender -->
            <a href="{{ route('kalender') }}" class="nav-item {{ request()->routeIs('kalender') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <span>Kalender</span>
            </a>

            <!-- Jurnal -->
            <a href="{{ route('jurnal.index') }}" class="nav-item {{ request()->routeIs('jurnal.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
                <span>Jurnal</span>
            </a>

            <!-- Profil -->
            <a href="{{ route('profil') }}" class="nav-item {{ request()->routeIs('profil') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span>Profil</span>
            </a>
        </nav>
        @endif
    </div>

    <script>
        // Loading state otomatis pada seluruh formulir saat disubmit
        document.addEventListener('submit', function(e) {
            var submitBtn = e.target.querySelector('button[type="submit"]:not([data-no-loading])');
            if (submitBtn && !submitBtn.disabled) {
                setTimeout(function() {
                    submitBtn.disabled = true;
                    submitBtn.dataset.originalHtml = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<span style="display:inline-flex; align-items:center; justify-content:center; gap:6px;"><svg style="animation:ficSpin 0.9s linear infinite; width:16px; height:16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle><path d="M12 2a10 10 0 0 1 10 10"></path></svg> Menyimpan...</span>';
                    submitBtn.style.opacity = '0.82';
                    submitBtn.style.cursor = 'not-allowed';
                }, 30);
            }
        });
    </script>
    <style>
        @keyframes ficSpin { 100% { transform: rotate(360deg); } }
    </style>

    @stack('scripts')
</body>
</html>
