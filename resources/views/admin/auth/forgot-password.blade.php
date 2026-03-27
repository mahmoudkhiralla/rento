<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RENTO - طلب إعادة تعيين كلمة الدخول</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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
        .logo-name {
            color: #1e5aa8;
            font-size: 32px;
            font-weight: bold;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 20px;
            font-weight: 500;
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

        .info-text {
            text-align: center;
            color: #4a90e2;
            font-size: 13px;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .btn-submit {
            width: 100%;
            padding: 15px;
            background: #1e5aa8;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-submit:hover {
            background: #174a8b;
        }
    </style>
</head>
<body>
<div class="login-container">
    <div class="logo">
        <img src="/images/rento-text.svg" alt="Rento Text" class="sidebar-text-l">
        <img src="/images/rento-logo.svg" alt="Rento Logo" class="sidebar-logo-l">
    </div>

    <h2>طلب إعادة تعيين كلمة الدخول</h2>

    <form action="#" method="POST">
        @csrf

        <div class="form-group">
            <label for="email">البريد الإلكتروني</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
        </div>

        <p class="info-text">سيتم إرسال كود إعادة تعيين كلمة السر علي البريد الإلكتروني الخاص بك</p>

        <button type="submit" class="btn-submit">دخول</button>
    </form>
</div>
</body>
</html>
