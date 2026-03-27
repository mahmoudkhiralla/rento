@extends('dashboard.layouts.app')

@section('title', 'المستخدمين')

@section('content')
    <div class="container-fluid px-4 py-4">

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4 metrics-row">
            <!-- إجمالي المستخدمين -->
            <div class="col-xl-4 col-md-6">
                <div class="metric-card">
                    <div class="metric-top">
                        <div class="metric-left">
                            <div class="metric-icon"><i class="fas fa-users"></i></div>
                            <div>
                                <div class="metric-title">إجمالي المستخدمين</div>
                                <div class="metric-value">{{ number_format($totalUsers ?? 0) }}</div>
                            </div>
                        </div>
                        <div class="metric-change text-success">
                            <i class="fas fa-arrow-up"></i> {{ number_format($newUsersWeek ?? 0) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- المستخدمين الجدد خلال الشهر -->
            <div class="col-xl-4 col-md-6">
                <div class="metric-card">
                    <div class="metric-top">
                        <div class="metric-left">
                            <div class="metric-icon"><i class="fas fa-user-plus"></i></div>
                            <div>
                                <div class="metric-title">المستخدمين الجدد خلال الشهر</div>
                                <div class="metric-value">{{ number_format($newUsersMonth ?? 0) }}</div>
                            </div>
                        </div>
                        <div class="metric-change text-success">
                            <i class="fas fa-arrow-up"></i> {{ number_format($newUsersWeek ?? 0) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- المؤجرين النشطين -->
            <div class="col-xl-4 col-md-6">
                <div class="metric-card">
                    <div class="metric-top">
                        <div class="metric-left">
                            <div class="metric-icon"><i class="fas fa-user-tie"></i></div>
                            <div>
                                <div class="metric-title">المؤجرين النشطين</div>
                                <div class="metric-value">{{ number_format($activeLandlords ?? 0) }}</div>
                            </div>
                        </div>
                        <div class="metric-change text-success">
                            <i class="fas fa-arrow-up"></i> {{ number_format($newLandlordsWeek ?? 0) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- قائمة المستخدمين Table -->
        <div class="card modern-card mb-4">
            <div class="card-header-custom">
                <h2 class="table-title">قائمة المستخدمين</h2>
                <div class="header-actions">

                    <form method="get" class="search-box-inline">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="ابحث عن اسم مستخدم أو بريد إلكتروني" />
                        <i class="fas fa-search"></i>
                    </form>

                    <button class="btn-filter" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-filter"></i>
                        تصفية
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 200px;">
                        <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['type' => 'all']) }}">الكل</a>
                        <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['type' => 'landlord']) }}">المؤجرين</a>
                        <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['type' => 'tenant']) }}">المستأجرين</a>
                    </div>

                </div>
            </div>

            <div class="table-responsive">
                <table class="table users-table">
                    <thead>
                    <tr>
                        <th>اسم المستخدم</th>
                        <th>البريد الإلكتروني</th>
                        <th>تاريخ التسجيل</th>
                        <th>رقم الجوال</th>
                        <th>التقييم</th>
                        <th>تأكيد الهوية</th>
                        <th>تأكيد الوجه</th>
                        <th>موثق الهوية</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar-wrapper">
                                        @php
                                            $avatarSource = !empty($user->avatar)
                                                ? (\Illuminate\Support\Str::startsWith($user->avatar, ['http://','https://'])
                                                    ? $user->avatar
                                                    : asset('storage/' . $user->avatar))
                                                : ('https://i.pravatar.cc/160?u=' . urlencode($user->email ?? ($user->id ?? uniqid())));
                                        @endphp
                                        <img src="{{ $avatarSource }}" alt="{{ $user->name }}" class="user-avatar">
                                    </div>
                                    <span class="user-name">{{ $user->name ?? 'اسم المستخدم' }}</span>
                                </div>
                            </td>
                            <td class="text-secondary">{{ $user->email ?? '-' }}</td>
                            <td class="text-secondary">{{ optional($user->created_at)->format('d / m / Y') ?? '-' }}</td>
                            <td class="text-secondary">{{ $user->phone ?? $user->mobile ?? '-' }}</td>
                            <td class="text-secondary">
                                {{ number_format(($ratingsMap[$user->id]['avg'] ?? 0), 1) }}
                                <span class="text-muted">({{ $ratingsMap[$user->id]['count'] ?? 0 }})</span>
                            </td>
                            <td class="text-center">
                                @if(!empty($user->id_verified) || !empty($user->email_verified_at))
                                    <i class="fas fa-check-circle verification-icon verified"></i>
                                @else
                                    <i class="far fa-circle verification-icon unverified"></i>
                                @endif
                            </td>
                            <td class="text-center">
                                @if(!empty($user->face_verified))
                                    <i class="fas fa-check-circle verification-icon verified"></i>
                                @else
                                    <i class="far fa-circle verification-icon unverified"></i>
                                @endif
                            </td>
                            <td class="text-center">
                                @if(!empty($user->id_document_verified))
                                    <i class="fas fa-check-circle verification-icon verified"></i>
                                @else
                                    <i class="far fa-circle verification-icon unverified"></i>
                                @endif
                            </td>
                            <td class="text-center">
                                @if(in_array($user->user_type, ['landlord', 'both']))
                                    <a href="{{ route('dashboard.landlords.show', $user) }}" class="preview-link">معاينة</a>
                                @else
                                    <a href="{{ route('dashboard.users.show', $user) }}" class="preview-link">معاينة</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center empty-state-row">
                                <div class="empty-state">
                                    <i class="fas fa-users"></i>
                                    <p>لا توجد بيانات لعرضها</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($users) && $users->hasPages())
                <div class="table-footer">
                    <div class="pagination-info">
                        عرض {{ $users->firstItem() }} إلى {{ $users->lastItem() }} من {{ $users->total() }}
                    </div>
                    <div class="pagination-wrapper">
                        {{ $users->onEachSide(1)->links('vendor.pagination.rento') }}
                    </div>
                </div>
            @endif
        </div>

        <!-- حسابات المستخدمين المعلقة Table -->
        <div class="card modern-card">
            <div class="card-header-custom">
                <h2 class="table-title">حسابات المستخدمين المعلقة</h2>
                <div class="header-actions">

                    <form method="get" class="search-box-inline">
                        <input type="text" name="q_suspended" placeholder="ابحث عن اسم مستخدم أو بريد إلكتروني" />
                        <i class="fas fa-search"></i>
                    </form>

                    <button class="btn-filter" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-filter"></i>
                        تصفية
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 200px;">
                        <a class="dropdown-item" href="#">الكل</a>
                        <a class="dropdown-item" href="#">معلق</a>
                        <a class="dropdown-item" href="#">نشط</a>
                    </div>

                    <button id="bulk-activate-btn" type="button" class="btn-connect">
                        <i class="fas fa-check"></i>
                        تفعيل المحدد
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table users-table">
                    <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="bulk-select-all" class="form-check-input">
                        </th>
                        <th>اسم المستخدم</th>
                        <th>البريد الإلكتروني</th>
                        <th>تاريخ التعليق</th>
                        <th>سبب التعليق</th>
                        <th>اسم المؤجر</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($suspendedUsers ?? [] as $user)
                        <tr>
                            <td>
                                <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="form-check-input bulk-select">
                            </td>
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar-wrapper">
                                        @php
                                            $sAvatar = !empty($user->avatar)
                                                ? (\Illuminate\Support\Str::startsWith($user->avatar, ['http://','https://'])
                                                    ? $user->avatar
                                                    : asset('storage/' . $user->avatar))
                                                : ('https://i.pravatar.cc/160?u=' . urlencode($user->email ?? ($user->id ?? uniqid())));
                                        @endphp
                                        <img src="{{ $sAvatar }}" alt="{{ $user->name }}" class="user-avatar">
                                    </div>
                                    <span class="user-name">{{ $user->name ?? 'اسم المستخدم' }}</span>
                                </div>
                            </td>
                            <td class="text-secondary">{{ $user->email ?? '-' }}</td>
                            <td class="text-secondary">
                                @php
                                    $lastSusp = \App\Models\SuspendedUser::where('user_id', $user->id)
                                        ->where('status', 'suspended')
                                        ->latest('id')
                                        ->first();
                                @endphp
                                {{ optional($lastSusp?->created_at)->format('d / m / Y') ?? '-' }}
                            </td>
                            <td>
                                @php
                                    $reasonLabels = [
                                        'property_damage' => 'إتلاف ممتلكات',
                                        'tos_violation' => 'انتهاك اتفاقية الاستخدام',
                                        'repeated_cancellation' => 'إلغاء متكرر',
                                        'misconduct' => 'سوء معاملة',
                                        'misleading_info' => 'معلومات مضللة',
                                        'fraud_suspicion' => 'أنشطة مشبوهة',
                                        'review' => 'لحين مراجعة الإدارة',
                                        'permanent' => 'تعليق نهائي',
                                    ];
                                    $badgeClass = 'default';
                                    $label = $lastSusp && $lastSusp->reason ? ($reasonLabels[$lastSusp->reason] ?? $lastSusp->reason) : null;
                                    if ($lastSusp && $lastSusp->reason) {
                                        if (in_array($lastSusp->reason, ['tos_violation','fraud_suspicion','misconduct'])) $badgeClass = 'warning';
                                        elseif (in_array($lastSusp->reason, ['review'])) $badgeClass = 'info';
                                        elseif (in_array($lastSusp->reason, ['permanent'])) $badgeClass = 'success';
                                    }
                                @endphp
                                @if($label)
                                    <span class="suspension-badge {{ $badgeClass }}">{{ $label }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if(in_array($user->user_type, ['landlord','both']))
                                    <div class="user-cell">
                                        <div class="user-avatar-wrapper">
                                            @php
                                                $landlordAvatar = !empty($user->avatar)
                                                    ? (\Illuminate\Support\Str::startsWith($user->avatar, ['http://','https://'])
                                                        ? $user->avatar
                                                        : asset('storage/' . $user->avatar))
                                                    : ('https://i.pravatar.cc/160?u=' . urlencode($user->email ?? ($user->id ?? uniqid())));
                                            @endphp
                                            <img src="{{ $landlordAvatar }}" alt="{{ $user->name }}" class="user-avatar">
                                        </div>
                                        <span class="user-name">{{ $user->name ?? 'اسم المؤجر' }}</span>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.users.activate', $user) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">تفعيل</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center empty-state-row">
                                <div class="empty-state">
                                    <i class="fas fa-user-lock"></i>
                                    <p>لا توجد حسابات معلقة حالياً</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($suspendedUsers) && $suspendedUsers->hasPages())
                <div class="table-footer">
                    <div class="pagination-info">
                        عرض {{ $suspendedUsers->firstItem() }} إلى {{ $suspendedUsers->lastItem() }} من {{ $suspendedUsers->total() }}
                    </div>
                    <div class="pagination-wrapper">
                        {{ $suspendedUsers->onEachSide(1)->links('vendor.pagination.rento') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        /* Metric Cards (properties page style) */
        .metric-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: 0.2s ease-in-out;
        }
        .metric-card:hover { transform: translateY(-2px); }
        .metric-top { display: flex; align-items: center; justify-content: space-between; }
        .metric-left { display: flex; align-items: center; gap: 12px; }
        .metric-icon { background: #f3f4f6; color: #374151; font-size: 21px; margin-right: 10px;margin-bottom: 15px; padding: 10px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
        .metric-title { font-size: 14px; color: #6b7280; margin-bottom: 3px; padding: 10px; }
        .metric-value { font-size: 20px; font-weight: 700; color: #111827; margin-bottom: 15px }
        .metric-change { margin-left: 10px; margin-top: 25px; font-size: 13px; font-weight: 600; color: rgba(5, 150, 105, 1); }
        .metric-btn { background-color: #3b82f6; color: #fff; border: none; border-radius: 0 0 10px 10px; width: 100%; padding: 10px; margin-top: 10px; font-size: 13px; font-weight: 600; transition: background 0.2s; }
        .metric-btn:hover { background-color: #2563eb; }
        .stat-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
            transition: 0.3s;
            padding: 14px 16px;
            position: relative;
        }

        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .stat-card-body {
            position: relative;
            text-align: right;
            direction: rtl;
        }

        .stat-card-title {
            color: #374151;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 4px;
        }

        .stat-card-value {
            font-size: 22px;
            font-weight: 700;
            color: #1f2937;
        }

        .stat-card-change {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 13px;
            font-weight: 500;
            margin-top: 4px;
        }

        .stat-card-change.positive {
            color: #16a34a;
        }

        .stat-card-icon {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #f1f5f9;
            border-radius: 8px;
            padding: 6px;
            color: #1e3a8a;
            font-size: 16px;
        }

        .preview-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }
        .preview-link:hover {
            text-decoration: underline;
        }
        /* Statistics Cards */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .stat-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .stat-card-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .stat-card-icon.blue {
            background-color: #E8F4FD;
            color: #2B7FE6;
        }

        .btn-add-stat {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid #E0E0E0;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            color: #666;
        }

        .btn-add-stat:hover {
            background: #f5f5f5;
            border-color: #ccc;
        }

        .stat-card-body {
            text-align: right;
        }

        .stat-card-title {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
        }

        .stat-card-value {
            font-size: 32px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 8px;
        }

        .stat-card-change {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            font-weight: 600;
        }

        .stat-card-change.positive {
            color: #22C55E;
        }

        .stat-card-change i {
            font-size: 12px;
        }

        /* Modern Card */
        .modern-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            border: none;
        }

        .card-header-custom {
            padding: 20px 24px;
            background: white;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .table-title {
            font-size: 18px;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-filter,
        .btn-connect {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 8px;
            border: 1px solid #E0E0E0;
            background: white;
            font-size: 14px;
            color: #666;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-filter:hover,
        .btn-connect:hover {
            background: #f5f5f5;
            border-color: #ccc;
        }

        .btn-connect {
            background: #2B7FE6;
            color: white;
            border-color: #2B7FE6;
        }

        .btn-connect:hover {
            background: #2069c7;
            border-color: #2069c7;
        }

        .search-box-inline {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-box-inline input {
            padding: 8px 40px 8px 16px;
            border: 1px solid #E0E0E0;
            border-radius: 8px;
            font-size: 14px;
            width: 300px;
            transition: all 0.3s;
        }

        .search-box-inline input:focus {
            outline: none;
            border-color: #2B7FE6;
            box-shadow: 0 0 0 3px rgba(43, 127, 230, 0.1);
        }

        .search-box-inline i {
            position: absolute;
            left: 14px;
            color: #999;
            font-size: 14px;
        }

        /* Users Table */
        .users-table {
            margin: 0;
        }

        .users-table thead {
            background: #F9FAFB;
        }

        .users-table thead th {
            padding: 14px 20px;
            font-size: 13px;
            font-weight: 600;
            color: #666;
            text-align: right;
            border: none;
        }

        .users-table tbody td {
            padding: 16px 20px;
            vertical-align: middle;
            border-bottom: 1px solid #f5f5f5;
            text-align: right;
        }

        .users-table tbody tr:last-child td {
            border-bottom: none;
        }

        .users-table tbody tr:hover {
            background: #fafafa;
        }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar-wrapper {
            flex-shrink: 0;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-avatar-placeholder {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }

        .user-name {
            font-weight: 500;
            color: #1a1a1a;
            font-size: 14px;
        }

        .text-secondary {
            color: #666 !important;
            font-size: 14px;
        }

        /* Verification Icons */
        .verification-icon {
            font-size: 18px;
        }

        .verification-icon.verified {
            color: #2B7FE6;
        }

        .verification-icon.unverified {
            color: #D0D0D0;
        }

        /* Suspension Badges */
        .suspension-badge {
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }

        .suspension-badge.default {
            background: #FFF3CD;
            color: #856404;
        }

        .suspension-badge.warning {
            background: #FFEBEE;
            color: #C62828;
        }

        .suspension-badge.info {
            background: #E3F2FD;
            color: #1565C0;
        }

        .suspension-badge.success {
            background: #E8F5E9;
            color: #2E7D32;
        }

        /* Empty State */
        .empty-state-row {
            padding: 60px 20px !important;
        }

        .empty-state {
            text-align: center;
        }

        .empty-state i {
            font-size: 48px;
            color: #ddd;
            margin-bottom: 12px;
        }

        .empty-state p {
            color: #999;
            font-size: 15px;
            margin: 0;
        }

        /* Table Footer */
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


        /* Rento Pagination (matches screenshot) */
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
            border-color: #31C48D; /* أخضر */
            box-shadow: 0 0 0 2px rgba(49,196,141,0.25);
            color: #111;
        }
        .rento-item.disabled .rento-link { color: #AAA; background: #FAFAFA; cursor: not-allowed; }
        .rento-ellipsis .rento-link { pointer-events: none; cursor: default; }

        /* Form Check Input */
        .form-check-input {
            width: 18px;
            height: 18px;
            border: 2px solid #D0D0D0;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: #2B7FE6;
            border-color: #2B7FE6;
        }

        .btn.btn-success.btn-sm {
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 12px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .card-header-custom {
                flex-direction: column;
                align-items: flex-start;
            }

            .header-actions {
                width: 100%;
                flex-direction: column;
            }

            .search-box-inline input {
                width: 100%;
            }

            .stat-card-value {
                font-size: 24px;
            }
        }
    </style>
    <form id="bulk-activate-form" method="POST" action="{{ route('admin.users.activate.bulk') }}" style="display:none;">
        @csrf
    </form>
    <script>
        (function(){
            const selectAll = document.getElementById('bulk-select-all');
            const checkboxes = Array.from(document.querySelectorAll('.bulk-select'));
            const bulkBtn = document.getElementById('bulk-activate-btn');
            const bulkForm = document.getElementById('bulk-activate-form');

            if (selectAll) {
                selectAll.addEventListener('change', function(){
                    checkboxes.forEach(cb => { cb.checked = selectAll.checked; });
                });
            }

            if (bulkBtn && bulkForm) {
                bulkBtn.addEventListener('click', function(){
                    // Clear previous hidden inputs
                    Array.from(bulkForm.querySelectorAll('input[name="user_ids[]"]')).forEach(el => el.remove());
                    // Gather selected IDs
                    const selected = checkboxes.filter(cb => cb.checked).map(cb => cb.value);
                    if (selected.length === 0) {
                        alert('من فضلك اختر على الأقل مستخدمًا واحدًا لتفعيله');
                        return;
                    }
                    selected.forEach(id => {
                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = 'user_ids[]';
                        hidden.value = id;
                        bulkForm.appendChild(hidden);
                    });
                    bulkForm.submit();
                });
            }
        })();
    </script>
@endsection
