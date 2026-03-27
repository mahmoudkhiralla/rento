@extends('dashboard.layouts.app')

@section('title', 'الرئيسية')

@push('styles')
<style>
    .metric-card { background:#fff; border:1px solid #e5e7eb; border-radius:10px; box-shadow:0 2px 4px rgba(0,0,0,0.05); transition:0.2s ease-in-out; }
    .metric-card:hover { transform:translateY(-2px); }
    .metric-top { display:flex; align-items:center; justify-content:space-between; }
    .metric-left { display:flex; align-items:center; gap:12px; }
    .metric-icon { background:#f3f4f6; color:#374151; font-size:21px; margin-right:10px; padding:10px; border-radius:10px; display:flex; align-items:center; justify-content:center; }
    .metric-title { font-size:14px; color:#6b7280; margin-bottom:3px; padding:10px; }
    .metric-value { font-size:20px; font-weight:700; color:#111827; }
    .metric-change { margin-left:10px; margin-top:25px; font-size:13px; font-weight:600; color:rgba(5,150,105,1); }
    .metric-subtext { font-size:12px; color:rgba(5,150,105,1); margin-top:2px; }
    .metric-btn { background-color:#3b82f6; color:#fff; border:none; border-radius:0 0 10px 10px; width:100%; padding:10px; margin-top:10px; font-size:13px; font-weight:600; transition:background 0.2s; display:block; text-align:center; text-decoration:none; cursor:pointer; }
    .metric-btn:hover { background-color:#2563eb; }
</style>
@endpush

@section('content')
    <div class="row g-3 mb-4">
        <!-- إجمالي المستخدمين -->
        <div class="col-xl-3 col-md-6">
            <div class="metric-card">
                <div class="metric-top">
                    <div class="metric-left">
                        <div class="metric-icon"><i class="fas fa-users"></i></div>
                        <div>
                            <div class="metric-title">إجمالي المستخدمين</div>
                            <div class="metric-value">{{ number_format($stats['total_users'] ?? 0) }}</div>
                        </div>
                    </div>
                    <div class="metric-change">
                        <i class="fas fa-arrow-up"></i> 122
                    </div>
                </div>
                <a class="metric-btn" href="{{ route('dashboard.users.list') }}">عرض الكل</a>
            </div>
        </div>

        <!-- المؤجرين النشطين -->
        <div class="col-xl-3 col-md-6">
            <div class="metric-card">
                <div class="metric-top">
                    <div class="metric-left">
                        <div class="metric-icon"><i class="fas fa-user-tie"></i></div>
                        <div>
                            <div class="metric-title">المؤجرين النشطين</div>
                            <div class="metric-value">{{ number_format($stats['active_landlords'] ?? 0) }}</div>
                        </div>
                    </div>
                    <div class="metric-change">
                        <i class="fas fa-arrow-up"></i> 122
                    </div>
                </div>
                <a class="metric-btn" href="{{ route('dashboard.users.list', ['type' => 'landlord']) }}">عرض الكل</a>
            </div>
        </div>

        <!-- إجمالي العقارات -->
        <div class="col-xl-3 col-md-6">
            <div class="metric-card">
                <div class="metric-top">
                    <div class="metric-left">
                        <div class="metric-icon"><i class="fas fa-building"></i></div>
                        <div>
                            <div class="metric-title">إجمالي العقارات</div>
                            <div class="metric-value">{{ number_format($stats['total_properties'] ?? 0) }}</div>
                        </div>
                    </div>
                    <div class="metric-change">
                        <i class="fas fa-arrow-up"></i> 122
                    </div>
                </div>
                <a class="metric-btn" href="{{ route('dashboard.properties.index') }}">عرض الكل</a>
            </div>
        </div>

        <!-- إجمالي الحجوزات -->
        <div class="col-xl-3 col-md-6">
            <div class="metric-card">
                <div class="metric-top">
                    <div class="metric-left">
                        <div class="metric-icon"><i class="fas fa-image"></i></div>
                        <div>
                            <div class="metric-title">إجمالي الحجوزات</div>
                            <div class="metric-value">{{ number_format($stats['total_bookings'] ?? 0) }}</div>
                        </div>
                    </div>
                    <div class="metric-change">
                        <i class="fas fa-arrow-up"></i> 122
                    </div>
                </div>
                <a class="metric-btn" href="{{ route('dashboard.orders.latest', ['status' => 'all']) }}">عرض الكل</a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <!-- Chart 1: معدلات نمو العملاء -->
        <div class="col-xl-6">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h5 class="chart-card-title">معدلات نمو العملاء</h5>
                    <select class="chart-filter" id="growthPeriodSelect">
                        <option value="quarter" {{ ($period ?? 'quarter') === 'quarter' ? 'selected' : '' }}>ربع سنوي</option>
                        <option value="month" {{ ($period ?? 'quarter') === 'month' ? 'selected' : '' }}>شهري</option>
                        <option value="year" {{ ($period ?? 'quarter') === 'year' ? 'selected' : '' }}>سنوي</option>
                    </select>
                </div>
                <canvas id="growthChart" height="300"></canvas>
                <div class="chart-legend mt-3">
                    <div class="d-flex justify-content-center gap-4">
                        <div class="d-flex align-items-center">
                            <span class="legend-dot" style="background-color: #2B7FE6;"></span>
                            <span class="small text-muted">المؤجرين</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="legend-dot" style="background-color: #6B7280;"></span>
                            <span class="small text-muted">المستأجرين</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart 2: معدلات طلب الحجز -->
        <div class="col-xl-6">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h5 class="chart-card-title">معدلات طلب الحجز</h5>
                    <div class="dropdown" id="bookingCityDropdown">
                        <button class="chart-filter dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <span id="cityFilterLabel">{{ request('city') ? e(request('city')) : 'المنطقة' }}</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" data-city="الرياض">الرياض</a></li>
                            <li><a class="dropdown-item" href="#" data-city="جدة">جدة</a></li>
                            <li><a class="dropdown-item" href="#" data-city="الدمام">الدمام</a></li>
                            <li><a class="dropdown-item" href="#" data-city="">الكل</a></li>
                        </ul>
                    </div>
                </div>
                <canvas id="bookingChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <!-- Summary Cards -->
        <div class="col-xl-3 col-md-6">
            <div class="summary-card">
                <div class="summary-card-title">إجمالي قيمة الحجوزات</div>
                <div class="summary-card-value">{{ number_format($summary['booking_value'] ?? 0) }}</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="summary-card">
                <div class="summary-card-title">إجمالي الدخل</div>
                <div class="summary-card-value">{{ number_format($summary['total_revenue'] ?? 0) }}</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="summary-card">
                <div class="summary-card-title">إجمالي البطاقات المصدرة</div>
                <div class="summary-card-value">{{ number_format($summary['total_cards'] ?? 0) }}</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="summary-card">
                <div class="summary-card-title">إجمالي البطاقات المستخدمة</div>
                <div class="summary-card-value">{{ number_format($summary['used_cards'] ?? 0) }}</div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Chart 3: ملخص العمليات المالية -->
        <div class="col-xl-6">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h5 class="chart-card-title">ملخص العمليات المالية</h5>
                    <select class="chart-filter" id="financialPeriodSelect">
                        <option value="quarter" {{ ($period ?? 'quarter') === 'quarter' ? 'selected' : '' }}>ربع سنوي</option>
                        <option value="month" {{ ($period ?? 'quarter') === 'month' ? 'selected' : '' }}>شهري</option>
                        <option value="year" {{ ($period ?? 'quarter') === 'year' ? 'selected' : '' }}>سنوي</option>
                    </select>
                </div>
                <canvas id="financialChart" height="300"></canvas>
                <div class="chart-legend mt-3">
                    <div class="d-flex justify-content-center gap-4">
                        <div class="d-flex align-items-center">
                            <span class="legend-dot" style="background-color: #22C55E;"></span>
                            <span class="small text-muted">عمليات الإضافة</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="legend-dot" style="background-color: #EF4444;"></span>
                            <span class="small text-muted">المبالغ بانتظار دينار</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart 4: معدلات الحجز حسب المناطق -->
        <div class="col-xl-6">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h5 class="chart-card-title">معدلات الحجز حسب المناطق</h5>
                    <div class="d-flex align-items-center gap-2">
                        <select class="chart-filter" id="regionPeriodSelect">
                            <option value="quarter" {{ ($period ?? 'quarter') === 'quarter' ? 'selected' : '' }}>ربع سنوي</option>
                            <option value="month" {{ ($period ?? 'quarter') === 'month' ? 'selected' : '' }}>شهري</option>
                            <option value="year" {{ ($period ?? 'quarter') === 'year' ? 'selected' : '' }}>سنوي</option>
                        </select>
                        <div class="dropdown" id="regionCityDropdown">
                            <button class="chart-filter dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <span id="regionCityFilterLabel">{{ request('city') ? e(request('city')) : 'المنطقة' }}</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" data-city="الرياض">الرياض</a></li>
                                <li><a class="dropdown-item" href="#" data-city="جدة">جدة</a></li>
                                <li><a class="dropdown-item" href="#" data-city="الدمام">الدمام</a></li>
                                <li><a class="dropdown-item" href="#" data-city="">الكل</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <canvas id="regionChart" height="300"></canvas>
                <div class="chart-legend mt-3">
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <div class="d-flex align-items-center">
                            <span class="legend-dot" style="background-color: #F59E0B;"></span>
                            <span class="small text-muted">منطقة 1</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="legend-dot" style="background-color: #22C55E;"></span>
                            <span class="small text-muted">منطقة 2</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="legend-dot" style="background-color: #2B7FE6;"></span>
                            <span class="small text-muted">منطقة 3</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="legend-dot" style="background-color: #9CA3AF;"></span>
                            <span class="small text-muted">منطقة 4</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function updateQueryParam(name, value) {
                const url = new URL(window.location.href);
                if (value && value.length) {
                    url.searchParams.set(name, value);
                } else {
                    url.searchParams.delete(name);
                }
                window.location.href = url.toString();
            }

            const growthSel = document.getElementById('growthPeriodSelect');
            if (growthSel) {
                growthSel.addEventListener('change', function() { updateQueryParam('period', this.value); });
            }
            const finSel = document.getElementById('financialPeriodSelect');
            if (finSel) {
                finSel.addEventListener('change', function() { updateQueryParam('period', this.value); });
            }
            const regionSel = document.getElementById('regionPeriodSelect');
            if (regionSel) {
                regionSel.addEventListener('change', function() { updateQueryParam('period', this.value); });
            }
            const cityDropdown = document.getElementById('bookingCityDropdown');
            if (cityDropdown) {
                cityDropdown.addEventListener('click', function(e) {
                    const target = e.target.closest('.dropdown-item');
                    if (!target) return;
                    const city = target.getAttribute('data-city') || '';
                    updateQueryParam('city', city);
                });
            }
            const regionCityDropdown = document.getElementById('regionCityDropdown');
            if (regionCityDropdown) {
                regionCityDropdown.addEventListener('click', function(e) {
                    const target = e.target.closest('.dropdown-item');
                    if (!target) return;
                    const city = target.getAttribute('data-city') || '';
                    updateQueryParam('city', city);
                });
            }
            // Chart 1: معدلات نمو العملاء (Bar Chart)
            const growthCtx = document.getElementById('growthChart');
            if (growthCtx) {
                new Chart(growthCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($userGrowthData['labels']) !!},
                        datasets: [
                            {
                                label: 'المؤجرين',
                                data: {!! json_encode($userGrowthData['landlords']) !!},
                                backgroundColor: '#2B7FE6',
                                borderRadius: 8,
                                barThickness: 35
                            },
                            {
                                label: 'المستأجرين',
                                data: {!! json_encode($userGrowthData['tenants']) !!},
                                backgroundColor: '#6B7280',
                                borderRadius: 8,
                                barThickness: 35
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 125,
                                ticks: {
                                    stepSize: 25,
                                    font: {
                                        family: 'Cairo',
                                        size: 11
                                    },
                                    color: '#9CA3AF'
                                },
                                grid: {
                                    color: '#F3F4F6',
                                    drawBorder: false
                                }
                            },
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false
                                },
                                ticks: {
                                    font: {
                                        family: 'Cairo',
                                        size: 11
                                    },
                                    color: '#6B7280'
                                }
                            }
                        }
                    }
                });
            }

            // Chart 2: معدلات طلب الحجز (Pie Chart)
            const bookingCtx = document.getElementById('bookingChart');
            if (bookingCtx) {
                new Chart(bookingCtx, {
                    type: 'pie',
                    data: {
                        labels: {!! json_encode($bookingAreaData['labels']) !!},
                        datasets: [{
                            data: {!! json_encode($bookingAreaData['counts']) !!},
                            backgroundColor: ['#F59E0B', '#22C55E', '#2B7FE6', '#C084FC'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    padding: 15,
                                    font: {
                                        family: 'Cairo',
                                        size: 11
                                    },
                                    color: '#6B7280',
                                    generateLabels: function(chart) {
                                        const data = chart.data;
                                        if (data.labels.length && data.datasets.length) {
                                            return data.labels.map((label, i) => {
                                                const dataset = data.datasets[0];
                                                return {
                                                    text: label,
                                                    fillStyle: dataset.backgroundColor[i],
                                                    hidden: false,
                                                    index: i
                                                };
                                            });
                                        }
                                        return [];
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Chart 3: ملخص العمليات المالية (Bar Chart)
            const financialCtx = document.getElementById('financialChart');
            if (financialCtx) {
                new Chart(financialCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($financialSummary['labels']) !!},
                        datasets: [
                            {
                                label: 'عمليات الإضافة',
                                data: {!! json_encode($financialSummary['credits']) !!},
                                backgroundColor: '#22C55E',
                                borderRadius: 8,
                                barThickness: 35
                            },
                            {
                                label: 'عمليات الخصم',
                                data: {!! json_encode($financialSummary['debits']) !!},
                                backgroundColor: '#EF4444',
                                borderRadius: 8,
                                barThickness: 35
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 125,
                                ticks: {
                                    stepSize: 25,
                                    font: {
                                        family: 'Cairo',
                                        size: 11
                                    },
                                    color: '#9CA3AF'
                                },
                                grid: {
                                    color: '#F3F4F6',
                                    drawBorder: false
                                }
                            },
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false
                                },
                                ticks: {
                                    font: {
                                        family: 'Cairo',
                                        size: 11
                                    },
                                    color: '#6B7280'
                                }
                            }
                        }
                    }
                });
            }

            // Chart 4: معدلات الحجز حسب المناطق (Doughnut Chart)
            const regionCtx = document.getElementById('regionChart');
            if (regionCtx) {
                new Chart(regionCtx, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($bookingAreaData['labels']) !!},
                        datasets: [{
                            data: {!! json_encode($bookingAreaData['counts']) !!},
                            backgroundColor: ['#22C55E', '#2B7FE6', '#F59E0B', '#9CA3AF'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }
        });
    </script>

    <style>
        .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-left: 8px;
        }

        .chart-legend {
            font-size: 11px;
        }

        /* تثبيت ارتفاع الرسوم ومنع تمددها عمودياً */
        .chart-card {
            position: relative;
            /* يضمن وجود مساحة ثابتة للكانفاس داخل البطاقة */
            min-height: 360px;
        }

        .chart-card canvas,
        #growthChart,
        #bookingChart,
        #financialChart,
        #regionChart {
            width: 100% !important;
            height: 300px !important;
            display: block;
        }
    </style>
@endpush
