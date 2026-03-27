<p align="center">
  <img src="public/images/rento-logo.svg" alt="Rento Logo" width="200">
</p>

<h1 align="center">Rento — منصة تأجير العقارات</h1>

<p align="center">
  منصة متكاملة لتأجير العقارات تربط بين المؤجرين والمستأجرين بكل سهولة وأمان
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Backend-Laravel%2010-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/Frontend-Angular-DD0031?style=for-the-badge&logo=angular&logoColor=white" alt="Angular">
  <img src="https://img.shields.io/badge/Auth-Sanctum-38BDF8?style=for-the-badge&logo=laravel&logoColor=white" alt="Sanctum">
  <img src="https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/Database-MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
</p>

---

## 📖 نبذة عن المشروع

**Rento** هو نظام متكامل لإدارة تأجير العقارات يتكون من:

- **واجهة برمجية (RESTful API)** مبنية بـ Laravel 10 لخدمة تطبيق الموبايل
- **لوحة تحكم إدارية (Admin Dashboard)** شاملة لإدارة المنصة بالكامل
- **تطبيق موبايل (Mobile App)** مبني بـ Angular للمستأجرين والمؤجرين

يدعم النظام نوعين من المستخدمين: **المؤجر (Landlord)** الذي يعرض عقاراته للتأجير، و**المستأجر (Tenant)** الذي يبحث عن عقارات للاستئجار.

---

## ✨ المميزات الرئيسية

### 🏠 إدارة العقارات
- إضافة وتعديل وحذف العقارات مع صور متعددة
- تصنيف العقارات حسب النوع (شقة، فيلا، استوديو، إلخ)
- دعم الإيجار اليومي والشهري
- نظام موافقة/رفض طلبات نشر العقارات
- إدارة المرافق (Amenities) والمناطق والمدن
- البحث المتقدم والتصفية بالكلمات المفتاحية

### 📅 نظام الحجوزات
- حجز العقارات مع تحديد تواريخ البداية والنهاية
- إدارة حالات الحجز (معلق، مؤكد، ملغي، مكتمل)
- إلغاء الحجز من طرف المستأجر أو المؤجر مع سياسات مختلفة
- إحصائيات الحجوزات للمؤجرين (إجمالي الإيرادات، أيام التأجير)

### 💰 النظام المالي
- **المحفظة الإلكترونية**: رصيد مالي لكل مستخدم مع سجل المعاملات
- **نظام النقاط والمكافآت**: نقاط إحالة وتحويل نقاط إلى رصيد
- **العمولات**: نظام عمولات قابل للتخصيص من الأدمن
- **الغرامات والجزاءات**: غرامات إلغاء تلقائية مع معاينة مسبقة
- **استرداد الأموال**: طلبات استرداد مع متابعة الحالة
- **بطاقات الدفع**: إدارة بطاقات الدفع والتحقق منها

### ⭐ التقييمات والمراجعات
- تقييم المستأجر للعقار
- تقييم المؤجر للمستأجر والعكس
- معايير تقييم متعددة (النظافة، الالتزام بالمواعيد، العناية بالعقار)

### 🔔 الإشعارات
- إشعارات فورية عبر **OneSignal** (Push Notifications)
- إشعارات داخلية في التطبيق (In-App Notifications)
- إرسال إشعارات مخصصة من لوحة التحكم

### 🎫 الدعم الفني
- نظام تذاكر دعم متكامل
- الرد على التذاكر من الأدمن والنظام
- تتبع حالة التذاكر (مفتوحة، مغلقة)
- عدّاد التذاكر غير المقروءة في لوحة التحكم

### 👤 إدارة المستخدمين
- تسجيل وتسجيل دخول مع **Laravel Sanctum**
- استعادة كلمة المرور عبر كود التحقق بالبريد
- نظام أدوار وصلاحيات عبر **Spatie Permission**
- تعليق/تفعيل حسابات المستخدمين
- روابط الإحالة (Referral System)

### 📊 لوحة التحكم الإدارية
- داشبورد رئيسي بإحصائيات شاملة
- إدارة العقارات وطلبات النشر
- إدارة الحجوزات والطلبات المعلقة
- إدارة المدفوعات والمعاملات المالية
- تقارير تفصيلية ورسوم بيانية
- إدارة المشرفين (Admins) بصلاحيات مختلفة
- إدارة المدن والمناطق

