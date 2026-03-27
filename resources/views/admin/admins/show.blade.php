@extends('dashboard.layouts.app')

@section('title', 'عرض بيانات مشرف')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="page-header mb-4 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">عرض بيانات مشرف</h1>
                <p class="page-subtitle">تفاصيل المشرف المختار</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary modern-btn">
                    <i class="fas fa-arrow-right"></i>
                    رجوع للقائمة
                </a>
                <a href="{{ route('admin.admins.edit', $admin) }}" class="btn btn-primary modern-btn">
                    <i class="fas fa-edit"></i>
                    تعديل
                </a>
                <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا المشرف؟');" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger modern-btn">
                        <i class="fas fa-trash"></i>
                        حذف
                    </button>
                </form>
            </div>
        </div>

        <div class="card modern-card">
            <div class="card-header">
                <div class="card-header-content">
                    <i class="fas fa-user"></i>
                    <h2>بيانات المشرف</h2>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-4 align-items-center">
                    <div class="col-md-3">
                        <div class="user-avatar user-avatar-lg">
                            @php
                                $imagePath = $admin->image;
                                $imageUrl = null;
                                if ($imagePath) {
                                    $imagePath = ltrim($imagePath, '/');
                                    $imagePath = preg_replace('#^storage/#', '', $imagePath);
                                    $imageUrl = asset('storage/' . $imagePath);
                                }
                            @endphp
                            @if($imageUrl)
                                <img src="{{ $imageUrl }}" alt="{{ $admin->name }}">
                            @else
                                <span>{{ strtoupper(substr($admin->name, 0, 1)) }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="detail-item"><span class="detail-label">الاسم:</span> <span class="detail-value">{{ $admin->name }}</span></div>
                        <div class="detail-item"><span class="detail-label">البريد الإلكتروني:</span> <span class="detail-value">{{ $admin->email }}</span></div>
                        <div class="detail-item"><span class="detail-label">رقم الهاتف:</span> <span class="detail-value">{{ $admin->phone ?? '—' }}</span></div>
                        <div class="detail-item"><span class="detail-label">تاريخ الإنشاء:</span> <span class="detail-value">{{ $admin->created_at?->format('Y-m-d H:i') }}</span></div>
                        <div class="detail-item"><span class="detail-label">المعرف:</span> <span class="detail-value">#{{ $admin->id }}</span></div>
                    </div>
                </div>
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
        .user-avatar-lg { width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); display:flex; align-items:center; justify-content:center; color:#fff; font-size:42px; overflow:hidden; }
        .user-avatar-lg img { width:100%; height:100%; object-fit:cover; }
        .detail-item { margin-bottom: 10px; }
        .detail-label { color: var(--text-secondary); font-weight:600; margin-left: 6px; }
        .detail-value { color: var(--text-primary); }
    </style>
    @endpush
@endsection