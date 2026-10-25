<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    @include('partials.fic-icons')
    <title>Login - PWA BRUDER FIC INDONESIA</title>

    <!-- Google Fonts: Plus Jakarta Sans & Inter with safe system fallbacks -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- FIC PWA Core Design System -->
    <link rel="stylesheet" href="/css/fic-pwa.css?v=20261025d">

    <style>
        body {
            background: linear-gradient(180deg, #d5e4f8 0%, #f7f9fc 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px 16px;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            background: rgba(255, 255, 255, 0.68);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 20px;
            box-shadow: 0 12px 32px -4px rgba(0, 62, 151, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.84);
            padding: 32px 24px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
        }
        .brand-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 28px;
        }
        .brand-emblem {
            width: 96px;
            height: 96px;
            object-fit: contain;
            background: transparent;
            border: none;
            border-radius: 50%;
            display: block;
            margin-bottom: 14px;
            box-shadow: 0 8px 16px rgba(16, 24, 40, 0.12);
        }
        .brand-title-main {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 1.2px;
            color: var(--fic-dark-blue);
            line-height: 1.2;
        }
        .brand-title-sub {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 2px;
            color: var(--fic-primary);
            margin-top: 2px;
        }
        .brand-tagline {
            font-size: 13px;
            font-style: italic;
            color: var(--fic-text-secondary);
            margin-top: 8px;
        }
        .brand-desc {
            font-size: 11px;
            font-weight: 600;
            color: var(--fic-text-muted);
            letter-spacing: 0.4px;
            margin-top: 4px;
            text-transform: uppercase;
        }
        .alert-error {
            background-color: rgba(254, 243, 242, 0.84);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(254, 205, 202, 0.9);
            border-radius: 12px;
            padding: 12px 14px;
            color: #B42318;
            font-size: 13px;
            margin-bottom: 20px;
            line-height: 1.4;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        .password-container {
            position: relative;
            display: flex;
            align-items: center;
        }
        .password-toggle-btn {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            color: var(--fic-text-secondary);
            cursor: pointer;
            padding: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .password-toggle-btn:hover {
            color: var(--fic-text-primary);
        }
        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 4px;
            margin-bottom: 24px;
            font-size: 13px;
            color: var(--fic-text-secondary);
            user-select: none;
        }
        .remember-row input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--fic-primary);
            cursor: pointer;
        }
        .footer-note {
            text-align: center;
            font-size: 11px;
            color: #475467;
            margin-top: 24px;
            line-height: 1.5;
            font-weight: 500;
        }
        @media (max-width: 360px) {
            body {
                padding: 12px 10px;
            }
            .login-card {
                padding: 24px 16px;
                border-radius: 16px;
            }
            .brand-emblem {
                width: 80px;
                height: 80px;
                margin-bottom: 10px;
            }
            .brand-header {
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <!-- Brand Identity -->
        <div class="brand-header">
            <img src="/images/logo-fic.png?v=2" alt="Lambang Kongregasi Bruder FIC" class="brand-emblem" width="96" height="96">
            <div class="brand-title-main">BRUDER FIC</div>
            <div class="brand-title-sub">INDONESIA</div>
            <p class="brand-tagline">“Hidup kita bagi Allah dan Sesama”</p>
            <p class="brand-desc">Aplikasi Pendampingan Bruder Muda FIC Indonesia</p>
        </div>

        @if ($errors->any())
            <div class="alert-error" role="alert">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-top:2px;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <div>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <form action="{{ route('login.store') }}" method="POST">
            @csrf

            <!-- Username Input -->
            <div class="form-group">
                <label for="username" class="form-label">Username</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    value="{{ old('username') }}"
                    class="form-input"
                    placeholder="Masukkan username Anda"
                    required
                    autofocus
                    autocomplete="username"
                >
            </div>

            <!-- Password Input -->
            <div class="form-group">
                <label for="password" class="form-label">Kata Sandi</label>
                <div class="password-container">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input"
                        placeholder="Masukkan kata sandi Anda"
                        required
                        autocomplete="current-password"
                        style="padding-right: 44px;"
                    >
                    <button
                        type="button"
                        id="togglePassword"
                        class="password-toggle-btn"
                        aria-label="Tampilkan atau sembunyikan kata sandi"
                    >
                        <svg id="eyeIcon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="remember-row">
                <input type="checkbox" id="remember" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember" style="cursor: pointer;">Ingat Saya di perangkat ini</label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-primary">
                LOGIN
            </button>
        </form>

        <div class="footer-note">
            Akun dikelola oleh Administrator Provinsi.<br>
            Kongregasi Bruder Santa Perawan Maria Yang Terkandung Tak Bernoda (FIC)
        </div>
    </div>

    <script>
        const togglePasswordBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (togglePasswordBtn && passwordInput) {
            togglePasswordBtn.addEventListener('click', function () {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';

                if (isPassword) {
                    eyeIcon.innerHTML = `
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                    `;
                } else {
                    eyeIcon.innerHTML = `
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    `;
                }
            });
        }
    </script>
</body>
</html>
