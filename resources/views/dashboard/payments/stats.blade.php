@extends('dashboard.layouts.app')

@section('title', 'الغرامات والتعويضات')

@section('content')
    <div class="penalties-container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-title">إجمالي الغرامات المدفوعة</div>
                <div class="stat-value">{{ number_format($stats['total_paid_penalties'] ?? 0) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">إجمالي التعويضات المدفوعة</div>
                <div class="stat-value">{{ number_format($stats['total_paid_compensations'] ?? 0) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">العمليات قيد التنفيذ</div>
                <div class="stat-value">{{ number_format($stats['pending_operations'] ?? 0) }}</div>
            </div>
        </div>

        <!-- Main Section -->
        <div class="main-section">
            <div class="section-header">
                <h3 class="page-title">الغرامات والتعويضات</h3>
                <div class="header-actions">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="ابحث عن اسم مستخدم أو بريد إلكتروني" value="{{ request('search') }}">
                    </div>
                    <button class="filter-btn" onclick="toggleFilterDropdown()">
                        <i class="fas fa-sliders-h"></i>
                        تصفية
                    </button>
                </div>
            </div>

            <div class="filter-dropdown" id="filterDropdown" style="display:none;">
                <div class="filter-grid">
                    <div class="filter-item">
                        <label>الحالة</label>
                        <select id="fStatus" class="form-input">
                            <option value="all" {{ request('status')=='all' ? 'selected' : '' }}>الكل</option>
                            <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="paid" {{ request('status')=='paid' ? 'selected' : '' }}>تم الدفع</option>
                            <option value="cancelled" {{ request('status')=='cancelled' ? 'selected' : '' }}>ملغي</option>
                        </select>
                    </div>
                    <div class="filter-item">
                        <label>النوع</label>
                        <select id="fType" class="form-input">
                            <option value="all" {{ request('type')=='all' ? 'selected' : '' }}>الكل</option>
                            <option value="late_payment" {{ request('type')=='late_payment' ? 'selected' : '' }}>تأخير الدفع</option>
                            <option value="damage" {{ request('type')=='damage' ? 'selected' : '' }}>تلف</option>
                            <option value="cancellation" {{ request('type')=='cancellation' ? 'selected' : '' }}>إلغاء</option>
                            <option value="violation" {{ request('type')=='violation' ? 'selected' : '' }}>مخالفة</option>
                            <option value="compensation" {{ request('type')=='compensation' ? 'selected' : '' }}>تعويض</option>
                        </select>
                    </div>
                    <div class="filter-item">
                        <label>نوع الحساب</label>
                        <select id="fUserType" class="form-input">
                            <option value="all" {{ request('user_type')=='all' ? 'selected' : '' }}>الكل</option>
                            <option value="tenant" {{ request('user_type')=='tenant' ? 'selected' : '' }}>مستأجر</option>
                            <option value="landlord" {{ request('user_type')=='landlord' ? 'selected' : '' }}>مؤجر</option>
                        </select>
                    </div>
                    <div class="filter-item">
                        <label>من تاريخ</label>
                        <input type="date" id="fDateFrom" class="form-input" value="{{ request('date_from') }}">
                    </div>
                    <div class="filter-item">
                        <label>إلى تاريخ</label>
                        <input type="date" id="fDateTo" class="form-input" value="{{ request('date_to') }}">
                    </div>
                    <div class="filter-item">
                        <label>أدنى مبلغ</label>
                        <input type="number" step="0.01" id="fAmountMin" class="form-input" value="{{ request('amount_min') }}">
                    </div>
                    <div class="filter-item">
                        <label>أقصى مبلغ</label>
                        <input type="number" step="0.01" id="fAmountMax" class="form-input" value="{{ request('amount_max') }}">
                    </div>
                    <div class="filter-item">
                        <label>عدد العناصر</label>
                        <select id="fPerPage" class="form-input">
                            @php $pp = (int) request('per_page'); @endphp
                            <option value="10" {{ $pp===10 || $pp===0 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ $pp===25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ $pp===50 ? 'selected' : '' }}>50</option>
                        </select>
                    </div>
                </div>
                <div class="filter-actions">
                    <button id="applyFilters" class="page-btn">تطبيق</button>
                    <button id="clearFilters" class="page-btn">إزالة الفلاتر</button>
                </div>
            </div>

            <div class="table-section">
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>اسم المستخدم</th>
                                <th>تاريخ العملية</th>
                                <th>المبلغ</th>
                                <th>الحالة</th>
                                <th>نوع الحساب</th>
                                <th>السبب</th>
                                <th>التفاصيل</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($penalties as $index => $penalty)
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            @php
                                                $avatar = $penalty->user->avatar ?? null;
                                                $isUrl = $avatar && preg_match('/^https?:\/\//', $avatar);
                                                $avatarSrc = $isUrl ? $avatar : 'https://ui-avatars.com/api/?name=' . urlencode($penalty->user->name ?? 'User') . '&background=3B82F6&color=fff';
                                            @endphp
                                            <img src="{{ $avatarSrc }}" alt="User" class="user-avatar-sm">
                                            <span>{{ $penalty->user->name ?? 'اسم المستخدم' }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $penalty->created_at ? $penalty->created_at->format('Y/m/d g:i A') : '2025/05/25 6:00 م' }}</td>
                                    <td class="amount-cell">{{ number_format($penalty->amount ?? 0) }} د.ل</td>
                                    <td>
                                        @php
                                            $isCompPaid = (($penalty->status ?? null) === 'paid') && (($penalty->type ?? null) === 'compensation');
                                            $statusMap = [
                                                'pending' => ['text' => 'قيد التنفيذ', 'class' => 'status-pending'],
                                                'paid' => ['text' => ($isCompPaid ? 'تم الدفع' : 'تم الخصم'), 'class' => 'status-paid'],
                                                'cancelled' => ['text' => 'ملغي', 'class' => 'status-cancelled'],
                                            ];
                                            $status = $statusMap[$penalty->status ?? 'pending'] ?? $statusMap['pending'];
                                            $badgeClass = ($status['class'] === 'status-paid' && !$isCompPaid) ? 'status-deducted' : $status['class'];
                                        @endphp
                                        <span class="status-badge {{ $badgeClass }}">
                                            {{ $status['text'] }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $accountTypeMap = [
                                                'tenant' => 'مستأجر',
                                                'landlord' => 'مؤجر',
                                                'both' => 'مؤجر',
                                            ];
                                            $accType = $accountTypeMap[$penalty->user->user_type ?? 'tenant'] ?? 'مستخدم';
                                        @endphp
                                        {{ $accType }}
                                    </td>
                                    <td>{{ \Illuminate\Support\Str::limit($penalty->reason ?? '—', 80) }}</td>
                                    <td>
                                        <button type="button" class="details-link" onclick="openPenaltyModal({{ $penalty->id }})">
                                            معاينة
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align:center">لا توجد بيانات حالياً</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($penalties->lastPage() > 1)
                <div class="pagination-wrapper">
                    <span class="pagination-info">عرض {{ $penalties->count() ? $penalties->firstItem() : 0 }} إلى {{ $penalties->count() ? $penalties->lastItem() : 0 }} من {{ $penalties->total() }}</span>
                    <div class="pagination">
                        @php
                            $current = $penalties->currentPage();
                            $last = $penalties->lastPage();
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

        <div class="settings-card compensations-card" style="margin-top:24px;">
            <div class="card-header"><h3 class="card-title">حساب التعويضات</h3></div>
            <div class="card-body">
                <div class="row-grid">
                    <div class="row-right">
                        <label class="radio-label">
                            <input type="radio" name="compensation_method" value="percentage" class="radio-input" {{ ($settings['compensation_method'] ?? 'percentage') === 'percentage' ? 'checked' : '' }}>
                            <span class="radio-text">نسبة من المبلغ الأصلي</span>
                        </label>
                        <p class="option-description">نسبة التعويض التي يحصل عليها العميل عند الاسترداد</p>
                    </div>
                    <div class="row-left">
                        <div class="value-pill">{{ ($settings['compensation_percentage'] ?? 0) ? ('% ' . ($settings['compensation_percentage'])) : 'أدخل القيمة' }}</div>
                    </div>
                </div>
                <div class="row-grid">
                    <div class="row-right">
                        <label class="radio-label">
                            <input type="radio" name="compensation_method" value="full" class="radio-input" {{ ($settings['compensation_method'] ?? 'percentage') === 'full' ? 'checked' : '' }}>
                            <span class="radio-text">استرداد المبلغ كاملاً</span>
                        </label>
                        <p class="option-description">يسترد العميل المبلغ كاملاً</p>
                    </div>
                    <div class="row-left"></div>
                </div>
                <div class="row-grid">
                    <div class="row-right">
                        <label class="radio-label">
                            <input type="radio" name="compensation_method" value="fixed_extra" class="radio-input" {{ ($settings['compensation_method'] ?? 'percentage') === 'fixed_extra' ? 'checked' : '' }}>
                            <span class="radio-text">إضافة قيمة تعويضية للعميل</span>
                        </label>
                        <p class="option-description">يضاف مبلغ إضافي إلى رصيد العميل كتعويض</p>
                    </div>
                    <div class="row-left">
                        <div class="value-pill">{{ ($settings['compensation_fixed_extra'] ?? 0) ? ($settings['compensation_fixed_extra']) : 'أدخل القيمة' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="settings-card penalties-card" style="margin-top:16px;">
            <div class="card-header"><h3 class="card-title">الغرامات</h3></div>
            <div class="card-body">
                <div class="group-title">حساب غرامة الإلغاء</div>
                <div class="row-grid">
                    <div class="row-right">
                        <label class="radio-label">
                            <input type="radio" name="cancel_penalty_method" value="percentage" class="radio-input" {{ ($settings['cancel_penalty_method'] ?? 'fixed') === 'percentage' ? 'checked' : '' }}>
                            <span class="radio-text">نسبة ثابتة</span>
                        </label>
                        <p class="option-description">استخدام نسبة ثابتة من القيمة الإجمالية للإيجار لحساب عمولة التطبيق</p>
                    </div>
                    <div class="row-left">
                        <div class="value-pill">{{ ($settings['cancel_penalty_percentage'] ?? 0) ? ('% ' . ($settings['cancel_penalty_percentage'])) : 'أدخل القيمة' }}</div>
                    </div>
                </div>
                <div class="row-grid">
                    <div class="row-right">
                        <label class="radio-label">
                            <input type="radio" name="cancel_penalty_method" value="fixed" class="radio-input" {{ ($settings['cancel_penalty_method'] ?? 'fixed') === 'fixed' ? 'checked' : '' }}>
                            <span class="radio-text">قيمة ثابتة</span>
                        </label>
                        <p class="option-description">استخدام مبلغ ثابت من القيمة الإجمالية للإيجار لحساب عمولة التطبيق</p>
                    </div>
                    <div class="row-left">
                        <input type="text" class="form-input compact-input" id="penaltyFixedValueInput" value="{{ ($settings['cancel_penalty_fixed_value'] ?? 0) ? ($settings['cancel_penalty_fixed_value']) . ' د.ل' : '' }}" placeholder="أدخل القيمة">
                    </div>
                </div>
            </div>
        </div>

    </div>

    @include('dashboard.payments.partials.penalties-modal')
    @include('dashboard.payments.partials.penalties-styles')
    @include('dashboard.payments.partials.commissions-styles')
    <script>
        window.PAYMENT_SETTINGS = @json($settings ?? []);
        window.CSRF_TOKEN = '{{ csrf_token() }}';
    </script>
    @include('dashboard.payments.partials.penalties-scripts')
@endsection
