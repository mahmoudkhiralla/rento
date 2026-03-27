@extends('dashboard.layouts.app')

@section('title', 'الملف الشخصي')

@section('content')
@php
    $admin = auth('admin')->user();
@endphp

<div class="container-fluid profile-page">
    <div class="row align-items-center mb-4">
        <div class="col-lg-8 order-lg-2">
            <h1 class="profile-title mb-3">الملف الشخصي</h1>
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="avatar-wrapper position-relative">
                    <img src="{{ $admin?->image ? asset($admin->image) : '/images/default-avatar.png' }}" alt="صورة المشرف" class="profile-avatar">
                    <a href="#" class="avatar-edit" title="تعديل الصورة"><i class="fa fa-pen"></i></a>
                </div>
                <div class="profile-info text-end">
                    <div class="text-muted small mb-1">مرحباً بك</div>
                    <div class="profile-name">{{ $admin?->name }}</div>
                    <div class="text-muted">المشرف العام</div>
                </div>
            </div>
            <!-- نموذج رفع صورة أفاتار (خفي) -->
            <form id="avatar-upload-form" action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="d-none">
                @csrf
                <input type="hidden" name="name" value="{{ $admin?->name }}">
                <input type="hidden" name="email" value="{{ $admin?->email }}">
                <input type="hidden" name="phone" value="{{ $admin?->phone }}">
                <input type="file" id="avatar-image-input" name="image" accept="image/*">
            </form>

        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="profile-actions">
                <a href="{{ route('admin.password.change') }}" class="btn btn-primary action-btn"><i class="fa fa-key ms-2"></i> تغيير كلمة المرور</a>
                <a href="{{ route('admin.profile.edit') }}" class="btn btn-outline-primary action-btn"><i class="fa fa-pen ms-2"></i> تعديل الملف الشخصي</a>
                <a href="#" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();" class="btn btn-outline-danger action-btn"><i class="fa fa-right-from-bracket ms-2"></i> تسجيل خروج</a>
            </div>
            <form id="admin-logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">@csrf</form>
        </div>
    </div>

    <div class="row g-3 mt-3">
        <div class="col-md-6">
            <div class="info-box">
                <div class="info-label">رقم الهاتف</div>
                <div class="info-value">{{ $admin?->phone ?? '—' }}</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="info-box">
                <div class="info-label">البريد الإلكتروني</div>
                <div class="info-value">{{ $admin?->email }}</div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .profile-title { font-size: 26px; font-weight: 700; color: var(--text-primary); }
    .profile-name { font-size: 28px; font-weight: 700; color: var(--text-primary); }
    .avatar-wrapper { width: 110px; height: 110px; }
    .profile-avatar { width: 110px; height: 110px; object-fit: cover; border-radius: 16px; }
    .avatar-edit { position: absolute; bottom: 6px; left: 6px; width: 30px; height: 30px; background: var(--primary-color); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; box-shadow: 0 4px 10px rgba(0,0,0,.15); }

    .profile-actions { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; width: 100%; }
    .profile-actions .action-btn { width: 100%; padding: 20px 28px; border-radius: 12px; font-weight: 700; min-height: 72px; font-size: 16px; }
    .profile-actions .btn-outline-danger { background: #fff5f5; border-color: #ffc9c9; color: #dc3545; }
    .profile-actions .btn-outline-primary { background: #f5f9ff; border-color: #cfe1ff; }

    .info-box { background: #fff; border: 1px solid var(--border-color); border-radius: 12px; padding: 16px; min-height: 64px; }
    .info-label { font-size: 12px; color: var(--text-secondary); }
    .info-value { font-size: 14px; font-weight: 600; color: var(--text-primary); margin-top: 6px; }
</style>
@endpush

@push('scripts')
<script>
    (function() {
        const editBtn = document.querySelector('.avatar-edit');
        const fileInput = document.getElementById('avatar-image-input');
        const form = document.getElementById('avatar-upload-form');

        if (editBtn && fileInput && form) {
            editBtn.addEventListener('click', function(e) {
                e.preventDefault();
                fileInput.click();
            });

            fileInput.addEventListener('change', function() {
                const file = fileInput.files[0];
                if (!file) return;
                // تحقق سريع من الحجم (2MB)
                const maxSize = 2 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('حجم الصورة يتجاوز 2MB. الرجاء اختيار صورة أصغر.');
                    fileInput.value = '';
                    return;
                }
                form.submit();
            });
        }
    })();
</script>
@endpush
