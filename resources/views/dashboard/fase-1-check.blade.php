<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pengecekan Fase 1 - PWA BRUDER FIC INDONESIA</title>

    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- FIC PWA Core Design System -->
    <link rel="stylesheet" href="/css/fic-pwa.css">

    <style>
        body {
            background-color: #EEF2F6;
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: #1B2430;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
        }

        .check-container {
            width: 100%;
            max-width: 480px;
            background-color: #F6F8FC;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.08);
            position: relative;
        }

        .check-header {
            background: linear-gradient(135deg, #003E97 0%, #1455C0 100%);
            color: #ffffff;
            padding: 24px 20px;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
            box-shadow: 0 4px 16px rgba(0, 62, 151, 0.15);
        }

        .check-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .check-title {
            font-size: 20px;
            font-weight: 800;
            line-height: 1.25;
            margin: 0 0 6px 0;
        }

        .check-subtitle {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.4;
            margin: 0;
        }

        .check-body {
            padding: 20px 16px 40px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .user-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #E4E7EC;
            box-shadow: 0 2px 8px rgba(16, 24, 40, 0.04);
            padding: 16px 18px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .user-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #E4E7EC;
        }

        .user-name {
            font-size: 15px;
            font-weight: 700;
            color: #101828;
            line-height: 1.3;
        }

        .user-username {
            font-size: 12px;
            color: #667085;
            font-weight: 500;
        }

        .role-pill {
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            white-space: nowrap;
            letter-spacing: 0.3px;
        }

        .role-super_admin { background: #FEF3F2; color: #B42318; border: 1px solid #FECDCA; }
        .role-admin { background: #F0F9FF; color: #026AA2; border: 1px solid #B9E6FE; }
        .role-user { background: #ECFDF3; color: #027A48; border: 1px solid #ABEFC6; }

        .info-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            font-size: 13px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .info-label {
            font-size: 11px;
            font-weight: 700;
            color: #98A2B3;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 13.5px;
            font-weight: 600;
            color: #1D2939;
            word-break: break-word;
        }

        .info-value.highlight {
            color: #1455C0;
            font-weight: 700;
        }

        .action-area {
            margin-top: auto;
            padding-top: 16px;
        }

        .btn-back-main {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 14px 20px;
            background: linear-gradient(135deg, #1455C0 0%, #003E97 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(20, 85, 192, 0.25);
            transition: transform 0.15s ease;
        }

        .btn-back-main:active {
            transform: scale(0.98);
        }
    </style>
</head>
<body>
    <div class="check-container">
        <!-- Header -->
        <div class="check-header">
            <div class="check-badge">
                <span>✓</span> FASE 1 VERIFIKASI
            </div>
            <h1 class="check-title">Pengecekan Model &amp; Database</h1>
            <p class="check-subtitle">Verifikasi kolom Komunitas, No. Telepon, dan Bio pada data Bruder Muda &amp; Administrator.</p>
        </div>

        <!-- Body -->
        <div class="check-body">
            @foreach($users as $user)
                <div class="user-card">
                    <div class="user-card-top">
                        <div>
                            <div class="user-name">{{ $user->name }}</div>
                            <div class="user-username">{{ '@' . $user->username }}</div>
                        </div>
                        <span class="role-pill role-{{ $user->role }}">
                            {{ str_replace('_', ' ', $user->role) }}
                        </span>
                    </div>

                    <div class="info-list">
                        <div class="info-item">
                            <span class="info-label">Email</span>
                            <span class="info-value">{{ $user->email ?? '-' }}</span>
                        </div>

                        <div class="info-item">
                            <span class="info-label">Komunitas / Penugasan</span>
                            <span class="info-value highlight">{{ $user->community_location ?? 'Belum diisi' }}</span>
                        </div>

                        <div class="info-item">
                            <span class="info-label">No. Telepon</span>
                            <span class="info-value">{{ $user->phone ?? 'Belum diisi' }}</span>
                        </div>

                        <div class="info-item">
                            <span class="info-label">Bio / Catatan Peran</span>
                            <span class="info-value">{{ $user->bio ?? 'Belum diisi' }}</span>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Action Area -->
            <div class="action-area">
                <a href="{{ route('superadmin.dashboard') }}" class="btn-back-main">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    <span>Kembali ke Dashboard Super Admin</span>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
