<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
</head>
<body style="font-family: Tahoma, Arial, sans-serif; background:#f6f7f9; padding:24px;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:600px; margin:auto; background:#ffffff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
        <tr>
            <td style="padding:20px 24px; border-bottom:1px solid #eee;">
                <h2 style="margin:0; font-size:18px; color:#111827;">{{ $title }}</h2>
            </td>
        </tr>
        <tr>
            <td style="padding:20px 24px; color:#374151; line-height:1.8;">
                <div>{{ $message }}</div>
            </td>
        </tr>
        <tr>
            <td style="padding:16px 24px; border-top:1px solid #eee; color:#6B7280; font-size:12px;">
                تم الإرسال بواسطة نظام رنتو
            </td>
        </tr>
    </table>
</body>
</html>