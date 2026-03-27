@extends('dashboard.layouts.app')

@section('title', 'تعديل الملف الشخصي')

@section('content')
@php
    $admin = auth('admin')->user();
@endphp

<div class="container-fluid profile-page">
    <div class="row align-items-center mb-4">
        <div class="col-12">
            <h1 class="profile-title mb-3">تعديل الملف الشخصي</h1>
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
                <a href="{{ route('admin.password.change') }}" class="btn btn-primary action-btn"><i class="fa fa-key ms-2"></i> تغيير كلمة المرور</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card form-card p-3">
                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">الاسم الكامل</label>
                            <input type="text" name="name" class="form-control" value="{{ $admin?->name }}" placeholder="أدخل الاسم">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" name="email" class="form-control" value="{{ $admin?->email }}" placeholder="example@mail.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">رقم الهاتف</label>
                            <input type="text" name="phone" class="form-control" value="{{ $admin?->phone }}" placeholder="+218">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الصورة الشخصية</label>
                            <input type="file" name="image" class="form-control">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('admin.profile') }}" class="btn btn-light">إلغاء</a>
                        <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
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
    .avatar-edit { position: absolute; bottom: 6px; left: 6px; width: 30px; height: 30px; background: var(--primary-color); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; box-shadow: 0 4px 10px rgba(0,0,0,.15); }

    .profile-actions { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }
    .profile-actions .action-btn { width: 100%; padding: 20px 28px; border-radius: 12px; font-weight: 700; min-height: 64px; font-size: 16px; }
    .profile-actions .btn-outline-primary { background: #f5f9ff; border-color: #cfe1ff; }

    .form-card { border: 1px solid var(--border-color); border-radius: 12px; background: #fff; }
    .form-label { font-weight: 600; color: var(--text-secondary); }
</style>
@endpush