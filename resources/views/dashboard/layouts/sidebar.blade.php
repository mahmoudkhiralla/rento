<aside class="sidebar">
    <div class="sidebar-header">
        <img src="/images/rento-text.svg" alt="Rento Text" class="sidebar-text">
        <img src="/images/rento-logo.svg" alt="Rento Logo" class="sidebar-logo">
    </div>

    <nav class="sidebar-menu">
        <a href="{{ route('dashboard.index') }}" class="menu-item {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>الرئيسية</span>
        </a>

        <a href="{{ route('dashboard.users.list') }}" class="menu-item {{ request()->routeIs('dashboard.users.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>المستخدمين</span>
        </a>

        @php
            // عدد طلبات نشر العقارات غير المعتمدة (إشعار ديناميكي)
            try {
                $pendingPublishCount = \App\Models\Property::where('status', 'inprogress')->count();
            } catch (\Throwable $e) {
                $pendingPublishCount = 0;
            }
        @endphp
        <div class="menu-item-group  {{ request()->routeIs('dashboard.properties.*') ? 'open' : '' }}">
            <a href="{{ route('dashboard.properties.index') }}" class="menu-item toggle hover">
                <i class="fas fa-building"></i>
                <span class="menu-title">العقارات</span>
                @if(($pendingPublishCount ?? 0) > 0 && !request()->routeIs('dashboard.properties.index'))
                    <span class="badge">{{ $pendingPublishCount }}</span>
                @endif
            </a>

            <div class="submenu">
                <a href="{{ route('dashboard.properties.requests') }}"
                   class="submenu-item {{ request()->routeIs('dashboard.properties.requests') ? 'active' : '' }}">
                    <span>طلبات نشر العقارات</span>
                    @if(($pendingPublishCount ?? 0) > 0 && !request()->routeIs('dashboard.properties.requests'))
                        <span class="badge"> {{ $pendingPublishCount }} </span>
                    @endif
                </a>

                <a href="{{ route('dashboard.properties.areas') }}"
                   class="submenu-item {{ request()->routeIs('dashboard.properties.areas') ? 'active' : '' }}">
                    <span>تحديث المناطق والتصنيفات</span>
                </a>
            </div>
        </div>

        @php
            // عدد طلبات الحجز المعلقة (إشعار ديناميكي مبدئي من السيرفر)
            try {
                $pendingOrdersCount = \App\Models\Booking::where('status', 'pending')->count();
            } catch (\Throwable $e) {
                $pendingOrdersCount = 0;
            }
        @endphp
        <a href="{{  route('dashboard.orders.latest', ['status' => 'all']) }}" class="menu-item {{ request()->routeIs('dashboard.orders.*') ? 'active' : '' }}">
            <i class="fas fa-clipboard-list"></i>
            <span>طلبات الحجز</span>
            <span id="bookingRequestsBadge" class="badge" style="{{ ($pendingOrdersCount ?? 0) > 0 ? '' : 'display:none;' }}">{{ $pendingOrdersCount ?? 0 }}</span>
        </a>

        <div class="menu-item-group {{ request()->routeIs('admin.payments.*') || request()->routeIs('dashboard.payments.*') ? 'open' : '' }}">
            <a href="{{ route('admin.payments.index') }}" class="menu-item toggle hover">
                <i class="fas fa-wallet"></i>
                <span class="menu-title">الدفع والتحصيل</span>
            </a>

            <div class="submenu">
                <a href="{{ route('dashboard.payments.cards') }}"
                   class="submenu-item {{ request()->routeIs('dashboard.payments.cards') ? 'active' : '' }}">
                    <span>إدارة بطاقات الدفع</span>
                </a>

                <a href="{{ route('dashboard.payments.refunds') }}"
                   class="submenu-item {{ request()->routeIs('dashboard.payments.refunds') ? 'active' : '' }}">
                    <span>طلبات سحب الرصيد</span>
                </a>

                <a href="{{ route('dashboard.payments.transactions') }}"
                   class="submenu-item {{ request()->routeIs('dashboard.payments.transactions') ? 'active' : '' }}">
                    <span>المحافظ و العمليات</span>
                </a>

                <a href="{{ route('dashboard.payments.commissions') }}"
                   class="submenu-item {{ request()->routeIs('dashboard.payments.commissions') ? 'active' : '' }}">
                    <span>إعدادات العمولة وبرنامج النقاط</span>
                </a>

                <a href="{{ route('dashboard.payments.stats') }}"
                   class="submenu-item {{ request()->routeIs('dashboard.payments.stats') ? 'active' : '' }}">
                    <span>الغرمات و التعويضات</span>
                </a>
            </div>
        </div>

        <a href="{{ route('dashboard.support.tickets') }}" class="menu-item {{ request()->routeIs('dashboard.support.*') ? 'active' : '' }}">
            <i class="fas fa-headset"></i>
            <span>الشكاوى والدعم</span>
            <span id="supportTicketsBadge" class="badge" style="display:none;"></span>
        </a>

        <a href="{{ route('admin.notifications.index') }}" class="menu-item {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
            <i class="fas fa-gift"></i>
            <span>إدارة الإشعارات</span>
        </a>

        <a href="{{ route('dashboard.reports') }}" class="menu-item {{ request()->routeIs('dashboard.reports') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i>
            <span>الإحصائيات والتقارير</span>
        </a>
    </nav>
