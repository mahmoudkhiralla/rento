@extends('dashboard.layouts.app')

@section('title', 'إدارة التصنيفات')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">تصنيفات العقارات</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="fas fa-plus"></i> إضافة تصنيف
        </button>
    </div>

    <div class="row g-4">
        @foreach([
            ['name' => 'شقق', 'icon' => 'fa-building', 'count' => 450, 'color' => 'primary'],
            ['name' => 'فلل', 'icon' => 'fa-home', 'count' => 280, 'color' => 'success'],
            ['name' => 'مكاتب', 'icon' => 'fa-briefcase', 'count' => 150, 'color' => 'info'],
            ['name' => 'محلات', 'icon' => 'fa-store', 'count' => 180, 'color' => 'warning'],
            ['name' => 'أراضي', 'icon' => 'fa-map', 'count' => 120, 'color' => 'danger'],
            ['name' => 'مستودعات', 'icon' => 'fa-warehouse', 'count' => 65, 'color' => 'secondary']
        ] as $category)
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card text-center border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas {{ $category['icon'] }} fa-3x text-{{ $category['color'] }}"></i>
                        </div>
                        <h6 class="card-title">{{ $category['name'] }}</h6>
                        <p class="text-muted mb-2">{{ $category['count'] }} عقار</p>
                        <div class="btn-group btn-group-sm w-100">
                            <button class="btn btn-outline-secondary"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-outline-danger"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="chart-card mt-4">
        <h6 class="mb-3">تفاصيل التصنيفات</h6>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>التصنيف</th>
                    <th>عدد العقارات</th>
                    <th>العقارات النشطة</th>
                    <th>متوسط السعر</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
                </thead>
                <tbody>
                @foreach(['شقق', 'فلل', 'مكاتب', 'محلات', 'أراضي', 'مستودعات'] as $cat)
                    <tr>
                        <td class="fw-bold">{{ $cat }}</td>
                        <td>{{ rand(50, 500) }}</td>
                        <td><span class="badge bg-success">{{ rand(30, 450) }}</span></td>
                        <td>{{ number_format(rand(2000, 8000)) }} ر.س</td>
                        <td><span class="badge bg-success">نشط</span></td>
                        <td>
                            <button class="btn btn-sm btn-light"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-light text-danger"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">إضافة تصنيف جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">اسم التصنيف</label>
                            <input type="text" class="form-control" placeholder="مثال: شقق">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الأيقونة</label>
                            <input type="text" class="form-control" placeholder="fa-building">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">اللون</label>
                            <select class="form-select">
                                <option>أزرق (Primary)</option>
                                <option>أخضر (Success)</option>
                                <option>أحمر (Danger)</option>
                                <option>أصفر (Warning)</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-primary">حفظ</button>
                </div>
            </div>
        </div>
    </div>
@endsection
