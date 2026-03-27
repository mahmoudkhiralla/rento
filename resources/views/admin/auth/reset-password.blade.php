<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RENTO - إعادة تعيين كلمة الدخول</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        /* Persisted logo/text sizes per request */
        .sidebar-text-l{
            max-width: 120px;
            height: auto;
        }
        .sidebar-logo-l{
            max-width: 30px;
            height: auto;
            margin-right: 5px;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #e8ecf1;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .logo-text {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            margin-bottom: 20px;
        }

        .logo-icon {
            background: #1e5aa8;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 24px;
        }

        .logo-name {
            color: #1e5aa8;
            font-size: 32px;
            font-weight: bold;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
            font-size: 20px;
            font-weight: 500;
        }

        .error-header {
            text-align: center;
            color: #e53e3e;
            margin-bottom: 30px;
            font-size: 13px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            text-align: right;
            color: #999;
            margin-bottom: 8px;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            text-align: right;
            transition: border-color 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #1e5aa8;
        }

        input.error {
            border-color: #e53e3e;
        }

        .password-group {
            position: relative;
        }

        .password-group input {
            padding-left: 45px;
        }

        .password-toggle {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #e53e3e;
            font-size: 18px;
        }

        .error-message {
            color: #e53e3e;
            font-size: 12px;
            margin-top: 8px;
            text-align: right;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 5px;
        }

        .error-icon {
            font-size: 14px;
        }

        .btn-login {
            width: 100%;
            padding: 15px;
            background: #c4c4c4;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
            transition: background 0.3s;
        }

        .btn-login:hover {
            background: #b0b0b0;
        }
    </style>
</head>
<body>
<div class="login-container">
    <div class="logo-text">
        <div class="logo-icon">R</div>
        <div class="logo-name">RENTO</div>
    </div>

    <h2>تسجيل الدخول</h2>
    <p class="error-header">كلمة الدخول غير صحيحة، يرجاء إعادة المحاولة (1 من 2)</p>

    <form action="#" method="POST">
        @csrf

        <div class="form-group">
            <label for="email">البريد الإلكتروني</label>
            <input type="email" id="email" name="email" value="Mohsen_Saleh@gmail.com" required>
        </div>

        <div class="form-group">
            <label for="password">كلمة الدخول</label>
            <div class="password-group">
                <input type="password" id="password" name="password" value="*********" class="error" required>
                <span class="password-toggle">👁</span>
            </div>
            <div class="error-message">
                <span class="error-icon">⚠</span>
                <span>رسالة خطأ</span>
            </div>
        </div>

        <button type="submit" class="btn-login">دخول</button>
    </form>
</div>
</body>
</html>