---

## 🛠️ التقنيات المستخدمة

| التقنية | الاستخدام |
|---------|-----------|
| **Laravel 10** | الـ Backend و RESTful API |
| **Angular** | تطبيق الموبايل (Frontend) |
| **Laravel Sanctum** | المصادقة وحماية الـ API |
| **Spatie Permission** | الأدوار والصلاحيات |
| **Spatie Media Library** | إدارة رفع الصور والملفات |
| **Laravel Fortify** | المصادقة الثنائية (2FA) |
| **OneSignal** | الإشعارات الفورية (Push) |
| **MySQL** | قاعدة البيانات |
| **Vite** | أداة البناء للـ Frontend Assets |
| **GuzzleHTTP** | الاتصالات الخارجية (HTTP Client) |

---

## 📁 هيكل المشروع

```
rentofinle/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # كنترولرات لوحة التحكم (21 ملف)
│   │   │   └── Api/            # كنترولرات الـ API (16 ملف)
│   │   ├── Middleware/         # الوسائط
│   │   ├── Requests/           # Form Requests
│   │   └── Resources/          # API Resources
│   ├── Models/                 # النماذج (28 نموذج)
│   ├── Mail/                   # قوالب البريد الإلكتروني
│   └── Services/               # خدمات منفصلة
├── database/
│   ├── migrations/             # ملفات الـ Migration (59 ملف)
│   ├── factories/              # مصانع البيانات
│   └── seeders/                # بذور البيانات
├── routes/
│   ├── api.php                 # مسارات الـ API
│   └── web.php                 # مسارات لوحة التحكم
├── resources/                  # Views و Assets
├── public/                     # الملفات العامة
└── config/                     # ملفات الإعدادات
```

---

## ⚙️ التثبيت والإعداد

### المتطلبات
- PHP >= 8.1
- Composer
- MySQL
- Node.js & npm

### خطوات التثبيت

**1. نسخ المشروع**
```bash
git clone https://github.com/your-username/rento.git
cd rento
```

**2. تثبيت حزم PHP**
```bash
composer install
```

**3. تثبيت حزم Node.js**
```bash
npm install
```

**4. إعداد ملف البيئة**
```bash
cp .env.example .env
php artisan key:generate
```

**5. تعديل إعدادات قاعدة البيانات في `.env`**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rento
DB_USERNAME=root
DB_PASSWORD=
```

**6. إعداد البريد الإلكتروني في `.env`**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your@gmail.com
MAIL_FROM_NAME="Rento"
```

**7. تشغيل الـ Migrations**
```bash
php artisan migrate
```

**8. تشغيل الـ Seeders (اختياري)**
```bash
php artisan db:seed
```

**9. إنشاء رابط التخزين**
```bash
php artisan storage:link
```

**10. تشغيل السيرفر**
```bash
php artisan serve
npm run dev
```

---

## 🔗 الـ API Endpoints

### المصادقة (Auth)
| Method | Endpoint | الوصف |
|--------|----------|-------|
| POST | `/api/auth/register` | تسجيل مستخدم جديد |
| POST | `/api/auth/login` | تسجيل الدخول |
| POST | `/api/auth/logout` | تسجيل الخروج |
| POST | `/api/auth/password/forgot` | نسيت كلمة المرور |
| POST | `/api/auth/password/verify-code` | التحقق من كود الاستعادة |
| POST | `/api/auth/password/reset` | إعادة تعيين كلمة المرور |
| GET | `/api/auth/referral-link` | رابط الإحالة |

### العقارات (Properties)
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/api/properties` | قائمة العقارات |
| GET | `/api/properties/published` | العقارات المنشورة |
| GET | `/api/properties/{id}` | تفاصيل عقار |
| POST | `/api/properties` | إضافة عقار 🔒 |
| PUT | `/api/properties/{id}` | تعديل عقار 🔒 |
| DELETE | `/api/properties/{id}` | حذف عقار 🔒 |
| GET | `/api/properties/mine` | عقارات المؤجر 🔒 |
| GET | `/api/properties/{id}/reviews` | تقييمات العقار |

### الحجوزات (Bookings)
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/api/bookings` | قائمة الحجوزات 🔒 |
| POST | `/api/bookings` | حجز جديد 🔒 |
| GET | `/api/bookings/{id}` | تفاصيل حجز 🔒 |
| PATCH | `/api/bookings/{id}/status` | تحديث حالة الحجز 🔒 |
| POST | `/api/bookings/{id}/cancel/renter` | إلغاء بواسطة المستأجر 🔒 |
| POST | `/api/bookings/{id}/cancel/owner` | إلغاء بواسطة المؤجر 🔒 |

