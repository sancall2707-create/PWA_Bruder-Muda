<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pengecekan Fase 2 (Backend Logic &amp; Services) - PWA BRUDER FIC</title>

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

        .stat-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .stat-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid #E4E7EC;
            padding: 14px 16px;
            box-shadow: 0 2px 6px rgba(16, 24, 40, 0.03);
        }

        .stat-card.full-width {
            grid-column: span 2;
            background: linear-gradient(135deg, #F0F9FF 0%, #E0F2FE 100%);
            border-color: #B9E6FE;
        }

        .stat-val {
            font-size: 24px;
            font-weight: 800;
            color: #1455C0;
            line-height: 1.1;
        }

        .stat-label {
            font-size: 12px;
            font-weight: 600;
            color: #667085;
            margin-top: 4px;
        }

        .section-title {
            font-size: 15px;
            font-weight: 800;
            color: #101828;
            margin-top: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .user-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid #E4E7EC;
            padding: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 6px rgba(16, 24, 40, 0.03);
        }

        .user-info-name {
            font-size: 14px;
            font-weight: 700;
            color: #101828;
        }

        .user-info-loc {
            font-size: 12px;
            color: #667085;
            margin-top: 2px;
        }

        .badge-status {
            padding: 3px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
            background: #ECFDF3;
            color: #027A48;
            border: 1px solid #ABEFC6;
        }

        .btn-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 700;
            color: #1455C0;
            text-decoration: none;
        }

        .btn-link:hover { text-decoration: underline; }

        .service-box {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid #EAECF0;
            padding: 14px;
            font-size: 13px;
        }

        .service-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #F2F4F7;
        }
        .service-item:last-child { border-bottom: none; }

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
        }
    </style>
</head>
<body>
    <div class="check-container">
        <!-- Header -->
        <div class="check-header">
            <div class="check-badge">
                <span>✓</span> FASE 2 VERIFIKASI
            </div>
            <h1 class="check-title">Backend Logic &amp; Services</h1>
            <p class="check-subtitle">Verifikasi Layanan Kalkulasi Statistik Pembinaan, Service User Management, &amp; Export Engine.</p>
        </div>

        <!-- Body -->
        <div class="check-body">
            <!-- Stat Cards -->
            <div class="stat-grid">
                <div class="stat-card full-width">
                    <div class="stat-val">{{ $stats['completion_percent'] }}%</div>
                    <div class="stat-label">Persentase Kepatuhan Pembinaan Bulanan ({{ $stats['period_label'] }})</div>
                </div>

                <div class="stat-card">
                    <div class="stat-val">{{ $stats['active_bruders'] }}</div>
                    <div class="stat-label">Bruder Muda Aktif</div>
                </div>

                <div class="stat-card">
                    <div class="stat-val">{{ $stats['admins'] }}</div>
                    <div class="stat-label">Admin / TPPBM</div>
                </div>

                <div class="stat-card">
                    <div class="stat-val">{{ $stats['bimbingan_submitted'] }}</div>
                    <div class="stat-label">Laporan Bimbingan</div>
                </div>

                <div class="stat-card">
                    <div class="stat-val">{{ $stats['wawanhati_submitted'] }}</div>
                    <div class="stat-label">Laporan Wawanhati</div>
                </div>
            </div>

            <!-- Tested Services List -->
            <div class="section-title">
                <span>Services &amp; Controllers Aktif</span>
            </div>
            <div class="service-box">
                <div class="service-item">
                    <span>SuperAdminOverviewService</span>
                    <span style="color:#027A48; font-weight:700;">OK ✅</span>
                </div>
                <div class="service-item">
                    <span>UserManagementController</span>
                    <a href="{{ route('superadmin.users.index') }}" class="btn-link">Test Route →</a>
                </div>
                <div class="service-item">
                    <span>MonitoringController</span>
                    <a href="{{ route('superadmin.monitoring.index') }}" class="btn-link">Test Route →</a>
                </div>
                <div class="service-item">
                    <span>Export CSV Engine</span>
                    <a href="{{ route('superadmin.monitoring.export') }}" class="btn-link">Test Download →</a>
                </div>
                <div class="service-item">
                    <span>ProvincialMessageManagementController</span>
                    <a href="{{ route('superadmin.sapaan.index') }}" class="btn-link">Test Route →</a>
                </div>
            </div>

            <!-- Bruder List Preview -->
            <div class="section-title">
                <span>Daftar Bruder Muda</span>
                <a href="{{ route('superadmin.users.index') }}" class="btn-link">Lihat Semua</a>
            </div>

            @foreach($bruders as $b)
                <div class="user-card">
                    <div>
                        <div class="user-info-name">{{ $b->name }}</div>
                        <div class="user-info-loc">{{ $b->community_location ?? 'Komunitas belum diset' }}</div>
                    </div>
                    <a href="{{ route('superadmin.users.track-record', $b->id) }}" class="btn-link">Track Record →</a>
                </div>
            @endforeach

            <!-- Action Area -->
            <div style="margin-top: 12px;">
                <a href="{{ route('superadmin.fase1-check') }}" class="btn-back-main" style="background: #ffffff; color: #1455C0; border: 1px solid #1455C0; margin-bottom: 10px;">
                    ← Lihat Pengecekan Fase 1
                </a>
            </div>
        </div>
    </div>
</body>
</html>
