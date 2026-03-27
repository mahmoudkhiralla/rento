@extends('dashboard.layouts.app')

@section('title', 'المحافظ و العمليات')

@section('content')
    <div class="transactions-container">
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h4 class="stat-title">إجمالي رصيد المحفظة</h4>
                <p class="stat-value">{{ number_format($stats['wallet_total_balance']) }}</p>
            </div>
            <div class="stat-card">
                <h4 class="stat-title">إجمالي العمولات</h4>
                <p class="stat-value">{{ number_format($stats['total_commissions']) }}</p>
            </div>
            <div class="stat-card">
                <h4 class="stat-title">إجمالي سحب الرصيد</h4>
                <p class="stat-value">{{ number_format($stats['total_refunds_amount']) }}</p>
            </div>
            <div class="stat-card">
                <h4 class="stat-title">طلبات سحب جديدة</h4>
                <p class="stat-value">{{ number_format($stats['pending_refunds_count']) }}</p>
            </div>
        </div>

        <!-- Transactions Table Section -->
        <div class="table-section">
            <div class="table-header">
                <h3 class="section-title">العمليات الأخيرة</h3>

                <div class="header-actions">
                    <div class="filter-dropdown">
                        <button class="filter-btn" onclick="toggleFilterDropdown()">
                            <i class="fas fa-sliders-h"></i>
                            تصفية
                        </button>
                        <div class="dropdown-menu" id="filterDropdown">
                            <a href="#" class="dropdown-item" onclick="applyTransactionsFilter('status','all')">كل الحالات</a>
                            <a href="#" class="dropdown-item" onclick="applyTransactionsFilter('status','pending')">قيد الانتظار</a>
                            <a href="#" class="dropdown-item" onclick="applyTransactionsFilter('status','completed')">تم الاكتمال</a>
                            <a href="#" class="dropdown-item" onclick="applyTransactionsFilter('status','failed')">فشل التحويل</a>
                            <a href="#" class="dropdown-item" onclick="applyTransactionsFilter('status','cancelled')">ملغي</a>
                            <div class="dropdown-item" style="font-weight:600; cursor:default;">— نوع العملية —</div>
                            <a href="#" class="dropdown-item" onclick="applyTransactionsFilter('type','all')">الكل</a>
                            <a href="#" class="dropdown-item" onclick="applyTransactionsFilter('type','payment')">Payment Received من عميل</a>
                            <a href="#" class="dropdown-item" onclick="applyTransactionsFilter('type','commission')">Commission Added عمولة</a>
                            <a href="#" class="dropdown-item" onclick="applyTransactionsFilter('type','penalty')">Fine Collected غرامة من مالك</a>
                            <a href="#" class="dropdown-item" onclick="applyTransactionsFilter('type','refund')">Refund to Client تعويض عميل</a>
                            <a href="#" class="dropdown-item" onclick="applyTransactionsFilter('type','withdraw')">Withdrawal سحب بنكي</a>
                            <a href="#" class="dropdown-item" onclick="applyTransactionsFilter('type','deposit')">Wallet Recharge شحن محفظة</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>اسم المستخدم</th>
                            <th>تاريخ العملية</th>
                            <th>المبلغ</th>
                            <th>الحالة</th>
                            <th>نوع العملية</th>
                            <th>التفاصيل</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $index => $transaction)
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        @php
                                            $user = $transaction->user;
                                            $avatar = $user->avatar ?? null;
                                            $avatarUrl = $avatar ? (\Illuminate\Support\Str::startsWith($avatar, ['http://', 'https://']) ? $avatar : asset('storage/' . $avatar)) : ('https://ui-avatars.com/api/?name=' . urlencode($user->name ?? 'User') . '&background=3B82F6&color=fff');
                                        @endphp
                                        <img src="{{ $avatarUrl }}"
                                             alt="User" class="user-avatar-sm">
                                        <span>{{ $transaction->user->name ?? 'اسم المستخدم' }}</span>
                                    </div>
                                </td>
                                <td>{{ optional($transaction->created_at)->format('Y/m/d g:i A') }}</td>
                                <td class="amount-cell">{{ number_format($transaction->amount ?? 0) }} ج.م</td>
                                <td>
                                    @php
                                        $statusMap = [
                                            'pending' => ['text' => 'قيد الانتظار', 'class' => 'status-pending'],
                                            'completed' => ['text' => 'تم الاكتمال', 'class' => 'status-completed'],
                                            'failed' => ['text' => 'فشل التحويل', 'class' => 'status-failed'],
                                            'cancelled' => ['text' => 'ملغي', 'class' => 'status-cancelled'],
                                            'paid' => ['text' => 'تم الدفع', 'class' => 'status-completed'],
                                            'approved' => ['text' => 'تمت الموافقة', 'class' => 'status-completed'],
                                            'rejected' => ['text' => 'مرفوض', 'class' => 'status-failed'],
                                        ];
                                        $status = $statusMap[$transaction->status ?? 'pending'] ?? $statusMap['pending'];
                                    @endphp
                                    <span class="status-badge {{ $status['class'] }}">
                                        {{ $status['text'] }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $typeMap = [
                                            'payment' => 'Payment Received من عميل',
                                            'commission' => 'Commission Added عمولة',
                                            'penalty' => 'Fine Collected غرامة من مالك',
                                            'refund' => 'Refund to Client تعويض عميل',
                                            'withdraw' => 'Withdrawal سحب بنكي',
                                            'deposit' => 'Wallet Recharge شحن محفظة',
                                        ];
                                        $type = $typeMap[$transaction->type ?? 'payment'] ?? 'عملية مالية';
                                    @endphp
                                    {{ $type }}
                                </td>
                                <td>
                                    <button class="details-link" onclick="openTransactionModal('{{ $transaction->id }}')">
                                        معاينة
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">لا توجد عمليات حالياً</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($transactions->lastPage() > 1)
            <div class="pagination-wrapper">
                <span class="pagination-info">عرض {{ $transactions->count() ? $transactions->firstItem() : 0 }} إلى {{ $transactions->count() ? $transactions->lastItem() : 0 }} من {{ $transactions->total() }}</span>
                <div class="pagination">
                    @php
                        $current = $transactions->currentPage();
                        $last = $transactions->lastPage();
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
            @endif
        </div>
    </div>

    @include('dashboard.payments.partials.transactions-modal')
    @include('dashboard.payments.partials.transactions-styles')
    @include('dashboard.payments.partials.transactions-scripts')
@endsection
