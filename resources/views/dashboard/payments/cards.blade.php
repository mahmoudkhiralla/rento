@extends('dashboard.layouts.app')

@section('title', 'إدارة بطاقات الدفع')

@section('content')
    <div class="payment-cards-container">
        <!-- Header -->
        <div class="page-header">
            <div class="header-left">
                <h2 class="page-title">إدارة بطاقات الدفع</h2>
            </div>
            <button class="btn-primary" onclick="openIssueCardModal()">
                <i class="fas fa-plus"></i>
                إصدار بطاقات دفع جديدة
            </button>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h4 class="stat-title">إجمالي البطاقات المصدرة</h4>
                <p class="stat-value">{{ number_format($stats['total_cards']) }}</p>
            </div>
            <div class="stat-card">
                <h4 class="stat-title">البطاقات المنتهية</h4>
                <p class="stat-value">{{ number_format($stats['expired_cards']) }}</p>
            </div>
            <div class="stat-card">
                <h4 class="stat-title">البطاقات المباعة</h4>
                <p class="stat-value">{{ number_format($stats['sold_cards']) }}</p>
            </div>
            <div class="stat-card">
                <h4 class="stat-title">البطاقات المشحونة</h4>
                <p class="stat-value">{{ number_format($stats['pending_cards']) }}</p>
            </div>
        </div>

        <!-- Filters and Table Section -->
        <div class="table-section">
            <div class="table-header">
                <h3 class="section-title">آخر بطاقات الدفع المصدرة</h3>

                <div class="header-actions">
                    <!-- Search -->
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="ابحث عن اسم مستخدم او بريد إلكتروني" id="searchInput">
                    </div>
                    <!-- Filter Dropdown -->
                    <div class="filter-dropdown">
                        <div class="dropdown-menu" id="filterDropdown">
                            <a href="{{ route('dashboard.payments.cards') }}" class="dropdown-item">الكل</a>
                            <a href="{{ route('dashboard.payments.cards', ['status' => 'pending']) }}" class="dropdown-item">مشحون</a>
                            <a href="{{ route('dashboard.payments.cards', ['status' => 'active']) }}" class="dropdown-item">مصدر</a>
                            <a href="{{ route('dashboard.payments.cards', ['status' => 'expired']) }}" class="dropdown-item">منتهي</a>
                            <a href="{{ route('dashboard.payments.cards', ['status' => 'cancelled']) }}" class="dropdown-item">ملغي</a>
                            <a href="#" class="dropdown-item">معاينة</a>
                        </div>
                        <button class="filter-btn" onclick="toggleFilterDropdown()">
                            <i class="fas fa-filter"></i>
                            تصفية
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>رقم بطاقة الدفع</th>
                            <th>القيمة (د.ل)</th>
                            <th>تاريخ الإصدار</th>
                            <th>الحالة</th>
                            <th>تاريخ الاستخدام</th>
                            <th>معاينة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cards as $card)
                            @php
                                $statusClassMap = [
                                    'pending' => 'status-pending',
                                    'active' => 'status-issued',
                                    'expired' => 'status-expired',
                                    'cancelled' => 'status-cancelled-red',
                                ];
                                $statusTextMap = [
                                    'pending' => 'مشحون',
                                    'active' => 'مصدر',
                                    'expired' => 'منتهي',
                                    'cancelled' => 'ملغي',
                                ];
                                $statusClass = $statusClassMap[$card->status] ?? 'status-pending';
                                $statusText = $statusTextMap[$card->status] ?? 'مشحون';
                            @endphp
                            <tr>
                                <td>{{ $card->card_number }}</td>
                                <td>{{ number_format($card->amount) }}</td>
                                <td>{{ optional($card->issue_date)->format('d / m / Y') ?? '-' }}</td>
                                <td><span class="status-badge {{ $statusClass }}">{{ $statusText }}</span></td>
                                <td>{{ optional($card->expiry_date)->format('d / m / Y') ?? '-' }}</td>
                                <td><button class="card-link" onclick="openCardDetailsModal({{ $card->id }})">معاينة</button></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center">لا توجد بطاقات حالياً</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                <span class="pagination-info">عرض {{ $cards->firstItem() }} إلى {{ $cards->lastItem() }} من {{ $cards->total() }}</span>
                <div class="pagination">
                    @php
                        $current = $cards->currentPage();
                        $last = $cards->lastPage();
                        $makeUrl = function($page) {
                            return request()->fullUrlWithQuery(['page' => $page]);
                        };
                    @endphp
                    <a class="page-btn" href="{{ $current > 1 ? $makeUrl($current - 1) : '#' }}"><i class="fas fa-chevron-right"></i></a>
                    @for ($p = 1; $p <= $last; $p++)
                        <a class="page-btn {{ $p == $current ? 'active' : '' }}" href="{{ $makeUrl($p) }}">{{ $p }}</a>
                    @endfor
                    <a class="page-btn" href="{{ $current < $last ? $makeUrl($current + 1) : '#' }}"><i class="fas fa-chevron-left"></i></a>
                </div>
            </div>
        </div>
    </div>

    @include('dashboard.payments.partials.modals')
    @include('dashboard.payments.partials.styles')
    @include('dashboard.payments.partials.scripts')
@endsection
