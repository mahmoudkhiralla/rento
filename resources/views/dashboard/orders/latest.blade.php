@extends('dashboard.layouts.app')
@section('title', 'طلبات الحجز')

@push('styles')
<style>
    /* بطاقات المتركس (مطابقة صفحة الخصائص) */
    .metric-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); transition: 0.2s ease-in-out; }
    .metric-card:hover { transform: translateY(-2px); }
    .metric-top { display: flex; align-items: center; justify-content: space-between; }
    .metric-left { display: flex; align-items: center; gap: 12px; }
    .metric-icon { background: #f3f4f6; color: #374151; font-size: 21px; margin-right: 10px; padding: 10px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
    .metric-title { font-size: 14px; color: #6b7280; margin-bottom: 3px; padding: 10px; }
    .metric-value { font-size: 20px; font-weight: 700; color: #111827; }
    .metric-btn { background-color: #3b82f6; color: #fff; border: none; border-radius: 0 0 10px 10px; width: 100%; padding: 10px; margin-top: 10px; font-size: 13px; font-weight: 600; transition: background 0.2s; display: block; text-align: center; text-decoration: none; cursor: pointer; }
    .metric-btn:hover { background-color: #2563eb; }

    /* صندوق ونمط الهيدر */
    .properties-box { background: #fff; border: 1px solid #E5E7EB; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 16px; position: relative; }
    .properties-header { display: flex; align-items: center; justify-content: space-between; flex-direction: row-reverse; margin-bottom: 14px; }
    .properties-title { font-size: 18px; font-weight: 700; color: #1F2937; }
    .searchbar { display: flex; align-items: center; gap: 8px; position: relative; }
    .searchbar .filter-btn { border: 1px solid rgba(63, 149, 253, 1); border-radius: 8px; background: #fff; color: rgba(63, 149, 253, 1); padding: 8px 12px; font-size: 13px; }
    .searchbar .search-input { width: 360px; max-width: 100%; border: 1px solid #E5E7EB; border-radius: 10px; padding: 9px 14px; outline: none; }

    /* قائمة التصفية المنسدلة */
    .filter-dropdown { position: absolute; left: 0; top: 44px; min-width: 220px; background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); padding: 8px; display: none; z-index: 10; }
    .filter-dropdown.show { display: block; }
    .filter-item { display: flex; justify-content: space-between; align-items: center; padding: 8px 10px; border-radius: 8px; color: #1F2937; font-size: 13px; cursor: pointer; }
    .filter-item + .filter-item { border-top: 1px solid #F0F0F0; }
    .filter-item:hover { background: #F3F4F6; }
    .filter-item .icon { color: #6B7280; font-size: 12px; margin-inline-start: 8px; }
    .filter-item.active { font-weight: 600; color: #111827; }

    /* الجدول بنفس تصميم الخصائص */
    .table-area { padding: 0; }
    .properties-table thead th { background: #E9F3FF; color: #1F2937; font-weight: 600; border-bottom: none; text-align: right; }
    .properties-table tbody td { color: #374151; text-align: right; }
    .properties-table .property-thumb { width: 36px; height: 36px; border-radius: 10px; object-fit: cover; border: 1px solid #E5E7EB; background: #F3F4F6; }
    .property-name { display: flex; align-items: center; direction: rtl; flex-direction: row; gap: 8px; }
    .action-link { color: #2B7FE6; font-weight: 600; text-decoration: none; }
    .active-filter-chip { display: inline-block; margin-right: 8px; font-size: 12px; color: #2563eb; background: #E9F3FF; border: 1px solid #CDE3FF; border-radius: 8px; padding: 4px 8px; }

    /* Footer + Pagination */
    .table-footer { padding: 16px 24px; background: #F9FAFB; border-top: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; }
    .pagination-info { font-size: 14px; color: #555; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 14px; display: inline-block; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-top: 10px; }

    /* Rento Pagination Styles (موحد مع صفحة العقارات) */
    nav.rento-pagination { direction: ltr; }
    .rento-pages { list-style: none; margin: 0; padding: 0; display: flex; align-items: center; gap: 6px; }
    .rento-item { line-height: 1; }
    .rento-link {
        display: flex; align-items: center; justify-content: center;
        min-width: 32px; height: 32px; padding: 0 10px;
        font-size: 13px; text-decoration: none;
        background: #fff; color: #374151;
        border: 1px solid #E5E7EB; border-radius: 8px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    }
    .rento-item.active .rento-link { background: #2B7FE6; color: #fff; border-color: #2B7FE6; font-weight: 600; }
    .rento-item.disabled .rento-link { background: #F3F4F6; color: #9CA3AF; border-color: #E5E7EB; cursor: not-allowed; }
    .rento-item:not(.disabled):not(.active) .rento-link:hover { background: #F8FAFC; color: #111827; border-color: #D1D5DB; }
    .rento-ellipsis .rento-link { background: transparent; border: none; box-shadow: none; cursor: default; }

    /* عناصر المستخدم والحالات */
    .user-cell { display: flex; align-items: center; gap: 12px; }
    .user-avatar-wrapper { flex-shrink: 0; }
    .user-avatar { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; }
    .user-avatar-placeholder { width: 32px; height: 32px; border-radius: 50%; background: #E5E7EB; color: #6B7280; display: flex; align-items: center; justify-content: center; }
    .user-name { font-weight: 500; color: #1a1a1a; font-size: 14px; }
    .text-secondary { color: #666 !important; font-size: 13px; }
    .status-badge { padding: 6px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; display: inline-block; }
    .status-badge.success { background: #E8F5E9; color: #2E7D32; }
    .status-badge.warning { background: #FFF3CD; color: #856404; }
    .status-badge.info { background: #E3F2FD; color: #1565C0; }
    .status-badge.neutral { background: #F3F4F6; color: #374151; }
    .status-badge.danger { background: #FFEBEE; color: #C62828; }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('filterToggle');
        const dropdown = document.getElementById('filterDropdown');
        const form = document.getElementById('filterForm');
        const statusInput = document.getElementById('statusInput');
        // عناصر فلترة قائمة الملغيات
        const cToggleBtn = document.getElementById('cFilterToggle');
        const cDropdown = document.getElementById('cFilterDropdown');
        const cForm = document.getElementById('cFilterForm');
        const cbyInput = document.getElementById('cbyInput');
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
        if (cToggleBtn && cDropdown) {
            cToggleBtn.addEventListener('click', function (e) {
                e.preventDefault();
                cDropdown.classList.toggle('show');
            });
            document.addEventListener('click', function (e) {
                if (!cDropdown.contains(e.target) && !cToggleBtn.contains(e.target)) {
                    cDropdown.classList.remove('show');
                }
            });
        }
        if (form && statusInput) {
            document.querySelectorAll('.instant-status-item').forEach(function (item) {
                item.addEventListener('click', function () {
                    const val = this.getAttribute('data-value') || '';
                    statusInput.value = (statusInput.value === val) ? '' : val;
                    const pageInput = document.getElementById('pageInput');
                    if (pageInput) { pageInput.value = '1'; }
                    form.submit();
                });
            });
        }
        if (cForm && cbyInput) {
            document.querySelectorAll('.instant-cby-item').forEach(function (item) {
                item.addEventListener('click', function () {
                    const val = this.getAttribute('data-value') || 'all';
                    cbyInput.value = (cbyInput.value === val) ? 'all' : val;
                    const cpageInput = document.getElementById('cpageInput');
                    if (cpageInput) { cpageInput.value = '1'; }
                    cForm.submit();
                });
            });
        }
    });
</script>
@endpush

@section('content')
    @php
        use App\Models\Booking;
        use Carbon\Carbon;

        $q = trim((string) request('q', ''));
        $statusFilter = (string) request('status', '');

        $bookingsQuery = Booking::with(['user', 'property.user'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->whereHas('user', function ($uq) use ($q) {
                        $uq->where('name', 'like', "%{$q}%")
                           ->orWhere('email', 'like', "%{$q}%");
                    })->orWhereHas('property', function ($pq) use ($q) {
                        $pq->where('title', 'like', "%{$q}%")
                           ->orWhere('city', 'like', "%{$q}%");
                    });
                });
            })
            ->when($statusFilter !== '' && $statusFilter !== 'all', function ($query) use ($statusFilter) {
                $query->where('status', $statusFilter);
            })
            ->orderByDesc('start_date');

        // ضبط الصفحة المطلوبة ضمن النطاق الصحيح لتفادي الصفحات الفارغة
        $perPage = 10;
        $totalMain = (clone $bookingsQuery)->count();
        $requestedPage = max(1, (int) request('page', 1));
        $lastMainPage = max(1, (int) ceil($totalMain / $perPage));
        if ($requestedPage > $lastMainPage) { $requestedPage = $lastMainPage; }

        $bookings = (clone $bookingsQuery)
            ->paginate($perPage, ['*'], 'page', $requestedPage)
            ->withQueryString();

        // استعلام منفصل للحجوزات الملغاة يدعم بحث cq وفلتر cby
        $cq = trim((string) request('cq', ''));
        $cbyFilter = (string) request('cby', 'all');

        $canceledQuery = Booking::with(['user', 'property.user'])
            ->where('status', 'canceled')
            ->when($cq !== '', function ($query) use ($cq) {
                $query->where(function ($qq) use ($cq) {
                    $qq->whereHas('user', function ($uq) use ($cq) {
                        $uq->where('name', 'like', "%{$cq}%")
                           ->orWhere('email', 'like', "%{$cq}%");
                    })->orWhereHas('property', function ($pq) use ($cq) {
                        $pq->where('title', 'like', "%{$cq}%")
                           ->orWhere('city', 'like', "%{$cq}%");
                    });
                });
            })
            ->when($cbyFilter !== '' && $cbyFilter !== 'all', function ($query) use ($cbyFilter) {
                $query->where('canceled_by', $cbyFilter);
            })
            ->orderByDesc('canceled_at')
            ->orderByDesc('updated_at');

        // ترقيم منفصل للحجوزات الملغاة مع ضبط cpage بناءً على الاستعلام الجديد
        $cTotal = (clone $canceledQuery)->count();
        $requestedCPage = max(1, (int) request('cpage', 1));
        $lastCPage = max(1, (int) ceil($cTotal / $perPage));
        if ($requestedCPage > $lastCPage) { $requestedCPage = $lastCPage; }

        $canceledBookings = (clone $canceledQuery)
            ->paginate($perPage, ['*'], 'cpage', $requestedCPage)
            ->withQueryString();

        $summary = [
            ['title' => 'إجمالي قيمة الحجوزات', 'value' => number_format(71897), 'change' => '+122', 'icon' => 'fa-clipboard-check'],
            ['title' => 'إجمالي الدخل', 'value' => number_format(71897), 'change' => '+122', 'icon' => 'fa-wallet'],
            ['title' => 'حجوزات مؤكدة', 'value' => number_format(71897), 'change' => '+122', 'icon' => 'fa-check-circle'],
            ['title' => 'حجوزات نشطة', 'value' => number_format(71897), 'change' => '+122', 'icon' => 'fa-bolt'],
            ['title' => 'حجوزات ملغاة', 'value' => number_format(150), 'change' => '-12', 'icon' => 'fa-ban'],
        ];

        // أرقام المتركس الفعلية
        $totalBookings = Booking::count();
        $confirmedBookings = Booking::where('status','confirmed')->count();
        $pendingBookings = Booking::where('status','pending')->count();
        $canceledCount = Booking::where('status','canceled')->count();

        $statusMap = function ($status) {
            switch ($status) {
                case 'confirmed':
                    return ['label' => 'تم التاكيد', 'class' => 'status-badge success'];
                case 'pending':
                    return ['label' => 'قيد الانتظار', 'class' => 'status-badge warning'];
                case 'paid':
                    return ['label' => 'مدفوع', 'class' => 'status-badge info'];
                case 'completed':
                    return ['label' => 'منتهي', 'class' => 'status-badge success'];
                case 'canceled':
                    return ['label' => 'ملغاة', 'class' => 'status-badge danger'];
                default:
                    return ['label' => 'غير معروفة', 'class' => 'status-badge neutral'];
            }
        };
    @endphp

    <div class="container-fluid px-4 py-4">
        <!-- بطاقات المتركس (مطابقة صفحة الخصائص) -->
        <div class="row g-4 mb-4 metrics-row">
            <div class="col-xl-3 col-md-6">
                <div class="metric-card">
                    <div class="metric-top">
                        <div class="metric-left">
                            <div class="metric-icon"><i class="fas fa-clipboard-list"></i></div>
                            <div>
                                <div class="metric-title">إجمالي الحجوزات</div>
                                <div class="metric-value">{{ number_format($totalBookings) }}</div>
                            </div>
                        </div>
                    </div>
                    <a class="metric-btn" href="{{ route('dashboard.orders.latest', ['status' => 'all']) }}">عرض الكل</a>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="metric-card">
                    <div class="metric-top">
                        <div class="metric-left">
                            <div class="metric-icon"><i class="fas fa-check-circle"></i></div>
                            <div>
                                <div class="metric-title">الحجوزات المؤكدة</div>
                                <div class="metric-value">{{ number_format($confirmedBookings) }}</div>
                            </div>
                        </div>
                    </div>
                    <a class="metric-btn" href="{{ route('dashboard.orders.latest', ['status' => 'confirmed']) }}">عرض الكل</a>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="metric-card">
                    <div class="metric-top">
                        <div class="metric-left">
                            <div class="metric-icon"><i class="fas fa-hourglass-half"></i></div>
                            <div>
                                <div class="metric-title">الحجوزات المعلقة</div>
                                <div class="metric-value">{{ number_format($pendingBookings) }}</div>
                            </div>
                        </div>
                    </div>
                    <a class="metric-btn" href="{{ route('dashboard.orders.latest', ['status' => 'pending']) }}">عرض الكل</a>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="metric-card">
                    <div class="metric-top">
                        <div class="metric-left">
                            <div class="metric-icon"><i class="fas fa-times-circle"></i></div>
                            <div>
                                <div class="metric-title">الحجوزات الملغية</div>
                                <div class="metric-value">{{ number_format($canceledCount) }}</div>
                            </div>
                        </div>
                    </div>
                    <a class="metric-btn" href="{{ route('dashboard.orders.latest', ['status' => 'canceled']) }}">عرض الكل</a>
                </div>
            </div>
        </div>

        <!-- قائمة الحجوزات -->
        <div class="properties-box mb-4">
            <div class="properties-header">
                <div class="searchbar">
            <form method="GET" action="{{ route('dashboard.orders.latest') }}" class="d-flex align-items-center gap-2">
                        <input type="text" name="q" class="search-input" value="{{ request('q') }}" placeholder="ابحث عن اسم مستخدم أو بريد إلكتروني">
                    </form>
                    <button class="filter-btn" id="filterToggle"><i class="fas fa-filter ms-1" style="color: rgba(63,149,253,1)"></i> تصفية</button>
                    <div class="filter-dropdown" id="filterDropdown">
            <form method="GET" action="{{ route('dashboard.orders.latest') }}" id="filterForm">
                            <input type="hidden" name="q" value="{{ request('q') }}">
                            <input type="hidden" name="status" id="statusInput" value="{{ request('status') }}">
                            <input type="hidden" name="page" id="pageInput" value="1">
                            @php $currentStatus = (string) request()->input('status', ''); $statuses = ['confirmed' => 'تم التاكيد','pending' => 'قيد الانتظار','paid' => 'مدفوع','completed'=>'منتهي']; @endphp
                            @foreach($statuses as $val => $label)
                                @php $isActive = $currentStatus === (string) $val; @endphp
                                <div class="filter-item instant-status-item {{ $isActive ? 'active' : '' }}" data-value="{{ $val }}">
                                    <span>{{ $label }}</span>
                                    <span class="icon">@if($isActive)<i class="fas fa-check"></i>@endif</span>
                                </div>
                            @endforeach
                        </form>
                    </div>
                </div>
                <div class="properties-title">قائمة الحجوزات
                    @if(!empty($currentStatus))
                        <span class="active-filter-chip">
                            @switch($currentStatus)
                                @case('confirmed') تم التاكيد @break
                                @case('pending') قيد الانتظار @break
                                @case('paid') مدفوع @break
                                @case('completed') منتهي @break
                            @endswitch
                        </span>
                    @endif
                </div>
            </div>

            <div class="table-responsive table-area">
                <table class="table table-hover align-middle properties-table">
                    <thead>
                        <tr>
                            <th>اسم المؤجر</th>
                            <th>اسم المستأجر</th>
                            <th>العقار</th>
                            <th>تاريخ الحجز</th>
                            <th>الحالة</th>
                            <th class="text-center">عمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                            @php
                                $tenant = $booking->user;
                                $property = $booking->property;
                                $landlord = $property?->user;
                                $status = $statusMap($booking->status ?? 'pending');
                            @endphp
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar-wrapper">
                                            @php $avatar = $landlord?->avatar; @endphp
                                            @if($avatar)
                                                <img class="user-avatar" src="{{ asset($avatar) }}" alt="avatar">
                                            @else
                                                <div class="user-avatar-placeholder"><i class="fas fa-user"></i></div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="user-name">{{ $landlord?->name ?? 'اسم المؤجر' }}</div>
                                            <div class="text-secondary">{{ $landlord?->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar-wrapper">
                                            @php $tavatar = $tenant?->avatar; @endphp
                                            @if($tavatar)
                                                <img class="user-avatar" src="{{ asset($tavatar) }}" alt="avatar">
                                            @else
                                                <div class="user-avatar-placeholder"><i class="fas fa-user"></i></div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="user-name">{{ $tenant?->name ?? 'اسم المستأجر' }}</div>
                                            <div class="text-secondary">{{ $tenant?->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="property-cell">
                                        <div class="property-name">{{ $property?->title ?? 'اسم العقار' }}</div>
                                        <div class="text-secondary">{{ $property?->city }} {{ $property?->address ? ' - '.$property->address : '' }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ Carbon::parse($booking->start_date)->format('Y/m/d') }} - {{ Carbon::parse($booking->end_date)->format('Y/m/d') }}</div>
                                    <div class="text-secondary">{{ Carbon::parse($booking->created_at)->diffForHumans() }}</div>
                                </td>
                                <td>
                                    <span class="{{ $status['class'] }}">{{ $status['label'] }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="#" class="action-link open-booking-modal" data-modal="bookingModal_{{ $booking->id }}">تفاصيل</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-state-row">
                                    <div class="empty-state">
                                        <i class="far fa-calendar-times"></i>
                                        <p>لا توجد حجوزات حالية مطابقة للبحث.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($bookings->hasPages())
                <div class="table-footer">
                    <div class="pagination-info">
                        عرض {{ $bookings->firstItem() }} إلى {{ $bookings->lastItem() }} من {{ $bookings->total() }}
                    </div>
                    <div class="pagination-wrapper rento-pagination">
                        {{ $bookings->onEachSide(1)->links('vendor.pagination.rento') }}
                    </div>
                </div>
            @endif
        </div>

        {{-- Modals for main bookings --}}
        @foreach($bookings as $bk)
            @php
                $tenant = $bk->user;
                $property = $bk->property;
                $landlord = $property?->user;
                $chipLabel = match($bk->status){
                    'confirmed' => 'تم التأكيد',
                    'pending' => 'قيد الانتظار',
                    'paid' => 'مدفوعة',
                    'review' => 'معاينة',
                    'canceled' => 'ملغاة',
                    'completed' => 'منتهية',
                    default => '—'
                };
                $chipClass = match($bk->status){
                    'confirmed' => 'chip-confirmed',
                    'pending' => 'chip-pending',
                    'paid' => 'chip-paid',
                    'review' => 'chip-review',
                    'canceled' => 'chip-canceled',
                    'completed' => 'chip-confirmed',
                    default => 'chip-review'
                };
            @endphp
            <div class="booking-modal-backdrop" id="bookingModal_{{ $bk->id }}" aria-hidden="true">
                <div class="booking-modal-card" role="dialog" aria-labelledby="bookingTitle_{{ $bk->id }}" aria-modal="true">
                    <div class="booking-modal-header">
                        <button type="button" class="booking-modal-close" data-close="#bookingModal_{{ $bk->id }}" aria-label="إغلاق">×</button>
                        <div class="booking-modal-title" id="bookingTitle_{{ $bk->id }}">تفاصيل الحجز</div>
                    </div>
                    <div class="booking-modal-body">
                        <table class="details-table">
                            <thead>
                                <tr>
                                    <th>اسم المؤجر</th>
                                    <th>اسم المستأجر</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <div class="user-avatar-wrapper">
                                                @php $av = $landlord?->avatar; @endphp
                                                @if($av)
                                                    <img class="user-avatar" src="{{ asset($av) }}" alt="avatar">
                                                @else
                                                    <div class="user-avatar-placeholder"><i class="fas fa-user"></i></div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="user-name">{{ $landlord?->name ?? 'اسم المؤجر' }}</div>
                                                <div class="text-secondary">{{ $landlord?->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="user-cell">
                                            <div class="user-avatar-wrapper">
                                                @php $tav = $tenant?->avatar; @endphp
                                                @if($tav)
                                                    <img class="user-avatar" src="{{ asset($tav) }}" alt="avatar">
                                                @else
                                                    <div class="user-avatar-placeholder"><i class="fas fa-user"></i></div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="user-name">{{ $tenant?->name ?? 'اسم المستخدم' }}</div>
                                                <div class="text-secondary">{{ $tenant?->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-chip {{ $chipClass }}">{{ $chipLabel }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <table class="details-table mt-2">
                            <thead>
                                <tr>
                                    <th>بداية الحجز</th>
                                    <th>نهاية الحجز</th>
                                    <th>قيمة الحجز</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($bk->start_date)->format('d / m / Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($bk->end_date)->format('d / m / Y') }}</td>
                                    <td>{{ number_format($property?->price ?? 0) }} د.ل</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="property-preview-card">
                            <div class="property-preview-left">
                                <div class="rating-pill"><i class="fas fa-star"></i> {{ $property?->rating ? number_format($property->rating, 1) : '—' }}</div>
                                <div class="price-pill"><i class="far fa-calendar"></i> {{ number_format($property?->price ?? 0) }} د.ل / اليوم</div>
                            </div>
                            <div class="property-preview-center">
                                <a href="{{ $property ? route('dashboard.properties.preview', $property) : '#' }}" class="action-link">{{ $property?->title ?? 'اسم العقار' }}</a>
                                <div class="text-secondary">{{ $property?->city }} {{ $property?->area ? ' - '.$property->area : '' }}</div>
                            </div>
                            <div class="property-preview-image">
                                @php $pimg = $property?->image; @endphp
                                @if($pimg)
                                    <img src="{{ asset($pimg) }}" alt="property" />
                                @else
                                    <div class="img-placeholder"><i class="far fa-image"></i></div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="booking-modal-actions">
                        <button type="button" class="modal-close-btn" data-close="#bookingModal_{{ $bk->id }}">إغلاق</button>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- قائمة الحجوزات الملغية -->
        <div class="properties-box">
            <div class="properties-header">
                <div class="searchbar">
            <form method="GET" action="{{ route('dashboard.orders.latest') }}" class="d-flex align-items-center gap-2">
                        <input type="text" name="cq" class="search-input" value="{{ request('cq') }}" placeholder="ابحث عن اسم مستخدم أو بريد إلكتروني">
                    </form>
                    <button class="filter-btn" id="cFilterToggle"><i class="fas fa-filter ms-1" style="color: rgba(63,149,253,1)"></i> تصفية</button>
                    <div class="filter-dropdown" id="cFilterDropdown">
            <form method="GET" action="{{ route('dashboard.orders.latest') }}" id="cFilterForm">
                            <input type="hidden" name="cq" value="{{ request('cq') }}">
                            <input type="hidden" name="cby" id="cbyInput" value="{{ request('cby', 'all') }}">
                            <input type="hidden" name="cpage" id="cpageInput" value="1">
                            @php $currentCby = (string) request()->input('cby', 'all'); $cbyOptions = ['all' => 'الكل','renter' => 'المستأجر','owner' => 'المؤجر']; @endphp
                            @foreach($cbyOptions as $val => $label)
                                @php $isActive = $currentCby === (string) $val; @endphp
                                <div class="filter-item instant-cby-item {{ $isActive ? 'active' : '' }}" data-value="{{ $val }}">
                                    <span>{{ $label }}</span>
                                    <span class="icon">@if($isActive)<i class="fas fa-check"></i>@endif</span>
                                </div>
                            @endforeach
                        </form>
                    </div>
                </div>
                <div class="properties-title">قائمة الحجوزات الملغية</div>
            </div>

            <div class="table-responsive table-area">
                <table class="table table-hover align-middle properties-table">
                    <thead>
                        <tr>
                            <th>اسم المؤجر</th>
                            <th>اسم المستأجر</th>
                            <th>العقار</th>
                            <th>تاريخ الحجز</th>
                            <th>تاريخ الإلغاء</th>
                            <th>إلغاء من قبل</th>
                            <th class="text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($canceledBookings as $booking)
                            @php
                                $tenant = $booking->user;
                                $property = $booking->property;
                                $landlord = $property?->user;
                                $status = $statusMap('canceled');
                            @endphp
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar-wrapper">
                                            @php $avatar = $landlord?->avatar; @endphp
                                            @if($avatar)
                                                <img class="user-avatar" src="{{ asset($avatar) }}" alt="avatar">
                                            @else
                                                <div class="user-avatar-placeholder"><i class="fas fa-user"></i></div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="user-name">{{ $landlord?->name ?? 'اسم المؤجر' }}</div>
                                            <div class="text-secondary">{{ $landlord?->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar-wrapper">
                                            @php $tavatar = $tenant?->avatar; @endphp
                                            @if($tavatar)
                                                <img class="user-avatar" src="{{ asset($tavatar) }}" alt="avatar">
                                            @else
                                                <div class="user-avatar-placeholder"><i class="fas fa-user"></i></div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="user-name">{{ $tenant?->name ?? 'اسم المستخدم' }}</div>
                                            <div class="text-secondary">{{ $tenant?->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="property-cell">
                                        <div class="property-name">{{ $property?->title ?? 'اسم العقار' }}</div>
                                        <div class="text-secondary">{{ $property?->city }} {{ $property?->address ? ' - '.$property->address : '' }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $booking->canceled_at ? Carbon::parse($booking->canceled_at)->format('Y/m/d') : '—' }}</div>
                                    @if(!empty($booking->cancel_reason))
                                        <div class="text-secondary">{{ $booking->cancel_reason }}</div>
                                    @endif
                                </td>
                                <td>
                                    @php $who = $booking->canceled_by === 'renter' ? 'المستأجر' : ($booking->canceled_by === 'owner' ? 'المؤجر' : '—'); @endphp
                                    <span class="status-badge neutral">{{ $who }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="#" class="action-link open-booking-modal" data-modal="bookingModal_{{ $booking->id }}">تفاصيل</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-state-row">
                                    <div class="empty-state">
                                        <i class="far fa-calendar-times"></i>
                                        <p>لا توجد حجوزات ملغاة حالياً.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($canceledBookings->hasPages())
                <div class="table-footer">
                    <div class="pagination-info">
                        عرض {{ $canceledBookings->firstItem() }} إلى {{ $canceledBookings->lastItem() }} من {{ $canceledBookings->total() }}
                    </div>
                    <div class="pagination-wrapper rento-pagination">
                        {{ $canceledBookings->onEachSide(1)->links('vendor.pagination.rento') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Modals for canceled bookings --}}
    @foreach($canceledBookings as $bk)
        @php
            $tenant = $bk->user;
            $property = $bk->property;
            $landlord = $property?->user;
            $chipLabel = 'ملغاة';
            $chipClass = 'chip-canceled';
        @endphp
        <div class="booking-modal-backdrop" id="bookingModal_{{ $bk->id }}" aria-hidden="true">
            <div class="booking-modal-card" role="dialog" aria-labelledby="bookingTitle_{{ $bk->id }}" aria-modal="true">
                <div class="booking-modal-header">
                    <button type="button" class="booking-modal-close" data-close="#bookingModal_{{ $bk->id }}" aria-label="إغلاق">×</button>
                    <div class="booking-modal-title" id="bookingTitle_{{ $bk->id }}">تفاصيل الحجز</div>
                </div>
                <div class="booking-modal-body">
                    <table class="details-table">
                        <thead>
                            <tr>
                                <th>اسم المؤجر</th>
                                <th>اسم المستأجر</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar-wrapper">
                                            @php $av = $landlord?->avatar; @endphp
                                            @if($av)
                                                <img class="user-avatar" src="{{ asset($av) }}" alt="avatar">
                                            @else
                                                <div class="user-avatar-placeholder"><i class="fas fa-user"></i></div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="user-name">{{ $landlord?->name ?? 'اسم المؤجر' }}</div>
                                            <div class="text-secondary">{{ $landlord?->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar-wrapper">
                                            @php $tav = $tenant?->avatar; @endphp
                                            @if($tav)
                                                <img class="user-avatar" src="{{ asset($tav) }}" alt="avatar">
                                            @else
                                                <div class="user-avatar-placeholder"><i class="fas fa-user"></i></div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="user-name">{{ $tenant?->name ?? 'اسم المستخدم' }}</div>
                                            <div class="text-secondary">{{ $tenant?->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-chip {{ $chipClass }}">{{ $chipLabel }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <table class="details-table mt-2">
                        <thead>
                            <tr>
                                <th>بداية الحجز</th>
                                <th>نهاية الحجز</th>
                                <th>قيمة الحجز</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($bk->start_date)->format('d / m / Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($bk->end_date)->format('d / m / Y') }}</td>
                                <td>{{ number_format($property?->price ?? 0) }} د.ل</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="property-preview-card">
                        <div class="property-preview-left">
                            <div class="rating-pill"><i class="fas fa-star"></i> {{ $property?->rating ? number_format($property->rating, 1) : '—' }}</div>
                            <div class="price-pill"><i class="far fa-calendar"></i> {{ number_format($property?->price ?? 0) }} د.ل / اليوم</div>
                        </div>
                        <div class="property-preview-center">
                            <a href="{{ $property ? route('dashboard.properties.preview', $property) : '#' }}" class="action-link">{{ $property?->title ?? 'اسم العقار' }}</a>
                            <div class="text-secondary">{{ $property?->city }} {{ $property?->area ? ' - '.$property->area : '' }}</div>
                        </div>
                        <div class="property-preview-image">
                            @php $pimg = $property?->image; @endphp
                            @if($pimg)
                                <img src="{{ asset($pimg) }}" alt="property" />
                            @else
                                <div class="img-placeholder"><i class="far fa-image"></i></div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="booking-modal-actions">
                    <button type="button" class="modal-close-btn" data-close="#bookingModal_{{ $bk->id }}">إغلاق</button>
                </div>
            </div>
        </div>
    @endforeach

    <style>
        /* Booking Modal (مطابق للصورة) */
        .booking-modal-backdrop { position: fixed; inset: 0; background: rgba(17,24,39,0.55); display: none; align-items: center; justify-content: center; z-index: 2001; }
        .booking-modal-backdrop[aria-hidden="false"] { display: flex; }
        .booking-modal-card { width: 560px; max-width: 92vw; background: #fff; border: 1px solid #E5E7EB; border-radius: 12px; box-shadow: 0 12px 24px rgba(0,0,0,0.12); overflow: hidden; direction: rtl; }
        .booking-modal-header { background: #E9F3FF; padding: 12px 14px; display: flex; align-items: center; justify-content: space-between; }
        .booking-modal-title { font-size: 18px; font-weight: 700; color: #1F2937; text-align: center; margin: 0 auto; }
        .booking-modal-close { background: #fff; color: #374151; border: 1px solid #E5E7EB; border-radius: 8px; width: 30px; height: 30px; line-height: 28px; text-align: center; font-size: 16px; cursor: pointer; }
        .booking-modal-body { padding: 12px; }

        .details-table { width: 100%; border-collapse: separate; border-spacing: 0; background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; overflow: hidden; }
        .details-table thead th { background: #E9F3FF; color: #1F2937; font-weight: 600; padding: 10px 12px; border: none; text-align: right; }
        .details-table tbody td { padding: 12px; color: #374151; border-top: 1px solid #F3F4F6; text-align: right; }

        .status-chip { display: inline-block; padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; }
        .chip-confirmed { color: #7c3aed; background: #f5f3ff; border: 1px solid #c4b5fd; }
        .chip-pending { color: #856404; background: #FFF7E6; border: 1px solid #FFE8B3; }
        .chip-paid { color: #1565C0; background: #E3F2FD; border: 1px solid #BBDEFB; }
        .chip-review { color: #374151; background: #F3F4F6; border: 1px solid #E5E7EB; }
        .chip-canceled { color: #C62828; background: #FFEBEE; border: 1px solid #FFCDD2; }

        .property-preview-card { display: flex; align-items: center; gap: 12px; padding: 12px; border: 1px solid #E5E7EB; border-radius: 10px; background: #fff; margin-top: 10px; }
        .property-preview-left { display: flex; align-items: center; gap: 8px; }
        .rating-pill, .price-pill { display: inline-flex; align-items: center; gap: 6px; padding: 6px 10px; border-radius: 8px; font-size: 12px; color: #374151; background: #F9FAFB; border: 1px solid #E5E7EB; }
        .rating-pill i { color: #f59e0b; }
        .property-preview-center { flex: 1; }
        .property-preview-center .action-link { font-weight: 600; }
        .property-preview-image img, .property-preview-image .img-placeholder { width: 72px; height: 48px; border-radius: 8px; object-fit: cover; border: 1px solid #E5E7EB; background: #F3F4F6; display: block; }

        .booking-modal-actions { padding: 12px; border-top: 1px solid #F3F4F6; display: flex; justify-content: flex-end; }
        .modal-close-btn { background: #1F2937; color: #fff; border: none; border-radius: 8px; padding: 8px 14px; font-size: 13px; cursor: pointer; }

        /* Modern Card reuse + tweaks */
        .modern-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); overflow: hidden; border: none; }
        .card-header-custom { padding: 20px 24px; background: #fff; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
        .table-title { font-size: 18px; font-weight: 600; color: #1a1a1a; margin: 0; }
        .header-actions { display: flex; align-items: center; gap: 12px; }
        .btn-filter { display: flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 8px; border: 1px solid #E0E0E0; background: white; font-size: 14px; color: #666; cursor: pointer; }
        .btn-filter:hover { background: #f5f5f5; border-color: #ccc; }
        .search-box-inline { position: relative; display: flex; align-items: center; }
        .search-box-inline input { padding: 8px 40px 8px 16px; border: 1px solid #E0E0E0; border-radius: 8px; font-size: 14px; width: 300px; transition: all 0.3s; }
        .search-box-inline input:focus { outline: none; border-color: #2B7FE6; box-shadow: 0 0 0 3px rgba(43,127,230,0.1); }
        .search-box-inline i { position: absolute; left: 14px; color: #999; font-size: 14px; }

        /* Chips in filter panel */
        .chip { border: 1px solid #E5E7EB; background: #fff; border-radius: 8px; padding: 6px 10px; font-size: 12px; color: #1F2937; text-decoration: none; display: inline-block; }
        .chip.active { background: #E9F3FF; border-color: #CDE3FF; }

        /* Orders Table */
        .orders-table thead { background: #F9FAFB; }
        .orders-table thead th { padding: 14px 20px; font-size: 13px; font-weight: 600; color: #666; text-align: right; border: none; }
        .orders-table tbody td { padding: 16px 20px; vertical-align: middle; border-bottom: 1px solid #f5f5f5; text-align: right; }
        .orders-table tbody tr:hover { background: #fafafa; }

        .user-cell { display: flex; align-items: center; gap: 12px; }
        .user-avatar-wrapper { flex-shrink: 0; }
        .user-avatar { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; }
        .user-avatar-placeholder { width: 32px; height: 32px; border-radius: 50%; background: #E5E7EB; color: #6B7280; display: flex; align-items: center; justify-content: center; }
        .user-name { font-weight: 500; color: #1a1a1a; font-size: 14px; }
        .text-secondary { color: #666 !important; font-size: 13px; }
        .property-name { font-weight: 500; color: #1a1a1a; }

        /* Status badges */
        .status-badge { padding: 6px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; display: inline-block; }
        .status-badge.success { background: #E8F5E9; color: #2E7D32; }
        .status-badge.warning { background: #FFF3CD; color: #856404; }
        .status-badge.info { background: #E3F2FD; color: #1565C0; }
        .status-badge.neutral { background: #F3F4F6; color: #374151; }
        .status-badge.danger { background: #FFEBEE; color: #C62828; }

        .action-link { color: #2B7FE6; font-weight: 600; text-decoration: none; }

        /* Empty State */
        .empty-state-row { padding: 60px 20px !important; }
        .empty-state { text-align: center; }
        .empty-state i { font-size: 48px; color: #ddd; margin-bottom: 12px; }
        .empty-state p { color: #999; font-size: 15px; margin: 0; }

        /* Footer / Pagination */
        .table-footer { padding: 16px 24px; background: #F9FAFB; border-top: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; }
        .pagination-info { font-size: 14px; color: #555; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 14px; display: inline-block; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-top: 10px; }

        @media (max-width: 768px) {
            .card-header-custom { flex-direction: column; align-items: flex-start; }
            .header-actions { width: 100%; flex-direction: column; }
            .search-box-inline input { width: 100%; }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const btn = document.getElementById('ordersFilterBtn');
            const panel = document.getElementById('ordersFilterPanel');
            if(btn && panel){
                btn.addEventListener('click', function(e){
                    e.preventDefault();
                    panel.classList.toggle('show');
                });
                document.addEventListener('click', function(e){
                    if(!panel.contains(e.target) && !btn.contains(e.target)){
                        panel.classList.remove('show');
                    }
                });
            }

            // Open booking modals
            document.querySelectorAll('.open-booking-modal').forEach(function(btn){
                btn.addEventListener('click', function(e){
                    e.preventDefault();
                    const id = btn.getAttribute('data-modal');
                    const modal = document.getElementById(id);
                    if(modal){ modal.setAttribute('aria-hidden','false'); }
                });
            });

            // Close modal handlers
            document.querySelectorAll('.booking-modal-backdrop').forEach(function(backdrop){
                backdrop.addEventListener('click', function(e){ if(e.target === backdrop){ backdrop.setAttribute('aria-hidden','true'); }});
            });
            document.querySelectorAll('.booking-modal-close, .modal-close-btn').forEach(function(btn){
                btn.addEventListener('click', function(e){
                    e.preventDefault();
                    const target = btn.getAttribute('data-close');
                    const modal = target ? document.querySelector(target) : btn.closest('.booking-modal-backdrop');
                    if(modal){ modal.setAttribute('aria-hidden','true'); }
                });
            });
        });
    </script>
@endsection
