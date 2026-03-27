{{-- User Profile Card Component --}}
<div class="user-profile-card">
    <div class="profile-card-header">
        <div class="profile-arrow">
            <i class="fas fa-chevron-up"></i>
        </div>
        <div class="profile-info">
            <div class="profile-avatar">
                <img src="{{ auth()->user()->avatar ?? '/images/default-avatar.png' }}" alt="صورة المستخدم">
            </div>
            <div class="profile-details">
                <h4 class="profile-name">{{ auth()->user()->name ?? 'محسن صالح' }}</h4>
                <p class="profile-role">{{ auth()->user()->role ?? 'مشرف عام' }}</p>
            </div>
        </div>
    </div>
    
    <div class="profile-card-body">
        <div class="profile-section">
            <h5 class="section-title">الملف الشخصي</h5>
        </div>
        
        <div class="profile-actions">
            <form method="POST" action="{{ route('admin.logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>تسجيل خروج</span>
                </button>
            </form>
        </div>
    </div>
</div>

<style>
.user-profile-card {
    position: absolute;
    top: 100%;
    left: 0;
    width: 280px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
    border: 1px solid #E5E7EB;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
}

.user-profile-card.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.profile-card-header {
    padding: 20px;
    border-bottom: 1px solid #F3F4F6;
    position: relative;
}

.profile-arrow {
    position: absolute;
    top: -8px;
    right: 20px;
    width: 16px;
    height: 16px;
    background: white;
    border: 1px solid #E5E7EB;
    border-bottom: none;
    border-right: none;
    transform: rotate(45deg);
}

.profile-arrow i {
    display: none;
}

.profile-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.profile-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    overflow: hidden;
    border: 2px solid #E5E7EB;
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-details {
    flex: 1;
}

.profile-name {
    font-size: 16px;
    font-weight: 600;
    color: #1F2937;
    margin: 0 0 4px 0;
}

.profile-role {
    font-size: 13px;
    color: #6B7280;
    margin: 0;
}

.profile-card-body {
    padding: 16px 20px 20px;
}

.profile-section {
    margin-bottom: 16px;
}

.section-title {
    font-size: 15px;
    font-weight: 600;
    color: #2B7FE6;
    margin: 0;
    text-align: center;
}

.profile-actions {
    border-top: 1px solid #F3F4F6;
    padding-top: 16px;
}

.logout-form {
    margin: 0;
}

.logout-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 16px;
    background: #EF4444;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.logout-btn:hover {
    background: #DC2626;
    transform: translateY(-1px);
}

.logout-btn i {
    font-size: 16px;
}

/* Responsive */
@media (max-width: 768px) {
    .user-profile-card {
        width: 260px;
        right: 10px;
        left: auto;
    }
}
</style>