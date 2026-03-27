@extends('dashboard.layouts.app')

@section('title', 'الدفع والتحصيل')

@section('content')
    <div class="payments-container">
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h4 class="stat-title">إجمالي البطاقات المصدرة</h4>
                <p class="stat-value">{{ $stats['total_cards'] ?? 0 }}</p>
            </div>
            <div class="stat-card">
                <h4 class="stat-title">مبلغ البطاقات المصدرة</h4>
                <p class="stat-value">{{ $stats['total_amount'] ?? 0 }}</p>
            </div>
            <div class="stat-card">
                <h4 class="stat-title">طلبات سحب الرصيد</h4>
                <p class="stat-value">{{ $stats['withdraw_requests'] ?? 0 }}</p>
            </div>
            <div class="stat-card">
                <h4 class="stat-title">عمليات الدفع الناجحة</h4>
                <p class="stat-value">{{ $stats['completed_payments'] ?? 0 }}</p>
            </div>
        </div>

        <!-- Mid Section: Updates on the right, Status on the left -->
        <div class="mid-section">
            <!-- Payment Cards Updates Section -->
            <div class="updates-section">
                <div class="section-header">
                    <h3 class="section-title">تحديثات بطاقات الدفع</h3>
                    <a href="{{ route('dashboard.payments.cards') }}" class="view-all-link">عرض الكل</a>
                </div>
                <div class="cards-table">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>رقم بطاقة الدفع</th>
                                <th>القيمة (د.ل)</th>
                                <th>تاريخ الإصدار</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cardsUpdates as $card)
                                <tr>
                                    <td>{{ $card->number }}</td>
                                    <td>{{ number_format($card->amount, 0) }}</td>
                                    <td>{{ optional($card->issue_date)->format('Y/m/d') ?? '-' }}</td>
                                    <td>
                                        <span class="badge status-{{ $card->status }}">{{ $card->status_ar }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align:center">لا توجد تحديثات حالياً</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Charts Section -->
        <div class="charts-section">
            <!-- Payment Cards States Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">حالات بطاقات الدفع</h3>
                    <div class="chart-actions">
                        <button class="date-range-btn" id="statesPeriodBtn">
                            <i class="fas fa-calendar-alt"></i>
                            تغيير الفترة
                        </button>
                        <!-- Modal for date range selection -->
                        <div class="modal-overlay" id="statesModal" style="display:none;">
                            <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="statesModalTitle">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="statesModalTitle">تغيير الفترة</h4>
                                    <button class="modal-close" id="statesModalClose" aria-label="إغلاق">×</button>
                                </div>
                                <div class="modal-body">
                                    <div class="modal-field">
                                        <label for="statesModalFrom">من</label>
                                        <input type="date" id="statesModalFrom" value="{{ $selectedStatesFrom ?? '' }}">
                                    </div>
                                    <div class="modal-field">
                                        <label for="statesModalTo">إلى</label>
                                        <input type="date" id="statesModalTo" value="{{ $selectedStatesTo ?? '' }}">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn-secondary" id="statesModalCancel">إلغاء</button>
                                    <button class="btn-primary" id="statesModalApply">تطبيق</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <canvas id="paymentStatesChart"></canvas>
                <div class="chart-legend">
                    <div class="legend-item">
                        <span class="legend-color" style="background: #10B981;"></span>
                        <span class="legend-label">بطاقات مباعة</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background: #3B82F6;"></span>
                        <span class="legend-label">بطاقات مشحونة</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background: #6B7280;"></span>
                        <span class="legend-label">بطاقات منتهية</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background: rgba(252, 179, 173, 1);"></span>
                        <span class="legend-label">بطاقات ملغية</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Recent Transactions Section -->
        <div class="transactions-section">
            <div class="section-header">
                <h3 class="section-title">تحديثات العمليات المالية</h3>
                <a href="{{ route('dashboard.payments.transactions') }}" class="view-all-link">عرض الكل</a>
            </div>

            <div class="transactions-table">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>اسم المستخدم</th>
                            <th>تاريخ العملية</th>
                            <th>المبلغ</th>
                            <th>الحالة</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTransactions as $index => $transaction)
                            <tr>
                                <td>
                                    <div class="user-info">
                                        <img src="{{ $transaction['user_avatar'] }}" alt="User" class="user-avatar">
                                        <div class="user-details">
                                            <span class="user-name">{{ $transaction['user_name'] }}</span>
                                            <span class="user-email">{{ $transaction['user_email'] }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{ $transaction['date_only'] ?? $transaction['date'] }}<br>
                                    <span class="time-text">{{ $transaction['time_only'] ?? '' }}</span>
                                </td>
                                <td>{{ $transaction['amount'] }}</td>
                                <td>
                                    @if($transaction['status'] === 'خصم')
                                        <span class="status-badge status-debit">خصم</span>
                                    @else
                                        <span class="status-badge status-credit">إضافة</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
            <!-- Financial Operations Summary Chart -->
            <div class="chart-card financial-summary">
                <div class="chart-header">
                    <h3 class="chart-title">ملخص العمليات المالية</h3>
                    <select class="period-select" id="periodSelect">
                        <option value="monthly" {{ ($selectedPeriod ?? 'monthly') === 'monthly' ? 'selected' : '' }}>شهري</option>
                        <option value="quarterly" {{ ($selectedPeriod ?? 'monthly') === 'quarterly' ? 'selected' : '' }}>ربع سنوي</option>
                        <option value="semiannual" {{ ($selectedPeriod ?? 'monthly') === 'semiannual' ? 'selected' : '' }}>نصف سنوي</option>
                        <option value="annual" {{ ($selectedPeriod ?? 'monthly') === 'annual' ? 'selected' : '' }}>سنوي</option>
                    </select>
                </div>
                <canvas id="financialOperationsChart"></canvas>
            </div>
    </div>

    <style>
        .date-range-btn {
            width: 105px;
            height: 34px;
            background: #0077B6; /* يمكنك تغيير اللون */
            border: none;
            color: #fff;
            font-size: 13px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            cursor: pointer;
            transition: 0.2s ease-in-out;
            opacity: 1;
        }

        .date-range-btn i {
            font-size: 14px;
        }

        .date-range-btn:hover {
            background: #005f8c;
        }

        /* Main Container */
        .payments-container {
            padding: 24px;
            background: #F3F4F6;
            min-height: 100vh;
            direction: rtl;
        }

        /* Statistics Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .stat-title {
            font-size: 13px;
            color: #6B7280;
            margin: 0 0 12px 0;
            font-weight: 500;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #1F2937;
            margin: 0;
        }

        /* Charts Section */
        .charts-section {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }

        .chart-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        /* Make financial summary same width as transactions updates */
        .financial-summary {
            width: 100%;
            margin: 0;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .chart-title {
            font-size: 16px;
            font-weight: 700;
            color: #1F2937;
            margin: 0;
        }

        .export-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            background: white;
            border: 1px solid #E5E7EB;
            border-radius: 6px;
            color: #374151;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .export-btn:hover {
            background: #F9FAFB;
        }

        .period-select {
            padding: 6px 12px;
            border: 1px solid #E5E7EB;
            border-radius: 6px;
            font-size: 13px;
            color: #374151;
        }

        /* Chart Legend */
        .chart-legend {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-top: 16px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #6B7280;
        }

        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 4px;
        }

        /* Updates Section */
        .updates-section {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 24px;
        }
        .cards-table { overflow-x: auto; }

        /* Badge colors for card statuses */
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        .status-pending   { background:#FEF3C7; color:#D97706; } /* مصدر: أصفر */
        .status-active    { background:#DBEAFE; color:#2563EB; } /* مشحون: لبني */
        .status-expired   { background:#E5E7EB; color:#374151; } /* منتهي: رمادي */
        .status-cancelled { background:#F3D6D6; color:#7E2E2E; } /* ملغي: بصلي */

        /* Status Overview Cards */
        .status-overview {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }
        .status-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            border-radius: 10px;
            color: white;
            font-weight: 700;
        }
        .status-card h4 { margin: 0; font-size: 14px; }
        .status-card span { font-size: 20px; }
        .bg-green { background:#10B981; }
        .bg-blue  { background:#3B82F6; }
        .bg-gray  { background:#6B7280; }
        .bg-onion { background:#7E2E2E; }

        /* Transactions Section */
        .transactions-section {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #1F2937;
            margin: 0;
        }

        .view-all-link {
            font-size: 13px;
            color: #3B82F6;
            text-decoration: none;
        }

        .view-all-link:hover {
            text-decoration: underline;
        }

        /* Data Table */
        .transactions-table {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead {
            background: #F9FAFB;
            border-bottom: 2px solid #E5E7EB;
        }

        .data-table th {
            padding: 12px 16px;
            text-align: center;
            font-size: 13px;
            font-weight: 700;
            color: #374151;
            white-space: nowrap;
        }

        .data-table td {
            padding: 16px;
            text-align: center;
            font-size: 13px;
            color: #4B5563;
            border-bottom: 1px solid #F3F4F6;
        }

        .data-table tbody tr:hover {
            background: #F9FAFB;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-debit {
            background: #FEE2E2;
            color: #DC2626;
        }

        .status-credit {
            background: #D1FAE5;
            color: #059669;
        }

        /* User Info */
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-direction: row;
            justify-content: flex-start;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
        }

        .user-details {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            text-align: right;
        }

        .user-name {
            font-size: 13px;
            font-weight: 600;
            color: #1F2937;
        }

        .user-email {
            font-size: 12px;
            color: #6B7280;
        }

        /* Action Button */
        .action-btn {
            background: none;
            border: none;
            color: #6B7280;
            cursor: pointer;
            padding: 4px 8px;
            transition: all 0.3s;
        }

        .action-btn:hover {
            color: #1F2937;
        }

        /* Mid Section layout: two columns (RTL -> updates on the right) */
        .mid-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            align-items: stretch; /* make columns equal height */
            margin-bottom: 24px;
        }

        /* Equalize heights between updates and card states, and enable scroll */
        .mid-section .updates-section,
        .mid-section .charts-section,
        .mid-section .charts-section .chart-card { height: 100%; }

        .mid-section .charts-section .chart-card,
        .mid-section .updates-section { min-height: 420px; }
        .mid-section .updates-section { margin-bottom: 0; }

        /* Scrollable tables */
        .cards-table {
            overflow-x: auto;
            max-height: 420px;
            overflow-y: auto;
        }

        .transactions-table {
            overflow-x: auto;
            max-height: 420px;
            overflow-y: auto;
        }

        /* Responsive */
        @media (max-width: 1400px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .status-overview {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 1400px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .status-overview {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 1024px) {
            .charts-section {
                grid-template-columns: 1fr;
            }
            .financial-summary {
                width: 100%;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
            .mid-section {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .payments-container {
                padding: 16px;
            }

            .transactions-table {
                overflow-x: scroll;
            }
            .cards-table { overflow-x: scroll; }
        }
        /* Modal styles */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        .modal-card {
            background: #fff;
            width: 90%;
            max-width: 520px;
            border-radius: 12px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
            overflow: hidden;
            direction: rtl;
        }
        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid #eee;
        }
        .modal-title { font-size: 18px; margin: 0; }
        .modal-close {
            background: transparent;
            border: none;
            font-size: 22px;
            cursor: pointer;
            line-height: 1;
            color: #6B7280;
        }
        .modal-body { padding: 16px 20px; }
        .modal-field { margin-bottom: 12px; }
        .modal-field label { display: block; margin-bottom: 6px; color: #374151; }
        .modal-field input[type="date"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            outline: none;
        }
        .modal-footer {
            display: flex;
            gap: 8px;
            justify-content: flex-start;
            padding: 14px 20px 18px;
            border-top: 1px solid #eee;
        }
        .btn-primary {
            background: #3B82F6;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 16px;
            cursor: pointer;
        }
        .btn-secondary {
            background: #e5e7eb;
            color: #111827;
            border: none;
            border-radius: 8px;
            padding: 10px 16px;
            cursor: pointer;
        }
        body.modal-open { overflow: hidden; }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Payment Card States Doughnut Chart (sold, charged, expired, cancelled)
        const paymentStatesCtx = document.getElementById('paymentStatesChart').getContext('2d');
        new Chart(paymentStatesCtx, {
            type: 'doughnut',
            data: {
                labels: ['بطاقات مباعة', 'بطاقات مشحونة', 'بطاقات منتهية', 'بطاقات ملغية'],
                datasets: [{
                    data: [
                        {{ $counts['sold'] ?? 0 }},
                        {{ $counts['charged'] ?? 0 }},
                        {{ $counts['expired'] ?? 0 }},
                        {{ $counts['cancelled'] ?? 0 }}
                    ],
                    backgroundColor: ['#10B981', '#3B82F6', '#6B7280', 'rgba(252, 179, 173, 1)'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1.5,
                plugins: {
                    legend: {
                        display: false,
                    }
                }
            }
        });
        // Modal date range for Payment Card States
        const statesOpenBtn = document.getElementById('statesPeriodBtn');
        const statesModal = document.getElementById('statesModal');
        const statesCloseBtn = document.getElementById('statesModalClose');
        const statesCancelBtn = document.getElementById('statesModalCancel');
        const statesApplyBtn = document.getElementById('statesModalApply');
        const statesModalFrom = document.getElementById('statesModalFrom');
        const statesModalTo = document.getElementById('statesModalTo');

        function openStatesModal() {
            if (statesModal) {
                statesModal.style.display = 'flex';
                document.body.classList.add('modal-open');
            }
        }
        function closeStatesModal() {
            if (statesModal) {
                statesModal.style.display = 'none';
                document.body.classList.remove('modal-open');
            }
        }
        if (statesOpenBtn) statesOpenBtn.addEventListener('click', openStatesModal);
        if (statesCloseBtn) statesCloseBtn.addEventListener('click', closeStatesModal);
        if (statesCancelBtn) statesCancelBtn.addEventListener('click', closeStatesModal);
        if (statesModal) {
            statesModal.addEventListener('click', (e) => { if (e.target === statesModal) closeStatesModal(); });
        }
        if (statesApplyBtn) {
            statesApplyBtn.addEventListener('click', () => {
                if (statesModalFrom && statesModalTo && statesModalFrom.value && statesModalTo.value) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('states_from', statesModalFrom.value);
                    url.searchParams.set('states_to', statesModalTo.value);
                    url.searchParams.delete('states_period');
                    window.location.href = url.toString();
                }
            });
        }

        // Financial Operations Bar Chart
        const financialOperationsCtx = document.getElementById('financialOperationsChart').getContext('2d');
        new Chart(financialOperationsCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($financialOperations['labels']) !!},
                datasets: [{
                    label: 'عمليات الإيداع',
                    data: {!! json_encode($financialOperations['deposits']) !!},
                    backgroundColor: '#10B981',
                }, {
                    label: 'عمليات السحب',
                    data: {!! json_encode($financialOperations['withdrawals']) !!},
                    backgroundColor: '#EF4444',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value;
                            }
                        }
                    }
                }
            }
        });

        // Handle period changes for financial operations
        const periodSelect = document.getElementById('periodSelect');
        if (periodSelect) {
            periodSelect.addEventListener('change', function () {
                const url = new URL(window.location.href);
                url.searchParams.set('period', this.value);
                // Clear any range params that may exist
                url.searchParams.delete('from');
                url.searchParams.delete('to');
                window.location.href = url.toString();
            });
        }
    </script>
@endsection
