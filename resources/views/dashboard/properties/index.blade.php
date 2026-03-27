@extends('dashboard.layouts.app')

@section('title', 'كل العقارات')

@push('styles')
<style>
    .metric-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: 0.2s ease-in-out;
    }

    .metric-card:hover {
        transform: translateY(-2px);
    }

    .metric-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .metric-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .metric-icon {
        background: #f3f4f6;
        color: #374151;
        font-size: 21px;
        margin-right: 10px;
        padding: 10px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .metric-title {
        font-size: 14px;
        color: #6b7280;
        margin-bottom: 3px;
        padding: 10px;
    }

    .metric-value {
        font-size: 20px;
        font-weight: 700;
        color: #111827;
    }

    .metric-change {
        margin-left: 10px;
        margin-top: 25px;
        font-size: 13px;
        font-weight: 600;
        color: rgba(5, 150, 105, 1);
    }

    .metric-subtext {
        font-size: 12px;
        color: rgba(5, 150, 105, 1);
        margin-top: 2px;
    }

    .metric-btn {
        background-color: #3b82f6;
        color: #fff;
        border: none;
        border-radius: 0 0 10px 10px;
        width: 100%;
        padding: 10px;
        margin-top: 10px;
        font-size: 13px;
        font-weight: 600;
        transition: background 0.2s;
        display: block;            /* لضمان عرض بعرض الكرت */
        text-align: center;        /* محاذاة النص مثل الزر */
        text-decoration: none;     /* إزالة تحت الخط للرابط */
        cursor: pointer;           /* مؤشر يشبه الزر */
    }

    .metric-btn:hover {
        background-color: #2563eb;
    }


    .properties-box {
        background: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        padding: 16px;
        position: relative;
    }
    .properties-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-direction: row-reverse; /* العنوان على اليمين وشريط البحث على اليسار */
        margin-bottom: 14px;
    }
    .properties-title {
        font-size: 18px;
        font-weight: 700;
        color: #1F2937;
    }
    .searchbar {
        display: flex;
        align-items: center;
        gap: 8px;
        position: relative; /* لإظهار قائمة التصفية أسفل الزر */
    }
    .searchbar .filter-btn {
        border: 1px solid rgba(63, 149, 253, 1);
        border-radius: 8px;
        background: #fff;
        color: rgba(63, 149, 253, 1);
        padding: 8px 12px;
        font-size: 13px;
    }
    .searchbar .search-input {
        width: 360px;
        max-width: 100%;
        border: 1px solid #E5E7EB;
        border-radius: 10px;
        padding: 9px 14px;
        outline: none;
    }

    /* قائمة التصفية المنسدلة */
    .filter-dropdown {
        position: absolute;
        left: 0; /* تظهر أسفل زر التصفية يسار شريط البحث */
        top: 44px;
        min-width: 220px;
        background: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 10px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        padding: 8px;
        display: none;
        z-index: 10;
    }
    .filter-dropdown.show { display: block; }
    .filter-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 10px;
        border-radius: 8px;
        color: #1F2937;
        font-size: 13px;
        cursor: pointer;
    }
    .filter-item + .filter-item { border-top: 1px solid #F0F0F0; }
    .filter-item:hover { background: #F3F4F6; }
    .filter-item .icon { color: #6B7280; font-size: 12px; margin-inline-start: 8px; }
    .filter-item.active { font-weight: 600; color: #111827; }
    .filter-item.static { cursor: default; }
    .filter-select { padding: 6px 10px; }
    .filter-select select {
        width: 100%;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        padding: 6px 8px;
        font-size: 13px;
        color: #1F2937;
        background: #fff;
    }

    .filters-side {
        position: absolute;
        left: 16px;
        top: 64px;
        width: 130px;
    }
    .filter-chip {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        padding: 8px 10px;
        margin-bottom: 10px;
        background: #fff;
        font-size: 13px;
        color: #1F2937;
    }
    .filter-chip i { color: #6B7280; font-size: 11px; }

    .table-area { padding: 0; }
    .properties-table { direction: rtl; }
    .properties-table thead th {
        background: #E9F3FF;
        color: #1F2937;
        font-weight: 600;
        border-bottom: none;
        text-align: right;
    }
    .properties-table tbody td { color: #374151; text-align: right; }
    .property-name { display: flex; align-items: center; direction: rtl; flex-direction: row; gap: 8px; }
    .properties-table .property-thumb { width: 36px; height: 36px; border-radius: 10px; object-fit: cover; border: 1px solid #E5E7EB; background: #F3F4F6; }
    .action-link { color: #2B7FE6; font-weight: 600; text-decoration: none; }
    .active-filter-chip { display: inline-block; margin-right: 8px; font-size: 12px; color: #2563eb; background: #E9F3FF; border: 1px solid #CDE3FF; border-radius: 8px; padding: 4px 8px; }

    /* Footer + Rento Pagination (same as users list) */
    .table-footer {
        padding: 16px 24px;
        background: #F9FAFB;
        border-top: 1px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .pagination-info {
        font-size: 14px;
        color: #555;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 14px;
        display: inline-block;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        margin-top: 10px;
    }

    .rento-pagination { direction: ltr; }
    .rento-pages {
        list-style: none;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 0;
        margin: 0;
    }
    .rento-item {}
    .rento-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        height: 36px;
        padding: 0 12px;
        border: 1px solid #E0E0E0;
        border-radius: 8px;
        background: #ffffff;
        color: #555;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
    }
    .rento-item .rento-link:hover { border-color: #D0D0D0; background: #F8F8F8; }
    .rento-item.active .rento-link {
        background: #F6F8FA;
        border-color: #31C48D;
        box-shadow: 0 0 0 2px rgba(49,196,141,0.25);
        color: #111;
    }
    .rento-item.disabled .rento-link { color: #AAA; background: #FAFAFA; cursor: not-allowed; }
    .rento-ellipsis .rento-link { pointer-events: none; cursor: default; }
</style>
@endpush

@section('content')
    <div class="row g-4 mb-4 metrics-row">

        <!-- إجمالي العقارات -->
        <div class="col-xl-4 col-md-6">
            <div class="metric-card">
                <div class="metric-top">
                    <div class="metric-left">
                        <div class="metric-icon"><i class="fas fa-building"></i></div>
                        <div>
                            <div class="metric-title">إجمالي العقارات</div>
                            <div class="metric-value">{{ number_format($totalProperties) }}</div>
                        </div>
                    </div>
                    <div class="metric-change text-success">
                        <i class="fas fa-arrow-up"></i> {{ $recentAdded }}
                    </div>
                </div>
                <a class="metric-btn" href="{{ route('dashboard.properties.index', ['filter' => 'all']) }}">عرض الكل</a>
            </div>
        </div>

        <!-- العقارات النشطة -->
        <div class="col-xl-4 col-md-6">
            <div class="metric-card">
                <div class="metric-top">
                    <div class="metric-left">
                        <div class="metric-icon"><i class="fas fa-city"></i></div>
                        <div>
                            <div class="metric-title">العقارات النشطة</div>
                            <div class="metric-value">{{ number_format($activeProperties) }}</div>
                        </div>
                    </div>
                    <div class="metric-change text-success">
                        <i class="fas fa-arrow-up"></i> {{ $recentApproved }}
                    </div>
                </div>
                <a class="metric-btn" href="{{ route('dashboard.properties.index', ['filter' => 'active']) }}">عرض الكل</a>
            </div>
        </div>

        <!-- المضافة حديثاً -->
        <div class="col-xl-4 col-md-6">
            <div class="metric-card">
                <div class="metric-top">
                    <div class="metric-left">
                        <div class="metric-icon"><i class="fas fa-rotate-right"></i></div>
                        <div>
                            <div class="metric-title">المضافة حديثًا</div>
                            <div class="metric-value">{{ number_format($recentAdded) }}</div>
                        </div>
                    </div>
                    <div class="metric-change text-success">آخر 7 أيام</div>
                </div>
                <a class="metric-btn" href="{{ route('dashboard.properties.index', ['filter' => 'recent']) }}">عرض الكل</a>
            </div>
        </div>

    </div>

    <div class="properties-box">
        <div class="properties-header">
            <div class="searchbar">
                <form method="GET" action="{{ route('dashboard.properties.index') }}" class="d-flex align-items-center gap-2">
                    <input type="text" name="q" class="search-input" value="{{ request('q') }}" placeholder="ابحث عن عقار أو مدينة أو مؤجر">
                </form>
                <button class="filter-btn" id="filterToggle"><i class="fas fa-filter ms-1" style="color: rgba(63, 149, 253, 1)"></i> تصفية</button>
                <div class="filter-dropdown" id="filterDropdown">
                    <form method="GET" action="{{ route('dashboard.properties.index') }}" id="filterForm">
                        <input type="hidden" name="q" value="{{ request('q') }}">
                        <input type="hidden" name="filter" value="{{ request('filter') }}">
                        <input type="hidden" name="rental_type" id="rentalTypeInput" value="{{ request('rental_type') }}">

                        @php $currentRental = (string) request()->input('rental_type', ''); @endphp
                        @foreach(($rentalTypes ?? []) as $rt)
                            @php $isActive = $currentRental === (string) $rt; @endphp
                            <div class="filter-item instant-rental-item {{ $isActive ? 'active' : '' }}" data-value="{{ $rt }}">
                                <span>{{ $rt }}</span>
                                <span class="icon">@if($isActive)<i class="fas fa-check"></i>@endif</span>
                            </div>
                        @endforeach

                        <div class="filter-item static">
                            <span>نوع العقار</span>
                        </div>
                        <div class="filter-select">
                            <select name="property_type_id" class="form-select form-select-sm">
                                <option value="">الكل</option>
                                @foreach(($propertyTypes ?? []) as $pt)
                                    <option value="{{ $pt->id }}" {{ (string) request('property_type_id') === (string) $pt->id ? 'selected' : '' }}>{{ $pt->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-item static">
                            <span>المدينة</span>
                        </div>
                        <div class="filter-select">
                            <select name="city" class="form-select form-select-sm">
                                <option value="">الكل</option>
                                @foreach(($citiesList ?? []) as $city)
                                    <option value="{{ $city }}" {{ (string) request('city') === (string) $city ? 'selected' : '' }}>{{ $city }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            </div>
            <div class="properties-title">كل العقارات
                @if(!empty($activeFilter) && $activeFilter !== 'all')
                    <span class="active-filter-chip">
                        @if($activeFilter === 'active') العقارات النشطة
                        @elseif($activeFilter === 'recent') المضافة حديثًا
                        @endif
                    </span>
                @endif
            </div>
        </div>

        <div class="table-responsive table-area">
            <table class="table table-hover align-middle properties-table">
                <thead>
                <tr>
                    <th>اسم العقار</th>
                    <th>اسم المؤجر</th>
                    <th>نوع الإيجار</th>
                    <th>نوع العقار</th>
                    <th>المدينة والعنوان</th>
                    <th>السعر</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($properties as $property)
                    <tr>
                        <td>
                            <div class="property-name">
                                @php
                                    $img = $property->image ? asset($property->image) : asset('images/rento-logo.svg');
                                @endphp
                                <img src="{{ $img }}" alt="صورة {{ $property->title }}" class="property-thumb">
                                <span>{{ $property->title }}</span>
                            </div>
                        </td>
                        <td>{{ optional($property->user)->name ?? '—' }}</td>
                        <td>{{ $property->rental_type ?? '—' }}</td>
                        <td>{{ optional($property->type)->name ?? '—' }}</td>
                        <td class="text-muted small">
                            @php
                                $city = $property->city ?? '';
                                $addr = $property->address ?? '';
                                $cityAddr = trim($city . ($addr ? ' - ' . $addr : ''));
                            @endphp
                            {{ $cityAddr ?: '—' }}
                        </td>
                        <td class="fw-semibold">د.ل {{ number_format($property->price ?? 0) }}</td>
                        <td><a href="{{ route('dashboard.properties.preview', $property) }}" class="action-link">معاينة</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        @if(isset($properties) && $properties->hasPages())
            <div class="table-footer">
                <div class="pagination-info">
                    عرض {{ $properties->firstItem() }} إلى {{ $properties->lastItem() }} من {{ $properties->total() }}
                </div>
                <div class="pagination-wrapper">
                    {{ $properties->onEachSide(1)->links('vendor.pagination.rento') }}
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('filterToggle');
        const dropdown = document.getElementById('filterDropdown');
        const form = document.getElementById('filterForm');
        const rentalInput = document.getElementById('rentalTypeInput');
        if (toggleBtn && dropdown) {
            toggleBtn.addEventListener('click', function (e) {
                e.preventDefault();
                dropdown.classList.toggle('show');
            });
            document.addEventListener('click', function (e) {
                if (!dropdown.contains(e.target) && !toggleBtn.contains(e.target)) {
                    dropdown.classList.remove('show');
                }
            });
        }

        // نقر فوري على نوع الإيجار
        if (form && rentalInput) {
            document.querySelectorAll('.instant-rental-item').forEach(function (item) {
                item.addEventListener('click', function () {
                    const val = this.getAttribute('data-value') || '';
                    // toggle: إذا نفس القيمة، امسح التحديد
                    rentalInput.value = (rentalInput.value === val) ? '' : val;
                    form.submit();
                });
            });
        }

        // إرسال تلقائي عند تغيير القوائم
        document.querySelectorAll('#filterDropdown select').forEach(function (sel) {
            sel.addEventListener('change', function () {
                if (form) form.submit();
            });
        });
    });
</script>
@endpush
