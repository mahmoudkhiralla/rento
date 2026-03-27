@extends('dashboard.layouts.app')

@section('title', 'الإحصائيات والتقارير')

@section('content')
    <div class="reports-container">
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-content">
                    <h4 class="stat-title">إجمالي الدخل</h4>
                    <p class="stat-value">{{ $stats['total_revenue'] }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <h4 class="stat-title">إجمالي قيمة الحجوزات</h4>
                    <p class="stat-value">{{ number_format($stats['booking_value']) }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <h4 class="stat-title">إجمالي طلبات الحجز</h4>
                    <p class="stat-value">{{ number_format($stats['booking_requests']) }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <h4 class="stat-title">إجمالي الشكاوى</h4>
                    <p class="stat-value">{{ number_format($stats['complaints']) }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <h4 class="stat-title">المستخدمين</h4>
                    <p class="stat-value">{{ number_format($stats['total_users']) }}</p>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-section">
            <!-- User Growth Chart -->
            <div class="chart-card full-width">
                <div class="chart-header">
                    <h3 class="chart-title">معدلات نمو المستخدمين</h3>
                    <div class="header-actions" style="display:flex; gap:8px; align-items:center;">
                        <button class="period-btn" onclick="openCalendar()">
                            تغيير الفترة
                            <i class="far fa-calendar"></i>
                        </button>
                    </div>
                </div>
                <canvas id="userGrowthChart"></canvas>
            </div>

            <!-- Booking Reservations Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">معدلات طلب الحجز</h3>
                    <select class="period-select js-period-select">
                        <option value="month" {{ $period === 'month' ? 'selected' : '' }}>شهري</option>
                        <option value="quarter" {{ $period === 'quarter' ? 'selected' : '' }}>ربع سنوي</option>
                        <option value="half" {{ $period === 'half' ? 'selected' : '' }}>نصف سنوي</option>
                        <option value="year" {{ $period === 'year' ? 'selected' : '' }}>سنوي</option>
                        @if($period === 'custom')
                            <option value="custom" selected>مخصص</option>
                        @endif
                    </select>
                </div>
                <canvas id="bookingChart"></canvas>
            </div>

            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">الأماكن الأكثر حجزًا (أعلى 4)</h3>
                </div>
                <canvas id="topPlacesChart"></canvas>
                <div class="chart-legend">
                    @php $colors = ['#3B82F6','#10B981','#F59E0B','#EF4444']; @endphp
                    @for($i=0; $i < count($topActivePlacesData['labels']); $i++)
                        <div class="legend-item">
                            <span class="legend-color" style="background: {{ $colors[$i % 4] }};"></span>
                            <span class="legend-label">{{ $topActivePlacesData['labels'][$i] }}</span>
                            <span class="legend-value" style="margin-right:auto; color:#6B7280;">{{ $topActivePlacesData['counts'][$i] }} ({{ $topActivePlacesData['percentages'][$i] }}%)</span>
                        </div>
                    @endfor
                </div>
            </div>

            <!-- Revenue Chart -->
            <div class="chart-card full-width">
                <div class="chart-header">
                    <h3 class="chart-title">معدلات نمو الدخل</h3>
                    <div class="header-actions" style="display:flex; gap:8px; align-items:center;">
                        <button class="period-btn" onclick="openCalendar()">
                            تغيير الفترة
                            <i class="far fa-calendar"></i>
                        </button>
                    </div>
                </div>
                <canvas id="revenueChart"></canvas>
            </div>

            <!-- Complaint Submission Ratio -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">معدلات تقديم الشكاوى</h3>
                    <button class="period-btn" onclick="openCalendar()">
                        تغيير الفترة
                        <i class="far fa-calendar"></i>
                    </button>
                </div>
                <div class="submission-ratio">
                    <div class="ratio-bar">
                        <div class="segment landlords" style="width: {{ $complaintSubmissionRatio['percentages'][1] }}%">
                            <span class="segment-label">{{ $complaintSubmissionRatio['percentages'][1] }}%</span>
                        </div>
                        <div class="segment tenants" style="width: {{ $complaintSubmissionRatio['percentages'][0] }}%">
                            <span class="segment-label">{{ $complaintSubmissionRatio['percentages'][0] }}%</span>
                        </div>
                    </div>
                </div>
                <div class="chart-legend">
                    <div class="legend-item">
                        <span class="legend-color" style="background:#3B82F6"></span>
                        <span>المؤجرين</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background:#6B7280"></span>
                        <span>المستأجرين</span>
                    </div>
                </div>
            </div>
            <div class="chart-card small-card">
                <div class="chart-header">
                    <h3 class="chart-title">معدلات أنواع الشكاوى</h3>
                    <select class="period-select js-complaint-types-source">
                        <option value="landlords" selected>المؤجرين</option>
                        <option value="tenants">المستأجرين</option>
                    </select>
                </div>
                <canvas id="complaintTypesChart"></canvas>
                <div class="chart-legend" id="complaintTypesLegend">
                    @php $colors = ['#F59E0B', '#10B981', '#45B7D1', '#8B5CF6']; @endphp
                    @for($i=0; $i < count($complaintCategoriesLandlords['labels']); $i++)
                        <div class="legend-item">
                            <span class="legend-color" style="background: {{ $colors[$i % 4] }};"></span>
                            <span class="legend-label">{{ $complaintCategoriesLandlords['labels'][$i] }}</span>
                        </div>
                    @endfor
                </div>
            </div>

        </div>


        <div id="calendarModal" class="modal-overlay" style="display: none;">
            <div class="calendar-container">
                <div class="calendar-top">
                    <div class="calendar-top-left"></div>
                    <div class="calendar-top-title">تحديد الفترة</div>
                    <button onclick="closeCalendar()" class="close-btn">&times;</button>
                </div>
                <div class="calendar-controls">
                    <button class="nav-btn" onclick="prevMonth()"><i class="fas fa-chevron-right"></i></button>
                    <div class="month-year">
                        <select id="monthSelect"></select>
                        <select id="yearSelect"></select>
                    </div>
                    <button class="nav-btn" onclick="nextMonth()"><i class="fas fa-chevron-left"></i></button>
                </div>
                <div class="days-header" id="daysHeader"></div>
                <div class="calendar-grid" id="calendarGrid"></div>
                <div class="calendar-actions">
                    <button class="apply-period-btn" onclick="applyPeriod()">تطبيق</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Main Container */
        .reports-container {
            padding: 24px;
            background: #F3F4F6;
            min-height: 100vh;
            direction: rtl;
        }

        /* Header */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            gap: 20px;
        }

        .engineer-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .engineer-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
        }

        .engineer-name {
            font-size: 16px;
            font-weight: 700;
            color: #1F2937;
            margin: 0;
        }

        .engineer-role {
            font-size: 13px;
            color: #6B7280;
            margin: 0;
        }

        .header-search {
            position: relative;
            flex: 1;
            max-width: 400px;
        }

        .search-input {
            width: 100%;
            padding: 10px 40px 10px 16px;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            font-size: 14px;
        }

        .header-search i {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9CA3AF;
        }

        .period-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border: 1px solid #3B82F6;
            color: #3B82F6;
            background: transparent;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }

        .submission-ratio {
            padding: 12px 16px;
        }

        .ratio-bar {
            width: 100%;
            height: 20px;
            display: flex;
            border-radius: 8px;
            overflow: hidden;
            background: #E5E7EB;
        }

        .segment {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 12px;
            font-weight: 700;
        }

        .segment.tenants { background: #6B7280; }
        .segment.landlords { background: #3B82F6; }

        .segment-label {
            position: relative;
            z-index: 1;
        }

        /* Statistics Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .stat-title {
            font-size: 13px;
            color: #6B7280;
            margin: 0 0 8px 0;
            font-weight: 500;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #1F2937;
            margin: 0;
        }

        /* Charts Section */
        .charts-section {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }

        .chart-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .chart-card.small-card {
            padding: 16px;
        }

        .small-card canvas {
            height: 180px !important;
        }

        #topPlacesChart {
            height: 220px !important;
        }

        .chart-card.full-width {
            grid-column: 1 / -1;
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
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-top: 16px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            justify-content: center; /* هنا السحر */
            padding-right: 10px;
            gap: 8px;
            font-size: 13px;
            color: #6B7280;
        }


        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 4px;
        }

        /* Resolution Bars */
        .resolution-bars {
            margin-bottom: 16px;
        }

        .resolution-row {
            display: grid;
            grid-template-columns: 100px 1fr;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .resolution-label {
            font-size: 13px;
            color: #6B7280;
            text-align: right;
        }

        .resolution-bar {
            display: flex;
            height: 32px;
            border-radius: 6px;
            overflow: hidden;
        }

        .bar-segment {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            color: white;
        }

        .bar-segment.resolved {
            background: #10B981;
        }

        .bar-segment.pending {
            background: #3B82F6;
        }

        .resolution-legend {
            display: flex;
            gap: 20px;
            justify-content: center;
        }

        .resolution-legend .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 3px;
        }

        .resolution-legend .legend-color.resolved {
            background: #10B981;
        }

        .resolution-legend .legend-color.pending {
            background: #3B82F6;
        }

        /* Submission Bars */
        .submission-bars {
            margin-bottom: 16px;
        }

        .submission-row {
            display: grid;
            grid-template-columns: 150px 1fr;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .submission-label {
            font-size: 13px;
            color: #6B7280;
            text-align: right;
        }

        .submission-bar {
            display: flex;
            height: 28px;
            border-radius: 6px;
            overflow: hidden;
        }

        .bar-segment.rejected {
            background: #EF4444;
        }

        .submission-legend {
            display: flex;
            gap: 20px;
            justify-content: center;
        }

        .submission-legend .legend-color.rejected {
            background: #EF4444;
        }

        /* Complaint Boxes */
        .complaint-boxes {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }

        .complaint-box {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .box-title {
            font-size: 16px;
            font-weight: 700;
            color: #1F2937;
            margin: 0 0 16px 0;
            padding-bottom: 12px;
            border-bottom: 2px solid #E5E7EB;
        }

        .complaint-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .complaint-list li {
            padding: 12px 0;
            font-size: 14px;
            color: #374151;
            border-bottom: 1px solid #F3F4F6;
        }

        .complaint-list li:last-child {
            border-bottom: none;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .calendar-container {
            background: white;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            overflow: hidden;
        }

        .calendar-top {
            display: grid;
            grid-template-columns: 1fr auto 40px;
            align-items: center;
            background: #E5E7EB;
            padding: 8px 12px;
        }
        .calendar-top-title { font-weight: 700; color: #111827; }

        .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            color: #6B7280;
            cursor: pointer;
        }

        .calendar-controls { display:flex; align-items:center; justify-content: space-between; gap:12px; padding:12px; }
        .month-year { display:flex; gap:8px; align-items:center; }
        #monthSelect, #yearSelect { padding:6px 10px; border:1px solid #E5E7EB; border-radius:6px; font-size:13px; color:#374151; }

        .nav-btn { background: white; border: 1px solid #E5E7EB; border-radius: 6px; padding: 6px 12px; cursor: pointer; }
        #currentMode { font-weight: 600; color: #374151; }

        .calendar-mode { display: flex; justify-content: center; align-items: center; gap: 8px; margin-bottom: 12px; }
        .mode-btn { background: white; border: 1px solid #3B82F6; color: #3B82F6; border-radius: 8px; padding: 6px 12px; font-weight: 600; cursor: pointer; }
        .mode-btn.active { background: #3B82F6; color: #fff; }
        .slash { color: #6B7280; }

        .days-header { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; padding: 0 12px; color: #6B7280; font-size: 12px; }
        .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 6px; padding: 12px; touch-action: none; }
        .day { background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 8px; text-align: center; padding: 10px 0; color: #111827; cursor: pointer; user-select: none; }
        .day.disabled { background: transparent; border: none; cursor: default; }
        .day.start { background: #10B981; color: #fff; border-color: #10B981; }
        .day.end { background: #3B82F6; color: #fff; border-color: #3B82F6; }
        .day.in-range { background: #BFDBFE; color: #111827; border-color: #BFDBFE; }
        .calendar-actions { display: flex; justify-content: center; padding: 12px; }
        .apply-period-btn { background: #3B82F6; color: #fff; border: none; border-radius: 8px; padding: 8px 16px; font-weight: 700; cursor: pointer; }

        /* Responsive */
        @media (max-width: 1400px) {
            .stats-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .charts-section {
                grid-template-columns: 1fr;
            }

            .complaint-boxes {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .header-section {
                flex-direction: column;
                align-items: flex-start;
            }

            .header-search {
                max-width: 100%;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function(){
            var sels = document.querySelectorAll('.js-period-select');
            if (sels && sels.length) {
                sels.forEach(function(sel){
                    sel.addEventListener('change', function(){
                        var url = new URL(window.location.href);
                        url.searchParams.set('period', sel.value);
                        window.location.href = url.toString();
                    });
                });
            }
        })();

        // User Growth Chart
        const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
        new Chart(userGrowthCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($userGrowthData['labels']) !!},
                datasets: [{
                    label: 'المستأجرين',
                    data: {!! json_encode($userGrowthData['tenants']) !!},
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true,
                }, {
                    label: 'المؤجرين',
                    data: {!! json_encode($userGrowthData['landlords']) !!},
                    borderColor: '#6B7280',
                    backgroundColor: 'rgba(107, 114, 128, 0.1)',
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 3,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Booking Chart
        const bookingCtx = document.getElementById('bookingChart').getContext('2d');
        new Chart(bookingCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($bookingData['labels']) !!},
                datasets: [{
                    label: 'المستأجرين',
                    data: {!! json_encode($bookingData['tenants']) !!},
                    backgroundColor: '#3B82F6',
                }, {
                    label: 'المؤجرين',
                    data: {!! json_encode($bookingData['landlords']) !!},
                    backgroundColor: '#6B7280',
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
                        beginAtZero: true
                    }
                }
            }
        });

        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($revenueData['labels']) !!},
                datasets: [{
                    label: 'الدخل بالآلاف',
                    data: {!! json_encode($revenueData['revenue']) !!},
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true,
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
                        beginAtZero: true
                    }
                }
            }
        });

        // Removed legacy doughnut chart initialization for top active places

        // Landlord Complaints Pie Chart
        const ctColors = ['#F59E0B', '#10B981', '#45B7D1', '#8B5CF6'];
        const ctLandlordLabels = {!! json_encode($complaintCategoriesLandlords['labels']) !!};
        const ctLandlordCounts = {!! json_encode($complaintCategoriesLandlords['counts']) !!};
        const ctTenantLabels = {!! json_encode($complaintCategoriesTenants['labels']) !!};
        const ctTenantCounts = {!! json_encode($complaintCategoriesTenants['counts']) !!};
        const complaintTypesCtx = document.getElementById('complaintTypesChart').getContext('2d');
        let complaintTypesChart = new Chart(complaintTypesCtx, {
            type: 'doughnut',
            data: { labels: ctLandlordLabels, datasets: [{ data: ctLandlordCounts, backgroundColor: ctColors }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
        const sourceSel = document.querySelector('.js-complaint-types-source');
        const legendEl = document.getElementById('complaintTypesLegend');
        function renderLegend(labels) {
            if (!legendEl) return;
            legendEl.innerHTML = labels.map(function(l, i){
                return '<div class="legend-item"><span class="legend-color" style="background:'+ctColors[i%4]+'"></span><span class="legend-label">'+l+'</span></div>';
            }).join('');
        }
        if (sourceSel) {
            sourceSel.addEventListener('change', function(){
                const isLandlord = sourceSel.value === 'landlords';
                const labels = isLandlord ? ctLandlordLabels : ctTenantLabels;
                const data = isLandlord ? ctLandlordCounts : ctTenantCounts;
                complaintTypesChart.data.labels = labels;
                complaintTypesChart.data.datasets[0].data = data;
                complaintTypesChart.update();
                renderLegend(labels);
            });
        }

        var rangeStart = null;
        var rangeEnd = null;
        var viewMonth = null;
        var viewYear = null;
        var dragging = false;
        var dragAnchor = null;
        function openCalendar() {
            var m = document.getElementById('calendarModal');
            if (m) { m.style.display = 'flex'; }
            if (m) {
                m.addEventListener('click', function(e){ if (e.target === m) { closeCalendar(); } });
            }
            var url = new URL(window.location.href);
            var f = url.searchParams.get('from');
            var t = url.searchParams.get('to');
            if (f) { rangeStart = new Date(f); }
            if (t) { rangeEnd = new Date(t); }
            var base = rangeStart || new Date();
            viewMonth = base.getMonth();
            viewYear = base.getFullYear();
            initCalendarControls();
            renderCalendar();
        }

        // Top Active Places by Bookings (Top 4)
        const topPlacesCtx = document.getElementById('topPlacesChart').getContext('2d');
        new Chart(topPlacesCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($topActivePlacesData['labels']) !!},
                datasets: [{
                    data: {!! json_encode($topActivePlacesData['counts']) !!},
                    backgroundColor: ['#F59E0B', '#10B981', '#45B7D1', '#8B5CF6'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutout: '0%'
            }
        });

        // Calendar Functions
        function closeCalendar() { document.getElementById('calendarModal').style.display = 'none'; }

        function initCalendarControls(){
            var months = ['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];
            var monthSel = document.getElementById('monthSelect');
            var yearSel = document.getElementById('yearSelect');
            if (monthSel && monthSel.options.length === 0) {
                months.forEach(function(m, i){ var opt = document.createElement('option'); opt.value = i; opt.textContent = m; monthSel.appendChild(opt); });
            }
            if (yearSel && yearSel.options.length === 0) {
                var yNow = new Date().getFullYear();
                for (var y = yNow - 5; y <= yNow + 1; y++) { var opt = document.createElement('option'); opt.value = y; opt.textContent = y; yearSel.appendChild(opt); }
            }
            monthSel.value = String(viewMonth);
            yearSel.value = String(viewYear);
            monthSel.onchange = function(){ viewMonth = parseInt(monthSel.value, 10); renderCalendar(); };
            yearSel.onchange = function(){ viewYear = parseInt(yearSel.value, 10); renderCalendar(); };
        }
        function renderCalendar(){
            var daysHeader = document.getElementById('daysHeader');
            var grid = document.getElementById('calendarGrid');
            if (!daysHeader || !grid) return;
            var names = ['الأحد','الإثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت'];
            daysHeader.innerHTML = names.map(function(n){ return '<div>'+n+'</div>'; }).join('');
            grid.innerHTML = '';
            var first = new Date(viewYear, viewMonth, 1);
            var offset = first.getDay();
            var daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();
            for (var i = 0; i < offset; i++) { var d = document.createElement('div'); d.className = 'day disabled'; grid.appendChild(d); }
            for (var day = 1; day <= daysInMonth; day++) {
                var cell = document.createElement('div');
                cell.className = 'day';
                cell.textContent = day;
                var dateStr = formatDate(viewYear, viewMonth + 1, day);
                cell.dataset.date = dateStr;
                applyDayState(cell);
                cell.onpointerdown = function(e){ startDrag(this.dataset.date); e.preventDefault(); };
                cell.onpointerenter = function(){ if (dragging) { updateDrag(this.dataset.date); } };
                cell.onclick = function(){ onDayClick(this.dataset.date); };
                grid.appendChild(cell);
            }
        }
        function formatDate(y,m,d){ var mm = m<10 ? '0'+m : ''+m; var dd = d<10 ? '0'+d : ''+d; return y+'-'+mm+'-'+dd; }
        function parseDate(str){ var p = str.split('-'); return new Date(parseInt(p[0]), parseInt(p[1])-1, parseInt(p[2])); }
        function onDayClick(dateStr){
            var d = parseDate(dateStr);
            if (!rangeStart || (rangeStart && rangeEnd)) { rangeStart = d; rangeEnd = null; }
            else if (rangeStart && !rangeEnd) {
                if (d >= rangeStart) { rangeEnd = d; }
                else { rangeStart = d; rangeEnd = null; }
            }
            renderCalendar();
        }
        function startDrag(dateStr){ dragging = true; dragAnchor = parseDate(dateStr); rangeStart = dragAnchor; rangeEnd = null; renderCalendar(); }
        function updateDrag(dateStr){ if (!dragging || !dragAnchor) return; var d = parseDate(dateStr); if (d >= dragAnchor) { rangeStart = dragAnchor; rangeEnd = d; } else { rangeStart = d; rangeEnd = dragAnchor; } renderCalendar(); }
        document.addEventListener('pointerup', function(){ if (dragging) { dragging = false; dragAnchor = null; } });
        function applyDayState(cell){
            var ds = cell.dataset.date;
            if (!ds) return;
            var d = parseDate(ds);
            cell.classList.remove('start','end','in-range');
            if (rangeStart && sameDay(d, rangeStart)) { cell.classList.add('start'); }
            else if (rangeEnd && sameDay(d, rangeEnd)) { cell.classList.add('end'); }
            else if (rangeStart && rangeEnd && d > rangeStart && d < rangeEnd) { cell.classList.add('in-range'); }
        }
        function sameDay(a,b){ return a.getFullYear()===b.getFullYear() && a.getMonth()===b.getMonth() && a.getDate()===b.getDate(); }
        function prevMonth(){ if (viewMonth === 0) { viewMonth = 11; viewYear--; } else { viewMonth--; } updateSelects(); renderCalendar(); }
        function nextMonth(){ if (viewMonth === 11) { viewMonth = 0; viewYear++; } else { viewMonth++; } updateSelects(); renderCalendar(); }
        function updateSelects(){ var monthSel = document.getElementById('monthSelect'); var yearSel = document.getElementById('yearSelect'); if (monthSel) monthSel.value = String(viewMonth); if (yearSel) yearSel.value = String(viewYear); }
        function applyPeriod(){ if (!rangeStart) return; var url = new URL(window.location.href); url.searchParams.set('period', 'custom'); url.searchParams.set('from', formatDate(rangeStart.getFullYear(), rangeStart.getMonth()+1, rangeStart.getDate())); if (rangeEnd) { url.searchParams.set('to', formatDate(rangeEnd.getFullYear(), rangeEnd.getMonth()+1, rangeEnd.getDate())); } else { url.searchParams.delete('to'); } window.location.href = url.toString(); }

        function prevMonth() {
            // Implementation for previous month
        }

        function nextMonth() {
            // Implementation for next month
        }
    </script>
@endsection
