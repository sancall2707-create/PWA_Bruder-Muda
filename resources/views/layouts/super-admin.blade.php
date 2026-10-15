<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
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
            padding: 20px 16px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .sa-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 480px;
            background: #ffffff;
            border-top: 1px solid #E4E7EC;
            display: flex;
            justify-content: space-around;
            padding: 8px 0;
            z-index: 100;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.05);
        }

        .sa-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            text-decoration: none;
            color: #667085;
            font-size: 11px;
            font-weight: 600;
            width: 25%;
        }

        .sa-nav-item.active {
            color: #1455C0;
            font-weight: 800;
        }

        .sa-nav-item svg {
            width: 20px;
            height: 20px;
        }
    </style>
</head>
<body>
    <div class="sa-container">
        <!-- Header -->
        <div class="sa-header">
            <div class="sa-header-top">
                <div class="sa-badge">
                    SUPER ADMIN
                </div>
                <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: #fff; padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: 700; cursor: pointer;">
                        LOGOUT
                    </button>
                </form>
            </div>
            <h1 class="sa-title">{{ $headerTitle ?? 'Dashboard Super Admin' }}</h1>
            <p class="sa-subtitle">{{ $headerSubtitle ?? 'Kelola pembinaan, bruder muda, & sapaan' }}</p>
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

        <!-- Bottom Navigation Bar for Super Admin -->
        <nav class="sa-bottom-nav">
            <a href="{{ route('superadmin.dashboard') }}" class="sa-nav-item {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('superadmin.users.index') }}" class="sa-nav-item {{ request()->routeIs('superadmin.users*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                <span>Bruder</span>
            </a>
            <a href="{{ route('superadmin.monitoring.index') }}" class="sa-nav-item {{ request()->routeIs('superadmin.monitoring*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                <span>Monitoring</span>
            </a>
            <a href="{{ route('superadmin.sapaan.index') }}" class="sa-nav-item {{ request()->routeIs('superadmin.sapaan*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                <span>Sapaan</span>
            </a>
        </nav>
    </div>
</body>
</html>