</aside>

<style>
    /* أبعاد وسلوك التمرير للسايدبار */
    .sidebar {
        width: 220px;            /* تقليل العرض */
        min-width: 220px;        /* تثبيت الحد الأدنى لضمان ثبات التخطيط */
        background-color: #fff;  /* خلفية بيضاء كما المطلوب */
        overflow-y: auto;        /* تفعيل التمرير العمودي عند الحاجة */
        overflow-x: hidden;      /* إلغاء التمرير الأفقي (الاسكرول تحت) */
    }

    .menu-title{
        text-align: right;
    }
    .menu-item-group {
        margin-bottom: 12px;
    }

    .menu-item.toggle {
        background-color: #fff;
        border: none;
        width: 100%;
        text-align: right;
        font-weight: 600;
        font-size: 15px;
        color: #404040;
        padding: 8px 0;
        border-bottom: 1px solid transparent; /* يمنع تغيّر الارتفاع عند التحويم */
        position: relative; /* للسماح بعرض خط ::after أسفل الزر */
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 8px;
        column-gap: 8px;
        cursor: pointer;
    }

    /* خط سفلي متدرج للزر المجمّع (مثل العقارات) */
    .menu-item.toggle::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: 0;
        height: 2px;
        width: 100%;
        background: linear-gradient(to right, rgba(63, 149, 253, 0), rgba(30, 108, 181, 1));
        opacity: 0;
        transition: opacity 0.2s ease;
        pointer-events: none;
    }

    /* تطبيق نفس استايل زر العقارات على الروابط الرئيسية */
    .sidebar-menu > a.menu-item {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 8px;
        column-gap: 8px;
        text-align: right;
        font-weight: 600;
        font-size: 15px;
        color: #404040;
        padding: 8px 0;
        background-color: #fff; /* إزالة الخلفية الزرقاء وتعيين الأبيض */
        border-bottom: 1px solid transparent; /* يمنع تغيّر الارتفاع عند التحويم */
        text-decoration: none;
        cursor: pointer;
        margin-bottom: 12px; /* نفس مسافة مجموعات القوائم */
        position: relative; /* للسماح بعرض خط ::after أسفل الزر */
    }

    .sidebar-menu > a.menu-item:hover,
    .sidebar-menu > a.menu-item:active,
    .sidebar-menu > a.menu-item:focus {
        background-color: #fff; /* تبقى الخلفية بيضاء */
        border-bottom-color: transparent; /* الخط سيظهر عبر ::after */
    }

    /* الأزرار النشطة: خلفية بيضاء مع خط سفلي ولون نص */
    .sidebar-menu > a.menu-item.active {
        background-color: #fff; /* تبقى الخلفية بيضاء */
        border-bottom-color: transparent; /* نعرض الخط عبر ::after */
        color: #1E6CB5; /* لون النص المطلوب */
    }

    /* خط سفلي متدرج للأزرار الرئيسية بدون قائمة */
    .sidebar-menu > a.menu-item::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: 0;
        height: 2px;
        width: 100%;
        background: linear-gradient(to right, rgba(63, 149, 253, 0), rgba(30, 108, 181, 1));
        opacity: 0;
        transition: opacity 0.2s ease;
        pointer-events: none;
    }

    /* إظهار التدرج عند التحويم أو الفعالية */
    .sidebar-menu > a.menu-item:hover::after,
    .sidebar-menu > a.menu-item:active::after,
    .sidebar-menu > a.menu-item:focus::after,
    .sidebar-menu > a.menu-item.active::after {
        opacity: 1;
    }

    /* إظهار الخط السفلي فقط عند فتح القائمة الفرعية + تلوين النص */
    .menu-item-group.open .menu-item.toggle  {
        border-bottom-color: transparent; /* نستعيض عن الحد بخط ::after المتدرج */
        color: #1E6CB5;
    }

    /* إظهار الخط السفلي عند التحويم/الضغط على زر العقارات */
    .menu-item.toggle:hover,
    .menu-item.toggle:active,
    .menu-item.toggle:focus {
        background-color: #fff; /* تبقى الخلفية بيضاء */
        border-bottom-color: transparent; /* الخط يعرض عبر ::after */
    }

    /* إظهار التدرج عند التحويم أو فتح المجموعة */
    .menu-item.toggle:hover::after,
    .menu-item-group.open .menu-item.toggle::after {
        opacity: 1;
    }

    .menu-item-group .submenu {
        padding-top: 6px;
        padding-right: 34px;
        display: none;
        flex-direction: column;
        gap: 6px;
    }

    .menu-item-group.open .submenu {
        display: flex;
    }

    .submenu-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: #333;
        font-size: 14px;
        text-decoration: none;
        padding: 4px 0;
        transition: color 0.2s ease;
    }

    .submenu-item:hover,
    .submenu-item.active {
        color: #1E6CB5;
    }

    /* خصائص البادج في القائمة الفرعية فقط حتى لا تغيّر بادجات أخرى */
    .submenu .badge {
        color: white;
        background: var(--Colors-Red, rgba(255, 59, 48, 1));
        width: 16px;
        height: 15px;
        min-width: 16px;
        max-width: 34px;
        angle: 0 deg;
        opacity: 1;
        border-radius: 4px;
        padding-right: 4px;
        padding-left: 4px;
        margin-left: 10px;
    }

    /* بادج إشعار بجانب زر "العقارات" ليتطابق مع نمط القائمة الفرعية */
    .menu-item.toggle .badge {
        color: white;
        background: rgba(26, 35, 61, 1);
        width: 20px;
        height: 15px;
        min-width: 16px;
        max-width: 34px;
        border-radius: 15px;
        padding-right: 4px;
        padding-left: 4px;
        margin-right: 80px; /* لدفع الشارة لحافة العنصر اليسرى */
    }

    /* السهم أزيل من الزر، لذا لا توجد أنماط خاصة به */

    /* بادج طلبات الحجز في القائمة الرئيسية وفق المطلوب */
    #bookingRequestsBadge {
        background: rgba(127, 180, 189, 1);
        width: 16px;
        height: 16px;
        min-width: 16px;
        max-width: 34px;
        angle: 0 deg;
        opacity: 1;
        border-radius: 100px;
        padding-right: 4px;
        padding-left: 4px;
        color: #fff;
    }

    #supportTicketsBadge {
        background: rgba(255, 59, 48, 1);
        width: 16px;
        height: 16px;
        min-width: 16px;
        max-width: 34px;
        border-radius: 100px;
        padding-right: 4px;
        padding-left: 4px;
        color: #fff;
    }

