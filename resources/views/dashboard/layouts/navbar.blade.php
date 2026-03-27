<nav class="top-navbar">
    <div class="search-box">
        <input type="text" placeholder="ابحث عن عميل أو عامل أو طلب">
        <i class="fas fa-search"></i>
    </div>

    <div class="user-menu">
        @php $admin = auth('admin')->user(); @endphp
        <div class="dropdown">
            <div class="profile-toggle" id="profileDropdown">
                <img src="{{ $admin?->image ? asset($admin->image) : '/images/default-avatar.png' }}" alt="User Avatar" class="user-avatar">
                <div class="user-info">
                    <span class="user-name">{{ $admin?->name ?? 'مدير صالح' }}</span>
                    <span class="user-role">مشرف عام</span>
                </div>
                <i class="fas fa-chevron-down dropdown-arrow"></i>
            </div>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                <li class="dropdown-header">
                    <div class="user-profile-header">
                        <div class="dropdown-user-info">
                            <span class="dropdown-user-name">{{ $admin?->name ?? 'محسن صالح' }}</span>
                            <span class="dropdown-user-role">مشرف عام</span>
                        </div>
                        <img src="{{ $admin?->image ? asset($admin->image) : '/images/default-avatar.png' }}" alt="User Avatar" class="dropdown-user-avatar">
                    </div>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item profile-link" href="{{ route('admin.profile') }}">
                        <span>الملف الشخصي</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('admin.admins.index') }}">
                        <span>إدارة المشرفين</span>
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('admin.logout') }}" class="w-100">
                        @csrf
                        <button type="submit" class="dropdown-item logout-item">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>تسجيل خروج</span>
                        </button>
                    </form>
                </li>
            </ul>
        </div>

    </div>
</nav>

<style>
    .dropdown {
        position: relative;
        display: inline-block;
    }

    .profile-toggle {
        display: flex;
        align-items: center;
        background: none;
        border: none;
        cursor: pointer;
        padding: 5px 10px;
        border-radius: 8px;
        transition: background-color 0.2s;
        width: 100%;
    }

    .profile-toggle:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }

    .dropdown-menu {
        position: absolute;
        top: 100%;
        right: 8px; /* anchor to right edge with small inset */
        transform: translateX(130px); /* no left shift */
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        min-width: 300px; /* smaller card width */
        padding: 0;
        margin-top: 10px;
        z-index: 1000;
        display: none;
        border: 1px solid #e0e0e0;
    }

    .dropdown-menu.show {
        display: block;
    }

    .dropdown-header {
        padding: 0;
        margin: 0;
        background: transparent;
    }

    .user-profile-header {
        display: flex;
        align-items: center;
        padding: 20px;
        gap: 12px;
        flex-direction: row-reverse;
    }

    .dropdown-user-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
    }

    .dropdown-user-info {
        display: flex;
        flex-direction: column;
        text-align: right;
        flex: 1;
    }

    .dropdown-user-name {
        font-weight: 600;
        font-size: 16px;
        color: #333;
    }

    .dropdown-user-role {
        font-size: 13px;
        color: #999;
        margin-top: 2px;
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 20px;
        color: #333;
        text-decoration: none;
        transition: background-color 0.2s;
        font-size: 15px;
        background: none;
        border: none;
        width: 100%;
        text-align: right;
        cursor: pointer;
    }

    .dropdown-item i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
        color: #6c757d;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
    }

    .profile-link {
        color: #0d6efd;
        font-weight: 500;
    }

    .profile-link:hover {
        background-color: #e7f1ff;
        color: #0d6efd;
    }

    .logout-item {
        color: #dc3545;
        font-weight: 500;
    }

    .logout-item i {
        color: #dc3545;
    }

    .logout-item:hover {
        background-color: #ffe5e5;
        color: #dc3545;
    }
    /* Keep the logout text visible without wrapping */
    .logout-item span {
        white-space: nowrap;
    }

    .dropdown-divider {
        margin: 0;
        border-top: 1px solid #f0f0f0;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        margin-left: 10px;
    }

    .user-info {
        display: flex;
        flex-direction: column;
        text-align: right;
    }

    .user-name {
        font-weight: 600;
        font-size: 14px;
        color: #333;
    }

    .user-role {
        font-size: 12px;
        color: #6c757d;
    }

    .dropdown-arrow {
        margin-right: 5px;
        color: #6c757d;
        font-size: 12px;
        transition: transform 0.2s;
    }

    .profile-toggle.active .dropdown-arrow {
        transform: rotate(180deg);
    }

    form.w-100 {
        margin: 0;
        width: 100%;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const profileToggle = document.getElementById('profileDropdown');
        const dropdownMenu = document.querySelector('.dropdown-menu');

        // Toggle dropdown on click
        profileToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            this.classList.toggle('active');
            dropdownMenu.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!profileToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                profileToggle.classList.remove('active');
                dropdownMenu.classList.remove('show');
            }
        });

        // Close dropdown when clicking on a menu item
        const menuItems = document.querySelectorAll('.dropdown-item');
        menuItems.forEach(item => {
            item.addEventListener('click', function() {
                profileToggle.classList.remove('active');
                dropdownMenu.classList.remove('show');
            });
        });
    });
</script>
