@extends('dashboard.layouts.app')

@section('title', 'تعديل بيانات مشرف')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="page-header mb-4 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">تعديل بيانات مشرف</h1>
                <p class="page-subtitle">قم بتحديث بيانات المشرف ثم احفظ التغييرات</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary modern-btn">
                    <i class="fas fa-arrow-right"></i>
                    رجوع للقائمة
                </a>
                <a href="{{ route('admin.admins.show', $admin) }}" class="btn btn-outline-primary modern-btn">
                    <i class="fas fa-eye"></i>
                    عرض
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-modern">
                <i class="fas fa-exclamation-circle"></i>
                <span>يوجد أخطاء في الإدخال، يرجى مراجعة الحقول أدناه.</span>
            </div>
        @endif

        <div class="card modern-card">
            <div class="card-header">
                <div class="card-header-content">
                    <i class="fas fa-user-edit"></i>
                    <h2>نموذج تعديل البيانات</h2>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.admins.update', $admin) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-user"></i> الاسم الكامل</label>
                                <input type="text" name="name" class="form-control modern-input @error('name') is-invalid @enderror" value="{{ old('name', $admin->name) }}" placeholder="أدخل الاسم الكامل">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-envelope"></i> البريد الإلكتروني</label>
                                <input type="email" name="email" class="form-control modern-input @error('email') is-invalid @enderror" value="{{ old('email', $admin->email) }}" placeholder="example@mail.com">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-phone"></i> رقم الهاتف</label>
                                <input type="text" name="phone" class="form-control modern-input @error('phone') is-invalid @enderror" value="{{ old('phone', $admin->phone) }}" placeholder="+218 XX XXX XXXX">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-lock"></i> كلمة المرور (اختياري)</label>
                                <div class="password-input-wrapper">
                                    <input type="password" name="password" class="form-control modern-input @error('password') is-invalid @enderror" placeholder="اتركها فارغة إن لم ترغب بالتغيير">
                                    <span class="password-toggle"><i class="fas fa-eye"></i></span>
                                </div>
                                @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                <small class="text-muted">في حال تغيير كلمة المرور يجب ألا تقل عن 8 أحرف.</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-image"></i> صورة المشرف (اختياري)</label>
                                <div class="file-upload-wrapper">
                                    <input type="file" name="image" id="imageUpload" class="form-control modern-input @error('image') is-invalid @enderror" accept="image/*">
                                    <label for="imageUpload" class="file-upload-label">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <span>اختر صورة أو اسحبها هنا</span>
                                    </label>
                                </div>
                                @error('image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                @if($admin->image)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $admin->image) }}" alt="{{ $admin->name }}" style="height: 60px; border-radius: 8px; border:1px solid var(--border-color);">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary modern-btn">
                            <i class="fas fa-save"></i>
                            حفظ التغييرات
                        </button>
                        <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary modern-btn">
                            <i class="fas fa-times"></i>
                            إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .page-title { font-weight: 700; color: var(--text-primary); }
        .page-subtitle { color: var(--text-secondary); }
        .modern-card { background: #fff; border-radius: 16px; border: none; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; }
        .card-header { background: var(--primary-color); color: #fff; padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; }
        .card-header-content { display:flex; align-items:center; gap:12px; }
        .modern-btn { display:flex; align-items:center; gap:8px; border-radius:10px; font-weight:600; }
        .form-label { display:flex; align-items:center; gap:8px; font-weight:600; color: var(--text-secondary); }
        .form-label i { color: var(--primary-color); }
        .modern-input { border: 2px solid var(--border-color); border-radius: 10px; padding: 12px 16px; }
        .modern-input:focus { border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(43,127,230,0.12); outline: none; }
        .invalid-feedback { color: var(--danger-color); }
        .form-actions { display:flex; justify-content:flex-end; gap:12px; padding-top: 24px; border-top: 1px solid var(--border-color); margin-top: 16px; }
        .password-input-wrapper { position: relative; }
        .password-toggle { position: absolute; left: 16px; top:50%; transform: translateY(-50%); color: #94a3b8; cursor:pointer; }
        .password-toggle:hover { color: var(--primary-color); }
        .file-upload-wrapper { position: relative; }
        .file-upload-wrapper input[type="file"] { opacity: 0; position: absolute; z-index: -1; }
        .file-upload-label { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:12px; padding: 24px; border: 2px dashed #cbd5e1; border-radius: 12px; background:#f8fafc; cursor:pointer; }
        .file-upload-label:hover { border-color: var(--primary-color); background: rgba(43,127,230,0.05); }
        .file-upload-label i { font-size: 32px; color: var(--primary-color); }
        .file-upload-label span { color: var(--text-secondary); }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.querySelector('.password-toggle');
            const input = document.querySelector('input[name="password"]');
            if (toggle && input) {
                toggle.addEventListener('click', function() {
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    const icon = this.querySelector('i');
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                });
            }
        });
    </script>
    @endpush
@endsection