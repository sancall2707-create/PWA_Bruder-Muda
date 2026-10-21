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
    <link rel="stylesheet" href="/css/fic-pwa.css">

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
            padding-bottom: 24px;
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

    </style>
</head>
<body>
    <div class="sa-container">
        <!-- Header -->
        <div class="sa-header">
            <div class="sa-header-top">
                <div class="sa-badge">
                    {{ auth()->user()->role === 'admin' ? 'TIM PENDAMPING TPPBM' : 'SUPER ADMIN' }}
                </div>
                <div style="display:flex; align-items:center; gap:8px; margin-left:auto;">
                    <a href="{{ route('superadmin.profil') }}" title="Profil &amp; Akun Saya" style="display:inline-flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:50%; background:rgba(255,255,255,0.2); border:1.5px solid rgba(255,255,255,0.4); color:#fff; text-decoration:none; font-size:13px; font-weight:800; overflow:hidden;">
                        @php
                            $saUser = auth()->user();
                            $saWords = explode(' ', trim($saUser->name ?? 'SA'));
                            $saInitials = strtoupper(substr($saWords[0], 0, 1) . (isset($saWords[1]) ? substr($saWords[1], 0, 1) : ''));
                        @endphp
                        @if ($saUser->profile_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($saUser->profile_photo))
                            <img src="{{ route('profil.foto') }}" alt="Foto profil" style="width:100%; height:100%; object-fit:cover;">
                        @else
                            {{ $saInitials }}
                        @endif
                    </a>
                    @include('layouts.partials.sa-menu-toggle')
                </div>
            </div>
            <h1 class="sa-title">{{ $headerTitle ?? (auth()->user()->role === 'admin' ? 'Beranda TPPBM' : 'Beranda Super Admin') }}</h1>
            <p class="sa-subtitle">{{ $headerSubtitle ?? (auth()->user()->role === 'admin' ? 'Pemantauan formasi rohani & pembinaan bruder muda' : 'Kelola pembinaan, bruder muda, & sapaan') }}</p>
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

        @include('layouts.partials.sa-menu')
    </div>
</body>
</html>