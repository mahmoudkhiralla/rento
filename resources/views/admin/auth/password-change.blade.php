@extends('dashboard.layouts.app')

@section('title', 'تغيير كلمة المرور')

@section('content')
@php
    $admin = auth('admin')->user();
@endphp

<div class="container-fluid profile-page">
    <div class="row align-items-center mb-4">
        <div class="col-12">
            <h1 class="profile-title mb-3">تغيير كلمة المرور</h1>
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="avatar-wrapper position-relative">
                    <img src="{{ $admin?->image ? asset($admin->image) : '/images/default-avatar.png' }}" alt="صورة المشرف" class="profile-avatar">
                </div>
                <div class="profile-info text-end">
                    <div class="text-muted small mb-1">مرحباً بك</div>
                    <div class="profile-name">{{ $admin?->name }}</div>
                    <div class="text-muted">المشرف العام</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="profile-actions">
                <a href="{{ route('admin.profile') }}" class="btn btn-outline-primary action-btn"><i class="fa fa-user ms-2"></i> الرجوع للملف الشخصي</a>
                <a href="{{ route('admin.profile.edit') }}" class="btn btn-primary action-btn"><i class="fa fa-pen ms-2"></i> تعديل الملف الشخصي</a>
            </div>
        </div>
    </div>

            <div class="card form-card p-3">
                <form action="{{ route('admin.password.update') }}" method="POST">
                    @csrf
                    @if ($errors->any())
                        <div class="alert alert-danger mb-3">يوجد أخطاء في الإدخال، يرجى مراجعة الحقول أدناه.</div>
                    @endif
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">كلمة المرور الحالية</label>
                            <div class="password-field">
                                <input type="password" id="current_password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="••••••••">
                                <button type="button" class="toggle-password" data-target="current_password" aria-label="إظهار/إخفاء كلمة المرور الحالية"><i class="fa fa-eye"></i></button>
                                @error('current_password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">كلمة المرور الجديدة</label>
                            <div class="password-field">
                                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••">
                                <button type="button" class="toggle-password" data-target="password" aria-label="إظهار/إخفاء كلمة المرور الجديدة"><i class="fa fa-eye"></i></button>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">تأكيد كلمة المرور الجديدة</label>
                            <div class="password-field">
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="••••••••">
                                <button type="button" class="toggle-password" data-target="password_confirmation" aria-label="إظهار/إخفاء تأكيد كلمة المرور"><i class="fa fa-eye"></i></button>
                                @error('password_confirmation')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('admin.profile') }}" class="btn btn-light">إلغاء</a>
                        <button type="submit" class="btn btn-primary">تحديث كلمة المرور</button>
                    </div>
                </form>
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

    .profile-actions { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }
    .profile-actions .action-btn { width: 100%; padding: 20px 28px; border-radius: 12px; font-weight: 700; min-height: 64px; font-size: 16px; }
    .profile-actions .btn-outline-primary { background: #f5f9ff; border-color: #cfe1ff; }

    .form-card { border: 1px solid var(--border-color); border-radius: 12px; background: #fff; }
    .form-label { font-weight: 600; color: var(--text-secondary); }

    .password-field { position: relative; }
    .password-field .form-control { padding-left: 42px; }
    .toggle-password { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 32px; height: 32px; border: none; background: transparent; color: var(--text-secondary); display: flex; align-items: center; justify-content: center; cursor: pointer; }
    .toggle-password:focus { outline: none; }
    .toggle-password i { font-size: 16px; }
    .form-control.is-invalid { border-color: #dc3545; }
    .invalid-feedback { font-size: 12px; }
</style>
@endpush

@push('scripts')
<script>
    (function() {
        document.querySelectorAll('.toggle-password').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const targetId = btn.getAttribute('data-target');
                const input = document.getElementById(targetId);
                if (!input) return;
                const isText = input.type === 'text';
                input.type = isText ? 'password' : 'text';
                const icon = btn.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                }
            });
        });
    })();
</script>
@endpush