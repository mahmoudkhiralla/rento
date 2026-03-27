@extends('dashboard.layouts.app')

@section('title', 'قائمة العقارات النشطة')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">العقارات النشطة</h4>
        <div>
            <select class="form-select d-inline-block w-auto me-2">
                <option>كل الأنواع</option>
                <option>شقق</option>
                <option>فلل</option>
                <option>مكاتب</option>
            </select>
            <button class="btn btn-primary">
                <i class="fas fa-plus"></i> إضافة عقار
            </button>
        </div>
    </div>

    <div class="row g-4">
        @for($i = 1; $i <= 12; $i++)
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="position-relative">
                        <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Property">
                        <span class="badge bg-success position-absolute top-0 start-0 m-2">نشط</span>
                        <span class="badge bg-primary position-absolute top-0 end-0 m-2">مميز</span>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">شقة فاخرة {{ $i }}</h6>
                        <p class="card-text text-muted small mb-2">
                            <i class="fas fa-map-marker-alt"></i> الرياض، حي النخيل
                        </p>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="text-muted small">
                                <i class="fas fa-bed"></i> 3
                                <i class="fas fa-bath ms-2"></i> 2
                                <i class="fas fa-ruler-combined ms-2"></i> 150م²
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="text-primary mb-0">2,500 ر.س</h5>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary"><i class="fas fa-eye"></i></button>
                                <button class="btn btn-outline-secondary"><i class="fas fa-edit"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endfor
    </div>

    <div class="d-flex justify-content-center mt-4">
        <nav>
            <ul class="pagination">
                <li class="page-item"><a class="page-link" href="#">1</a></li>
                <li class="page-item active"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">التالي</a></li>
            </ul>
        </nav>
    </div>
@endsection
