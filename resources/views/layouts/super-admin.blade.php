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
            background-color: #EEF2F6;
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
            background-color: #F6F8FC;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.08);
            position: relative;
            padding-bottom: 72px;
        }

        .sa-header {
            background: linear-gradient(135deg, #003E97 0%, #1455C0 100%);
            color: #ffffff;
            padding: 20px 18px;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
            box-shadow: 0 4px 16px rgba(0, 62, 151, 0.15);
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

        .sa-logout {
            background: #dc2626;
            border: 1px solid #b91c1c;
            color: #ffffff;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
        }

        .sa-logout:hover {
            background: #b91c1c;
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
            padding: 20px 16px 92px;
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
                <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" class="sa-logout">
                        LOGOUT
                    </button>
                </form>
            </div>
            <h1 class="sa-title">{{ $headerTitle ?? (auth()->user()->role === 'admin' ? 'Dashboard TPPBM' : 'Dashboard Super Admin') }}</h1>
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

        @include('layouts.partials.sa-bottom-nav')
    </div>
</body>
</html>