</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const groups = document.querySelectorAll('.menu-item-group .toggle');
        groups.forEach(function (toggle) {
            toggle.addEventListener('click', function () {
                const parent = this.closest('.menu-item-group');
                parent.classList.toggle('open');
            });
        });
    });
</script>

<script>
    // تحديث ديناميكي لبادج "طلبات الحجز" دون التأثير على تحميل الصفحات
    document.addEventListener('DOMContentLoaded', function () {
        var badge = document.getElementById('bookingRequestsBadge');
        var endpoint = "{{ route('dashboard.orders.pending-count') }}";

        function updateBadge() {
            if (!badge) return;
            fetch(endpoint, { headers: { 'Accept': 'application/json' } })
                .then(function (res) { return res.ok ? res.json() : Promise.reject(new Error('Bad response')); })
                .then(function (data) {
                    var count = parseInt((data && data.pending) || 0, 10);
                    if (count > 0) {
                        badge.textContent = String(count);
                        badge.style.display = '';
                    } else {
                        badge.textContent = '';
                        badge.style.display = 'none';
                    }
                })
                .catch(function () {
                    // في حال الخطأ، نترك البادج كما هو بدون إزعاج المستخدم
                });
        }

        // تحديث فوري ثم كل 15 ثانية
        updateBadge();
        setInterval(updateBadge, 15000);
    });
</script>

<script>
    // تحديث ديناميكي لبادج "الشكاوى والدعم"
    document.addEventListener('DOMContentLoaded', function () {
        var badge = document.getElementById('supportTicketsBadge');
        var endpoint = "{{ route('dashboard.support.pending-count') }}";

        function updateSupportBadge() {
            if (!badge) return;
            fetch(endpoint, { headers: { 'Accept': 'application/json' } })
                .then(function (res) { return res.ok ? res.json() : Promise.reject(new Error('Bad response')); })
                .then(function (data) {
                    var count = parseInt((data && (data.unread ?? 0)) || 0, 10);
                    if (count > 0) {
                        badge.textContent = String(count);
                        badge.style.display = '';
                    } else {
                        badge.textContent = '';
                        badge.style.display = 'none';
                    }
                })
                .catch(function () {
                    // تجاهل الخطأ بدون التأثير على واجهة المستخدم
                });
        }

        updateSupportBadge();
        setInterval(updateSupportBadge, 15000);
    });
</script>
