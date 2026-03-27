@extends('dashboard.layouts.app')

@section('title', 'إدارة المشرفين')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="page-header mb-4">
            <h1 class="page-title">إدارة المشرفين</h1>
            <p class="page-subtitle">إضافة وإدارة حسابات المشرفين في النظام</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success alert-modern">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-modern">
                <i class="fas fa-exclamation-circle"></i>
                <span>يوجد أخطاء في الإدخال، يرجى مراجعة الحقول أدناه.</span>
            </div>
        @endif

        <!-- Admins Table Card -->
        <div class="card modern-card">
            <div class="card-header">
                <div class="card-header-content">
                    <i class="fas fa-users"></i>
                    <h2>قائمة المشرفين</h2>
                </div>
                <div class="card-header-actions">
                    <a href="{{ route('admin.admins.create') }}" class="btn btn-primary modern-btn" title="إضافة مشرف">
                        <i class="fas fa-user-plus"></i>
                        إضافة مشرف
                    </a>
                    <div class="search-box-small">
                        <input type="text" placeholder="بحث..." class="form-control">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table modern-table">
                        <thead>
                        <tr>
                            <th>
                                <i class="fas fa-user"></i>
                                الاسم
                            </th>
                            <th>
                                <i class="fas fa-envelope"></i>
                                البريد الإلكتروني
                            </th>
                            <th>
                                <i class="fas fa-phone"></i>
                                رقم الهاتف
                            </th>
                            <th>
                                <i class="fas fa-calendar"></i>
                                تاريخ الإنشاء
                            </th>
                            <th class="text-center">
                                <i class="fas fa-cog"></i>
                                الإجراءات
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($admins as $admin)
                            <tr>
                                <td>
                                    <div class="user-info-cell">
                                        <div class="user-avatar">
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
                                                <span>{{ substr($admin->name, 0, 1) }}</span>
                                            @endif
                                        </div>
                                        <span class="fw-semibold">{{ $admin->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $admin->email }}</td>
                                <td>
                                    @if($admin->phone)
                                        <span class="badge badge-phone">
                                            <i class="fas fa-phone-alt"></i>
                                            {{ $admin->phone }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="date-badge">
                                        {{ $admin->created_at?->format('Y-m-d') }}
                                    </span>
                                    <small class="text-muted d-block">{{ $admin->created_at?->format('H:i') }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.admins.show', $admin) }}" class="btn-action btn-view" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.admins.edit', $admin) }}" class="btn-action btn-edit" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المشرف؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action btn-delete" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center empty-state">
                                    <i class="fas fa-user-slash"></i>
                                    <p>لا توجد سجلات للمشرفين حالياً</p>
                                    <small>قم بإضافة مشرف جديد من الأعلى</small>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($admins->hasPages())
                <div class="card-footer">
                    <div class="pagination-wrapper">
                        {{ $admins->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        /* General Styling */
        .container-fluid {
            background: var(--light-bg);
            min-height: 100vh;
        }

        /* Page Header */
        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: var(--text-secondary);
            font-size: 14px;
            margin: 0;
        }

        /* Modern Alert */
        .alert-modern {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 20px;
            border-radius: 12px;
            border: none;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .alert-modern i {
            font-size: 20px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Modern Card */
        .modern-card {
            background: white;
            border-radius: 16px;
            border: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-header {
            background: var(--primary-color);
            color: white;
            padding: 20px 24px;
            border: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card-header-content i {
            font-size: 24px;
        }

        .card-header-content h2 {
            font-size: 20px;
            font-weight: 600;
            margin: 0;
        }

        .card-body {
            padding: 24px;
        }

        .card-footer {
            background: #f8f9fa;
            padding: 16px 24px;
            border-top: 1px solid var(--border-color);
        }

        /* Form Styling */
        .form-group {
            margin-bottom: 0;
        }

        .form-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-label i {
            color: var(--primary-color);
            font-size: 16px;
        }

        .modern-input {
            border: 2px solid var(--border-color);
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .modern-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(43, 127, 230, 0.12);
            outline: none;
        }

        .modern-input.is-invalid {
            border-color: #ef4444;
        }

        .invalid-feedback {
            color: var(--danger-color);
            font-size: 13px;
            margin-top: 6px;
            display: block;
        }

        /* Password Input */
        .password-input-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
            transition: color 0.3s;
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        /* File Upload */
        .file-upload-wrapper {
            position: relative;
        }

        .file-upload-wrapper input[type="file"] {
            opacity: 0;
            position: absolute;
            z-index: -1;
        }

        .file-upload-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 32px;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 0;
        }

        .file-upload-label:hover {
            border-color: var(--primary-color);
            background: rgba(43, 127, 230, 0.05);
        }

        .file-upload-label i {
            font-size: 36px;
            color: var(--primary-color);
        }

        .file-upload-label span {
            color: var(--text-secondary);
            font-size: 14px;
        }

        /* Form Actions */
        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
        }

        .modern-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            border: none;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-primary.modern-btn {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary.modern-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary.modern-btn {
            background: #e2e8f0;
            color: #475569;
        }

        .btn-secondary.modern-btn:hover {
            background: #cbd5e1;
        }

        /* Search Box Small */
        .search-box-small {
            position: relative;
        }

        .search-box-small input {
            padding: 8px 36px 8px 16px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            font-size: 14px;
            width: 200px;
        }

        .search-box-small input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .search-box-small input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
        }

        .search-box-small i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.7);
        }

        /* Modern Table */
        .modern-table {
            margin: 0;
        }

        .modern-table thead {
            background: #f8fafc;
        }

        .modern-table thead th {
            padding: 16px 20px;
            font-weight: 600;
            color: #475569;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
        }

        .modern-table thead th i {
            margin-left: 6px;
            color: #94a3b8;
        }

        .modern-table tbody td {
            padding: 16px 20px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }

        .modern-table tbody tr:last-child td {
            border-bottom: none;
        }

        .modern-table tbody tr:hover {
            background: #f8fafc;
        }

        /* User Info Cell */
        .user-info-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 16px;
            overflow: hidden;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Badges */
        .badge-phone {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: rgba(43, 127, 230, 0.12);
            color: var(--primary-color);
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
        }

        .date-badge {
            font-weight: 600;
            color: #334155;
            font-size: 14px;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .btn-action {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-edit {
            background: #dbeafe;
            color: #1e40af;
        }

        .btn-edit:hover {
            background: #3b82f6;
            color: white;
            transform: translateY(-2px);
        }

        .btn-delete {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn-delete:hover {
            background: #ef4444;
            color: white;
            transform: translateY(-2px);
        }

        .btn-view {
            background: #d1fae5;
            color: #065f46;
        }

        .btn-view:hover {
            background: #10b981;
            color: white;
            transform: translateY(-2px);
        }

        /* Empty State */
        .empty-state {
            padding: 60px 20px !important;
            text-align: center;
        }

        .empty-state i {
            font-size: 64px;
            color: #cbd5e1;
            margin-bottom: 16px;
        }

        .empty-state p {
            color: #64748b;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .empty-state small {
            color: #94a3b8;
            font-size: 14px;
        }

        /* Pagination */
        .pagination-wrapper {
            display: flex;
            justify-content: center;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .card-header {
                flex-direction: column;
                gap: 16px;
                align-items: flex-start;
            }

            .search-box-small input {
                width: 100%;
            }

            .form-actions {
                flex-direction: column;
            }

            .modern-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <script>
        // Password toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const passwordToggle = document.querySelector('.password-toggle');
            const passwordInput = document.querySelector('input[name="password"]');

            if (passwordToggle && passwordInput) {
                passwordToggle.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    const icon = this.querySelector('i');
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                });
            }

            // File upload preview
            const fileInput = document.getElementById('imageUpload');
            const fileLabel = document.querySelector('.file-upload-label span');

            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        fileLabel.textContent = this.files[0].name;
                    }
                });
            }
        });
    </script>
@endsection