### المحفظة والمعاملات (Wallet)
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/api/wallet` | عرض المحفظة 🔒 |
| POST | `/api/wallet/transactions` | إضافة معاملة 🔒 |
| POST | `/api/points/referral` | نقاط الإحالة 🔒 |
| POST | `/api/wallet/points/convert` | تحويل النقاط 🔒 |

> 🔒 = يتطلب مصادقة (Sanctum Token)

---

## 🖥️ لوحة التحكم

الوصول إلى لوحة التحكم الإدارية:

```
/admin/login
```

### أقسام لوحة التحكم:
- **الرئيسية**: إحصائيات شاملة (المستخدمين، العقارات، الحجوزات، الإيرادات)
- **العقارات**: إدارة العقارات، طلبات النشر، المناطق والتصنيفات
- **الطلبات**: الحجوزات الأخيرة، النشطة، الإحصائيات
- **المستخدمون**: قائمة المستخدمين، الحسابات المعلقة، بيانات المؤجرين والمستأجرين
- **المدفوعات**: المعاملات، العمولات، الغرامات، الاستردادات، بطاقات الدفع
- **الدعم**: التذاكر، الردود، إحصائيات الدعم
- **الإشعارات**: إرسال إشعارات مخصصة
- **التقارير**: تقارير مفصلة ورسوم بيانية
- **المشرفون**: إدارة حسابات الأدمن والصلاحيات

---

## 📱 تطبيق الموبايل

تطبيق الموبايل مبني بـ **Angular** ويتواصل مع الـ Backend عبر الـ RESTful API.

### مميزات التطبيق:
- تسجيل الدخول والتسجيل
- تصفح العقارات والبحث المتقدم
- حجز العقارات وإدارة الحجوزات
- المحفظة الإلكترونية والمعاملات
- التقييمات والمراجعات
- المفضلة
- تذاكر الدعم الفني
- الإشعارات الفورية
- إدارة المهام للمؤجرين

---

## 🗄️ نماذج قاعدة البيانات (Models)

| النموذج | الوصف |
|---------|-------|
| `User` | المستخدمين (مستأجر / مؤجر) |
| `Admin` | مشرفو النظام |
| `Property` | العقارات |
| `PropertyType` | أنواع العقارات |
| `PropertyImage` | صور العقارات |
| `Amenity` | المرافق |
| `Booking` | الحجوزات |
| `Transaction` | المعاملات المالية |
| `Wallet` | المحفظة الإلكترونية |
| `Review` | تقييمات المستخدمين |
| `PropertyReview` | تقييمات العقارات |
| `Complaint` | الشكاوى |
| `SupportTicket` | تذاكر الدعم |
| `Notification` | الإشعارات |
| `Favorite` | المفضلة |
| `Penalty` | الغرامات |
| `Refund` | الاستردادات |
| `PaymentCard` | بطاقات الدفع |
| `City` | المدن |
| `Area` | المناطق |
| `ActivePlace` | الأماكن النشطة |
| `LandlordTask` | مهام المؤجر |
| `Setting` | الإعدادات |
| `PointsTransaction` | معاملات النقاط |

---

## 🔐 الأمان

- مصادقة API Token عبر **Laravel Sanctum**
- حارس مصادقة منفصل للأدمن (`auth:admin`)
- حماية CSRF لنماذج الويب
- نظام أدوار وصلاحيات عبر **Spatie Permission**
- تعليق الحسابات المخالفة
- External API Key للتكامل الخارجي

---

## 🤝 المساهمة

1. Fork المشروع
2. أنشئ فرع جديد (`git checkout -b feature/amazing-feature`)
3. أعمل Commit لتغييراتك (`git commit -m 'Add amazing feature'`)
4. أعمل Push للفرع (`git push origin feature/amazing-feature`)
5. أفتح Pull Request

---

## 📄 الرخصة

هذا المشروع مرخص تحت رخصة [MIT](https://opensource.org/licenses/MIT).

---

<p align="center">
  صنع بـ ❤️ بواسطة فريق Rento
</p>
