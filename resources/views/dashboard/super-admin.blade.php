<!-- TEMPORARY AUTH TEST VIEW - Dashboard Super Admin -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Super Admin - PWA BRUDER FIC (Test View)</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 40px 16px;
            display: flex;
            justify-content: center;
        }
        .container {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            padding: 24px;
            box-sizing: border-box;
        }
        .badge-notice {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            color: #1e40af;
            padding: 8px 12px;
            font-size: 11px;
            margin-bottom: 16px;
            border-radius: 4px;
        }
        h1 {
            font-size: 20px;
            margin-top: 0;
            color: #111827;
        }
        .info-table {
            width: 100%;
            margin-bottom: 24px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }
        .info-table td.label {
            font-weight: 600;
            color: #4b5563;
            width: 120px;
        }
        .btn-logout {
            background-color: #dc2626;
            color: #ffffff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-logout:hover {
            background-color: #b91c1c;
        }
        .nav-links {
            margin-top: 20px;
            font-size: 13px;
        }
        .nav-links a {
            color: #2563eb;
            text-decoration: none;
            margin-right: 12px;
        }
        .nav-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="badge-notice">
            <strong>TEMPORARY AUTH TEST VIEW</strong><br>
            Halaman minimal untuk pengujian role &amp; autentikasi.
        </div>

        <h1>Dashboard Super Admin</h1>

        <table class="info-table">
            <tr>
                <td class="label">Nama:</td>
                <td>{{ auth()->user()->name }}</td>
            </tr>
            <tr>
                <td class="label">Username:</td>
                <td>{{ auth()->user()->username }}</td>
            </tr>
            <tr>
                <td class="label">Role:</td>
                <td><strong>{{ auth()->user()->role }}</strong></td>
            </tr>
        </table>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-logout">Logout</button>
        </form>

        <div class="nav-links">
            <span>Uji akses halaman lain:</span><br>
            <a href="{{ route('admin.dashboard') }}">Ke /admin (Boleh)</a>
            <a href="{{ route('dashboard') }}">Ke /dashboard (Boleh)</a>
        </div>
    </div>
</body>
</html>
