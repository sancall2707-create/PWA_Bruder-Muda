<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    @include('partials.fic-icons')
    <title>{{ $title ?? 'Super Admin' }} - PWA BRUDER FIC</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- FIC PWA Core Design System -->
    <link rel="stylesheet" href="/css/fic-pwa.css?v=20261024b">

    <style>
        body {
            background-color: #e7f0fb;
            background-image: linear-gradient(180deg, #d5e4f8 0%, #eef3fb 46%, #f7f9fc 100%);
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
        }

        .sa-container {
            width: 100%;
            max-width: 480px;
            background: linear-gradient(180deg, rgba(20, 85, 192, 0.1) 0%, rgba(234, 243, 255, 0.55) 180px, rgba(247, 249, 252, 0.88) 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 0 30px rgba(0, 62, 151, 0.08);
            position: relative;
            padding-bottom: 76px;
        }

        .sa-header {
            background: linear-gradient(135deg, rgba(0, 62, 151, 0.9) 0%, rgba(20, 85, 192, 0.78) 100%);
            color: #ffffff;
            padding: 20px 18px;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.28);
            box-shadow: 0 8px 20px rgba(0, 62, 151, 0.12);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        .sa-header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .sa-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .sa-title {
            font-size: 20px;
            font-weight: 800;
            margin: 0 0 4px 0;
        }

        .sa-subtitle {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.85);
            margin: 0;
        }

        .sa-body {
            padding: 20px 16px 28px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        @if(!empty($wideLayout))
            @media (min-width: 860px) {
                .sa-container {
                    max-width: 880px;
                }
            }
        @endif

        @media print {
            html, body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            .sa-container {
                max-width: 100% !important;
                width: 100% !important;
                min-height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                background: transparent !important;
            }
            .sa-header, .sa-bottom-nav, .sa-menu-toggle, .sa-menu, .no-print, header, nav {
                display: none !important;
            }
            .sa-body {
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                gap: 0 !important;
            }
        }
    </style>
</head>
<body>
    <div class="sa-container">
        <!-- Header -->
        <div class="sa-header">
            <div class="sa-header-top">
                <div class="sa-badge">
                    {{ auth()->user()->role === 'admin_ketua_tppbm' ? 'KETUA TPPBM' : (auth()->user()->role === 'admin' ? 'TIM PENDAMPING TPPBM' : 'SUPER ADMIN') }}
                </div>
                <div style="display:flex; align-items:center; gap:8px; margin-left:auto;">
                    <a href="{{ route('superadmin.profil') }}" title="Profil &amp; Akun Saya" style="display:inline-flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:50%; background:rgba(255,255,255,0.2); border:1.5px solid rgba(255,255,255,0.4); color:#fff; text-decoration:none; font-size:13px; font-weight:800; overflow:hidden;">
                        @php
                            $saUser = auth()->user();
                            $saWords = explode(' ', trim($saUser->name ?? 'SA'));
                            $saInitials = strtoupper(substr($saWords[0], 0, 1) . (isset($saWords[1]) ? substr($saWords[1], 0, 1) : ''));
                            $saPhotoVersion = $saUser->profile_photo ? substr(md5($saUser->profile_photo), 0, 8) : '0';
                        @endphp
                        @if ($saUser->profile_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($saUser->profile_photo))
                            <img src="{{ route('profil.foto', ['v' => $saPhotoVersion]) }}" alt="Foto profil" style="width:100%; height:100%; object-fit:cover;">
                        @else
                            {{ $saInitials }}
                        @endif
                    </a>
                    @include('layouts.partials.sa-menu-toggle')
                </div>
            </div>
            <h1 class="sa-title">{{ $headerTitle ?? (in_array(auth()->user()->role, ['admin', 'admin_ketua_tppbm']) ? 'Beranda TPPBM' : 'Beranda Super Admin') }}</h1>
            <p class="sa-subtitle">{{ $headerSubtitle ?? (in_array(auth()->user()->role, ['admin', 'admin_ketua_tppbm']) ? 'Pemantauan formasi rohani & pembinaan bruder muda' : 'Kelola pembinaan, bruder muda, & sapaan') }}</p>
        </div>

        <!-- Body Content -->
        <div class="sa-body">
            @if(session('success'))
                <div style="background:#ECFDF3; border: 1px solid #ABEFC6; color:#027A48; padding:12px 14px; border-radius:12px; font-size:13px; font-weight:700;">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </div>

        @if(empty($hideBottomNav))
            @include('layouts.partials.sa-bottom-nav')
        @endif

        @include('layouts.partials.sa-menu')
    </div>

    <script>
        // Loading state otomatis pada formulir saat disubmit
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
</body>
</html>