<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>RENTO - تسجيل الدخول</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #d4e4f0 0%, #c8d8e8 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            padding: 50px 45px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .logo-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 10px;
        }
        /* Persisted logo/text sizes per request */
        .sidebar-logo-l {
            max-width: 30px;
            height: auto;
            margin-right: 5px;
        }

        .sidebar-text-l {
            max-width: 120px;
            height: auto;
        }

        .logo-icon {
            width: 45px;
            height: 45px;
            background: #1e7fc1;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .logo-icon::before {
            content: 'R';
            color: #ffd700;
            font-size: 28px;
            font-weight: bold;
            font-style: italic;
        }

        .logo-text {
            font-size: 42px;
            font-weight: bold;
            color: #1e7fc1;
            letter-spacing: 2px;
        }

        .page-title {
            text-align: center;
            font-size: 22px;
            color: #333;
            margin-bottom: 35px;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-label {
            display: block;
            text-align: right;
            font-size: 14px;
            color: #999;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 15px 18px;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            text-align: right;
            direction: rtl;
            transition: all 0.3s ease;
            background: white;
        }

        .form-input:focus {
            outline: none;
            border-color: #1e7fc1;
            box-shadow: 0 0 0 3px rgba(30, 127, 193, 0.1);
        }

        .form-input::placeholder {
            color: #999;
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #999;
            padding: 5px;
            display: flex;
            align-items: center;
        }

        .toggle-password:hover {
            color: #666;
        }

        .toggle-password svg {
            width: 20px;
            height: 20px;
        }

        .forgot-password {
            text-align: left;
            margin-top: 10px;
        }

        .forgot-password a {
            color: #1e7fc1;
            text-decoration: none;
            font-size: 13px;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: #155a8a;
            text-decoration: underline;
        }

        .login-button {
            width: 100%;
            padding: 16px;
            background: #b8b8b8; /* الحالة الافتراضية تبقى كما هي */
            color: white;
            border: none;
            font-size: 18px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.25s ease;
            margin-top: 10px;
            border-radius: 8px; /* إعادة الحواف الدائرية */
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12); /* إعادة الظل الافتراضي */
        }

        /* Hover: لبني (أزرق فاتح) متوافق مع ألوان النظام */
        .login-button:hover:not(:disabled) {
            background: #1e7fc1; /* أزرق النظام المستخدم بالروابط */
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(30, 127, 193, 0.25);
        }

        /* Active: أزرق غامق متوافق مع ألوان النظام */
        .login-button:active:not(:disabled) {
            background: #155a8a;
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(21, 90, 138, 0.25);
        }

        .login-button:disabled {
            cursor: not-allowed;
            opacity: 0.6;
        }

        .error-message {
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: right;
            font-size: 14px;
        }
        .sidebar-text-l{
            max-width: 150px;
            height: auto;
        }
        .sidebar-logo-l{
            max-width: 35px;
            height: auto;
            margin-right: 3px;
        }
        .success-message {
            background: #efe;
            border: 1px solid #cfc;
            color: #3c3;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: right;
            font-size: 14px;
        }

        /* Validation errors */
        .invalid-feedback {
            color: #c33;
            font-size: 13px;
            margin-top: 5px;
            text-align: right;
            display: block;
        }

        .form-input.is-invalid {
            border-color: #c33;
        }

        .form-input.is-invalid:focus {
            border-color: #c33;
            box-shadow: 0 0 0 3px rgba(204, 51, 51, 0.1);
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 35px 25px;
            }

            .logo-text {
                font-size: 36px;
            }

            .page-title {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
<div class="login-container">
    <div class="logo-container">
        <div class="logo">
            <img src="/images/rento-text.svg" alt="Rento Text" class="sidebar-text-l">
            <img src="/images/rento-logo.svg" alt="Rento Logo" class="sidebar-logo-l">
        </div>
    </div>

    <h1 class="page-title">تسجيل الدخول</h1>

    @if (session('error'))
        <div class="error-message">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="error-message">
            <ul style="list-style: none; padding: 0; margin: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ request()->routeIs('login') ? route('login') : route('admin.login.submit') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="email">البريد الإلكتروني</label>
            <input
                type="email"
                id="email"
                name="email"
                class="form-input @error('email') is-invalid @enderror"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="email"
            >
            @error('email')
            <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password">كلمة المرور</label>
            <div class="password-container">
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-input @error('password') is-invalid @enderror"
                    required
                    autocomplete="current-password"
                >
                <button type="button" class="toggle-password" onclick="togglePassword()">
                    <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
            @error('password')
            <span class="invalid-feedback">{{ $message }}</span>
            @enderror

            @if (Route::has('password.request'))
                <div class="forgot-password">
                    <a href="{{ route('password.request') }}">نسيت كلمة المرور؟</a>
                </div>
            @endif
        </div>

        <button type="submit" class="login-button">دخول</button>
    </form>
</div>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                `;
        } else {
            passwordInput.type = 'password';
            eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
        }
    }

    // Auto-hide messages after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const messages = document.querySelectorAll('.error-message, .success-message');
        messages.forEach(function(message) {
            setTimeout(function() {
                message.style.transition = 'opacity 0.5s ease';
                message.style.opacity = '0';
                setTimeout(function() {
                    message.remove();
                }, 500);
            }, 5000);
        });
    });
</script>
</body>
</html>
