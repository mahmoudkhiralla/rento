@extends('dashboard.layouts.app')

@section('title', 'تفاصيل حاسب مستخدم')

@section('content')
    <div class="container-fluid px-4 py-4">
        <!-- Page Header -->
        <div class="page-header-section">
            <h1 class="page-main-title">تفاصيل حاسب مستخدم</h1>
            <a href="{{ route('dashboard.users.list') }}" class="btn back-btn">
                <i class="fas fa-chevron-right"></i>
                عودة الي صفحة المستخدمين
            </a>
        </div>

        <div class="row g-4">
            <!-- كارد تعليق/تفعيل الحساب -->
            <div id="suspend-card" class="suspend-card" style="display:none;" aria-hidden="true">
                <div class="suspend-top">
                    <div class="suspend-user">
                        <div class="suspend-user-name">{{ $user->name }}</div>
                        <div class="suspend-user-email">{{ $user->email }}</div>
                    </div>
                    <div class="suspend-avatar">
                        @php
                            $avatarForSuspend = !empty($user->avatar)
                                ? (\Illuminate\Support\Str::startsWith($user->avatar, ['http://','https://'])
                                    ? $user->avatar
                                    : asset('storage/' . $user->avatar))
                                : ('https://i.pravatar.cc/80?u=' . urlencode($user->email ?? ($user->id ?? uniqid())));
                        @endphp
                        <img src="{{ $avatarForSuspend }}" alt="{{ $user->name }}">
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.users.suspend', $user) }}">
                    @csrf
                    <div class="suspend-grid">
                        <div class="suspend-field">
                            <div class="suspend-field-label">مدة التعليق</div>
                            <div class="select-wrapper">
                                <select id="suspend-duration-select" name="duration" class="suspend-select" required>
                                    <option value="" selected disabled>اختر مدة التعليق</option>
                                    <option value="week">أسبوع</option>
                                    <option value="two_weeks">أسبوعين</option>
                                    <option value="month">شهر</option>
                                    <option value="review">لحين مراجعة الإدارة</option>
                                    <option value="permanent">نهائي</option>
                                </select>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                        <div class="suspend-field">
                            <div class="suspend-field-label">سبب تعليق الحساب</div>
                            <div class="select-wrapper">
                                <select id="suspend-reason-select" name="reason" class="suspend-select" required>
                                    <option value="" selected disabled>اختر السبب</option>
                                    <option value="property_damage">إتلاف ممتلكات</option>
                                    <option value="tos_violation">انتهاك اتفاقية الاستخدام</option>
                                    <option value="repeated_cancellation">إلغاء متكرر</option>
                                    <option value="misconduct">سوء معاملة</option>
                                    <option value="misleading_info">معلومات مضللة</option>
                                    <option value="fraud_suspicion">أنشطة مشبوهة</option>
                                </select>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                    </div>
                    <div class="suspend-actions">
                        <button id="suspend-apply-btn" class="btn btn-suspend-confirm" type="submit" disabled>تعليق الحساب</button>
                    </div>
                </form>
                @if(($user->status ?? 'active') === 'suspended')
                    <div class="suspend-actions" style="padding-top:0;">
                        <form method="POST" action="{{ route('admin.users.activate', $user) }}" style="display:inline;">
                            @csrf
                            <button class="btn btn-activate-confirm" type="submit">تفعيل الحساب</button>
                        </form>
                    </div>
                @endif
            </div>
            <!-- Right Column - User Profile (matched to users/show.blade.php) -->
            <div class="col-lg-6 order-lg-1 profile-card-container">
                <div class="profile-card">
                    <div class="profile-side">
                        <div class="profile-image">
                            @php
                                $avatarSource = !empty($user->avatar)
                                    ? (\Illuminate\Support\Str::startsWith($user->avatar, ['http://','https://'])
                                        ? $user->avatar
                                        : asset('storage/' . $user->avatar))
                                    : ('https://i.pravatar.cc/160?u=' . urlencode($user->email ?? ($user->id ?? uniqid())));
                            @endphp
                            <img src="{{ $avatarSource }}" alt="{{ $user->name }}">
                        </div>
                        <div class="profile-action">
                            <button class="btn btn-outline-danger btn-suspend-toggle">
                                <i class="fas fa-ban"></i>
                                إيقاف / تعليق
                            </button>
                        </div>
                    </div>

                    <div class="profile-info">
                        <div class="profile-header">
                            <div class="profile-name">{{ $user->name ?? 'اسم المستخدم' }}</div>
                            <p class="profile-date">تاريخ التسجيل:
                                <span class="date-value">{{ optional($user->created_at)->format('Y/m/d') ?? '-' }}</span>
                            </p>
                        </div>

                        <!-- Stats under name: places, reviews, rating -->
                        <div class="stats-row">
                            <div class="stat-item">
                                <i class="fas fa-map-marker-alt stat-icon"></i>
                                <span class="stat-value">{{ isset($properties) ? $properties->count() : ($activePropertiesCount ?? 0) }}</span>
                                <span class="stat-label">أماكن</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-comments stat-icon"></i>
                                <span class="stat-value">{{ $totalReviews ?? 0 }}</span>
                                <span class="stat-label">مراجعات</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-star stat-icon"></i>
                                <span class="stat-value">{{ $avgRating ?? 0 }}</span>
                                <span class="stat-label">تقييم</span>
                            </div>
                        </div>

                        <ul class="profile-details list-unstyled">
                            <li><i class="fas fa-briefcase"></i> يعمل في {{ $user->job ?? 'غير محدد' }}</li>
                            <li><i class="fas fa-home"></i> يسكن في مدينة {{ $user->city ?? 'غير محدد' }}</li>
                            <li><i class="fas fa-user-tag"></i> نوع المستخدم: {{ $user->user_type === 'tenant' ? 'مستأجر' : ($user->user_type === 'landlord' ? 'مؤجر' : 'كلاهما') }}</li>
                            <li><i class="fas fa-id-card"></i> إثبات الشخصية: {{ $user->id_verified ? 'مُوثَّق' : 'غير مُوثَّق' }}</li>
                            <li><i class="fas fa-user-check"></i> التعرف على الوجه: {{ $user->face_verified ? 'تم' : 'لم يتم' }}</li>
                            <li><i class="fas fa-bolt"></i> الحالة: {{
                                ($user->status ?? 'active') === 'active' ? 'مؤجر نشط' : (
                                    ($user->status ?? null) === 'suspended' ? 'مؤجر موقوف' : (
                                        ($user->status ?? null) === 'banned' ? 'مؤجر محظور' : 'غير محدد'
                                    )
                                )
                            }}</li>
                        </ul>
                    </div>
                </div>

                <!-- Active Properties Section -->
                <div class="active-properties-section">
                    <div class="section-header">
                        <h3 class="section-header-title">الأماكن النشطة</h3>
                    </div>

                    <div class="properties-grid">
                        @forelse($activePlaces ?? [] as $place)
                            <div class="property-item-card">
                                <div class="property-content">
                                    <!-- الصورة -->
                                    <div class="property-image-wrapper">
                                        @php $img = $place->image ?? null; @endphp
                                        @if($img)
                                            @php $img = is_string($img) ? trim($img) : null; @endphp
                                            @if($img && \Illuminate\Support\Str::startsWith($img, ['http://', 'https://']))
                                                <img src="{{ $img }}" alt="{{ $place->name }}">
                                            @elseif($img && \Illuminate\Support\Str::startsWith($img, ['/']))
                                                <img src="{{ asset(ltrim($img, '/')) }}" alt="{{ $place->name }}">
                                            @else
                                                @php $normalized = preg_replace('#^/?storage/#', 'storage/', $img); @endphp
                                                <img src="{{ asset($normalized ?? ('storage/' . ltrim($img, '/'))) }}" alt="{{ $place->name }}">
                                            @endif
                                        @else
                                            <div class="property-placeholder"><i class="fas fa-image"></i></div>
                                        @endif
                                    </div>

                                    <!-- التفاصيل -->
                                    <div class="property-info-section">
                                        <h4 class="property-title-text">{{ $place->name ?? 'اسم العقار' }}</h4>
                                        <p class="property-location-text">
                                            @if(($place->user_type ?? 'landlord') === 'tenant')
                                                {{ $place->city ?? '-' }}
                                            @else
                                                @php $city = $place->city ?? null; $area = $place->area ?? null; @endphp
                                                {{ $city && $area ? ($city . ' - ' . $area) : ($city ?? ($area ?? '-')) }}
                                            @endif
                                        </p>

                                        <div class="property-meta-row">
                                            @if(($place->user_type ?? 'landlord') === 'tenant')
                                                @if($place->rating !== null)
                                                    <div class="meta-item">
                                                        <i class="fas fa-star"></i>
                                                        <span>{{ number_format($place->rating, 1) }}</span>
                                                    </div>
                                                @endif
                                            @else
                                                @if(!empty($place->booking_type))
                                                    <div class="meta-item">
                                                        <i class="fas fa-flag"></i>
                                                        <span>{{ $place->booking_type }}</span>
                                                    </div>
                                                @endif
                                                @if(!empty($place->available_from))
                                                    <div class="meta-item">
                                                        <i class="far fa-calendar"></i>
                                                        <span>{{ $place->available_from instanceof \Illuminate\Support\Carbon ? $place->available_from->format('d M Y') : (is_string($place->available_from) ? $place->available_from : '') }}</span>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>

                                        @if(!is_null($place->price))
                                            <div class="property-price-row">
                                                <i class="fas fa-coins"></i>
                                                <span class="price-value">{{ $place->price }}</span>
                                                @if(!empty($place->price_unit))
                                                    <span class="price-unit">{{ $place->price_unit }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- الأزرار -->
                                <div class="property-actions-row">
                                    @if($place->is_published)
                                        <form method="POST" action="{{ route('dashboard.properties.deactivate', $place->property_id ?? $place->id) }}" onsubmit="return confirm('هل تريد إيقاف نشر هذا المكان؟');" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn-property-action btn-stop">
                                                <i class="fas fa-times"></i>
                                                إيقاف النشر
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('dashboard.properties.approve', $place->property_id ?? $place->id) }}" onsubmit="return confirm('هل تريد إعادة نشر هذا المكان؟');" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn-property-action btn-stop">
                                                <i class="fas fa-redo"></i>
                                                إعادة النشر
                                            </button>
                                        </form>
                                    @endif
                                    
                                </div>
                            </div>
                        @empty
                            <div class="empty-state text-center py-4">
                                <i class="fas fa-building" style="font-size: 28px; color: #888;"></i>
                                <p class="mt-2 mb-0">لا توجد أماكن نشطة</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Left Column - Reviews -->
            <div class="col-lg-6 order-lg-2">
                <div class="reviews-main-card">
                    <!-- Reviews Header -->
                    <div class="section-header">
                        <div class="reviews-card-header">
                            <h2 class="reviews-main-title">التقييم</h2>
                            <span class="avg-rating-badge">{{ number_format($avgRating ?? 0, 1) }}</span>
                            <a href="#" class="reviews-count-link view-all-link">قراءة ({{ $totalReviews ?? 0 }}) مراجعة</a>
                        </div>
                    </div>


                    <!-- Rating Bars -->
                    <div class="rating-bars-section">
                        @php
                            $calculateWidth = function($rating) { return (($rating ?? 0) / 5) * 100; };
                        @endphp

                        <div class="rating-bar-row">
                            <span class="rating-bar-label">العناية بالممتلكات</span>
                            <div class="rating-bar-wrapper">
                                <div class="rating-bar-bg">
                                    <div class="rating-bar-fill" style="width: {{ $calculateWidth($ratings['property_care'] ?? 0) }}%"></div>
                                </div>
                            </div>
                            <span class="rating-bar-value">{{ $ratings['property_care'] ?? '0' }}</span>
                        </div>

                        <div class="rating-bar-row">
                            <span class="rating-bar-label">الحفاظ على النظافة</span>
                            <div class="rating-bar-wrapper">
                                <div class="rating-bar-bg">
                                    <div class="rating-bar-fill" style="width: {{ $calculateWidth($ratings['cleanliness'] ?? 0) }}%"></div>
                                </div>
                            </div>
                            <span class="rating-bar-value">{{ $ratings['cleanliness'] ?? '0' }}</span>
                        </div>

                        <div class="rating-bar-row">
                            <span class="rating-bar-label">الالتزام بقواعد المنزل</span>
                            <div class="rating-bar-wrapper">
                                <div class="rating-bar-bg">
                                    <div class="rating-bar-fill" style="width: {{ $calculateWidth($ratings['rules_compliance'] ?? 0) }}%"></div>
                                </div>
                            </div>
                            <span class="rating-bar-value">{{ $ratings['rules_compliance'] ?? '0' }}</span>
                        </div>

                        <div class="rating-bar-row">
                            <span class="rating-bar-label">التسليم في الموعد</span>
                            <div class="rating-bar-wrapper">
                                <div class="rating-bar-bg">
                                    <div class="rating-bar-fill" style="width: {{ $calculateWidth($ratings['timely_delivery'] ?? 0) }}%"></div>
                                </div>
                            </div>
                            <span class="rating-bar-value">{{ $ratings['timely_delivery'] ?? '0' }}</span>
                        </div>
                    </div>

                    <!-- Reviews List -->
                    <div class="reviews-list-section">
                        @forelse(($reviewsList ?? collect()) as $review)
                            <div class="review-item-box">
                                <div class="review-header-row">
                                    <div class="reviewer-info-section">
                                        <div class="reviewer-avatar-wrapper">
                                            @if(!empty($review->reviewer_avatar_src))
                                                <img src="{{ $review->reviewer_avatar_src }}" alt="{{ $review->reviewer_name }}">
                                            @else
                                                <img src="https://via.placeholder.com/50" alt="reviewer">
                                            @endif
                                        </div>
                                        <div class="reviewer-details">
                                            <h4 class="reviewer-name-text">{{ $review->reviewer_name ?? 'اسم المستأجر' }}</h4>
                                            
                                        </div>
                                    </div>
                                    <div class="review-rating-badge">
                                        <i class="fas fa-star"></i>
                                        <span>{{ $review->rating ?? '0' }}</span>
                                    </div>
                                </div>
                                <div class="review-time-text">اليوم: {{ optional($review->created_at)->diffForHumans() ?? '-' }}</div>
                                <p class="review-comment-text">
                                    {{ $review->comment ?? '' }}
                                </p>
                            </div>
                        @empty
                            <div class="empty-state text-center py-4">
                                <i class="fas fa-comments" style="font-size: 24px; color: #888;"></i>
                                <p class="mt-2 mb-0">لا توجد مراجعات</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Profits Card -->
                <div class="profits-main-card">
                    <div class="section-header">
                    <div class="profits-card-header">
                            <h2 class="profits-main-title">الأرباح</h2>
                    </div>
                    </div>

                    <div class="profits-stats-grid">
                        <div class="profit-stat-box">
                            <div class="profit-stat-icon">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div class="profit-stat-content">
                                <div class="profit-stat-label">رصيد المحفظة</div>
                                <div class="profit-stat-value">{{ number_format($walletBalance ?? 0) }}</div>
                            </div>
                        </div>

                        <div class="profit-stat-box">
                            <div class="profit-stat-icon">
                                <i class="fas fa-gift"></i>
                            </div>
                            <div class="profit-stat-content">
                                <div class="profit-stat-label">مكافآت النقاط</div>
                                <div class="profit-stat-value">{{ number_format($pointsBalance ?? 0) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Operations Log Card -->
                <div class="transactions-main-card">
                    <div class="section-header">
                    <div class="transactions-card-header">
                        <h2 class="transactions-main-title">سجل العمليات</h2>
                        <button class="transactions-filter-btn" type="button" title="فلترة">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                    </div>

                    <div class="transactions-list">
                        @forelse(($transactions ?? collect()) as $tx)
                            @php
                                $booking = $tx->booking;
                                $days = $booking ? \Carbon\Carbon::parse($booking->start_date)->diffInDays(\Carbon\Carbon::parse($booking->end_date)) : null;
                                $dailyPrice = $booking?->property?->price;
                                $meta = is_array($tx->meta) ? $tx->meta : ($tx->meta ? json_decode($tx->meta, true) : []);
                                $total = $meta['total'] ?? (($dailyPrice && $days) ? round($dailyPrice * max(1, $days), 2) : null);
                                $commission = $meta['commission'] ?? ($total ? round($total * 0.09, 2) : null);
                                $isNegative = ($tx->amount < 0);
                                $typeMap = [
                                    'withdraw' => 'سحب',
                                    'deposit' => 'إيداع',
                                    'payment' => 'دفع',
                                    'credit' => 'ائتمان',
                                    'gift' => 'هدية',
                                ];
                                $typeLabel = $typeMap[$tx->type] ?? $tx->type;
                                $reason = $meta['reason'] ?? null;
                            @endphp

                            <div class="transaction-item">
                                <div class="transaction-top">
                                    <div class="transaction-amount {{ $isNegative ? 'negative' : 'positive' }}">
                                        <i class="{{ $isNegative ? 'fas fa-arrow-up' : 'fas fa-dollar-sign' }}"></i>
                                        <span>{{ number_format($tx->amount, 2) }} دينار</span>
                                    </div>
                                    <button class="transaction-toggle" type="button" aria-label="عرض التفاصيل">
                                        <span class="toggle-icon">▾</span>
                                    </button>
                                </div>
                                <div class="transaction-details">
                                    @if($booking)
                                        @php $roomCharge = $meta['room_charge'] ?? (($dailyPrice ?? 0) * max(1, $days ?? 1)); @endphp
                                        <div class="transaction-detail-row">
                                            <span class="detail-label">قيمة حجز الغرفة:</span>
                                            <span class="detail-value">{{ number_format($roomCharge ?? 0, 2) }} دينار</span>
                                        </div>
                                        <div class="transaction-detail-row">
                                            <span class="detail-label">لمدة:</span>
                                            <span class="detail-value">{{ $days }} يوم</span>
                                        </div>
                                        <div class="transaction-detail-row">
                                            <span class="detail-label">اسم المستأجر:</span>
                                            <span class="detail-value">{{ $booking->user?->name }}</span>
                                        </div>
                                        <div class="transaction-detail-row">
                                            <span class="detail-label">أجر المكان:</span>
                                            <span class="detail-value">{{ number_format($dailyPrice ?? 0, 2) }} دينار / يوم</span>
                                        </div>
                                        <div class="transaction-detail-row">
                                            <span class="detail-label">إجمالي قيمة الحجز:</span>
                                            <span class="detail-value">{{ number_format($total ?? 0, 2) }} دينار</span>
                                        </div>
                                        @if(!is_null($commission))
                                        <div class="transaction-detail-row">
                                            <span class="detail-label">عمولة المنصة:</span>
                                            <span class="detail-value">{{ number_format($commission, 2) }} دينار</span>
                                        </div>
                                        @endif
                                        <div class="transaction-detail-row">
                                            <span class="detail-label">تاريخ العملية:</span>
                                            <span class="detail-value">{{ optional($tx->created_at)->format('Y-m-d') }}</span>
                                        </div>
                                        <div class="transaction-detail-row">
                                            <span class="detail-label">وقت العملية:</span>
                                            <span class="detail-value">{{ optional($tx->created_at)->format('h:i A') }}</span>
                                        </div>
                                    @else
                                        <div class="transaction-detail-row">
                                            <span class="detail-label">نوع العملية:</span>
                                            <span class="detail-value">{{ $typeLabel }}</span>
                                        </div>
                                        <div class="transaction-detail-row">
                                            <span class="detail-label">تاريخ العملية:</span>
                                            <span class="detail-value">{{ optional($tx->created_at)->format('Y-m-d') }}</span>
                                        </div>
                                        <div class="transaction-detail-row">
                                            <span class="detail-label">وقت العملية:</span>
                                            <span class="detail-value">{{ optional($tx->created_at)->format('h:i A') }}</span>
                                        </div>
                                        @if($reason)
                                        <div class="transaction-detail-row">
                                            <span class="detail-label">السبب:</span>
                                            <span class="detail-value">{{ $reason }}</span>
                                        </div>
                                        @endif
                                        @if(isset($meta['note']))
                                        <div class="transaction-detail-row">
                                            <span class="detail-label">ملاحظة:</span>
                                            <span class="detail-value">{{ $meta['note'] }}</span>
                                        </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="transaction-item">
                                <div class="transaction-top">
                                    <div class="transaction-amount positive">
                                        <i class="fas fa-dollar-sign"></i>
                                        <span>0 دينار</span>
                                    </div>
                                </div>
                                <div class="transaction-details">
                                    <div class="transaction-detail-row">
                                        <span class="detail-label">لا توجد عمليات حديثة</span>
                                        <span class="detail-value">—</span>
                                    </div>
                                </div>
                            </div>
                        @endforelse

                        @foreach(($pointsTransactions ?? collect()) as $ptx)
                            @php
                                $meta = is_array($ptx->meta) ? $ptx->meta : ($ptx->meta ? json_decode($ptx->meta, true) : []);
                                $reason = $meta['reason'] ?? '—';
                            @endphp
                            <div class="transaction-item">
                                <div class="transaction-top">
                                    <div class="transaction-amount points">
                                        <i class="fas fa-gift"></i>
                                        <span>{{ number_format($ptx->amount, 2) }} دينار</span>
                                    </div>
                                    <button class="transaction-toggle" type="button" aria-label="عرض التفاصيل">
                                        <span class="toggle-icon">▾</span>
                                    </button>
                                </div>
                                <div class="transaction-details">
                                    <div class="transaction-detail-row">
                                        <span class="detail-label">نوع:</span>
                                        <span class="detail-value">{{ $ptx->type === 'gift' ? 'هدية' : 'ائتمان' }}</span>
                                    </div>
                                    <div class="transaction-detail-row">
                                        <span class="detail-label">السبب:</span>
                                        <span class="detail-value">{{ $reason }}</span>
                                    </div>
                                    <div class="transaction-detail-row">
                                        <span class="detail-label">تاريخ العملية:</span>
                                        <span class="detail-value">{{ optional($ptx->created_at)->format('Y-m-d') }}</span>
                                    </div>
                                    <div class="transaction-detail-row">
                                        <span class="detail-label">وقت العملية:</span>
                                        <span class="detail-value">{{ optional($ptx->created_at)->format('h:i A') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>



    <style>
        .section-header {
            background-color: #e9f4ff; /* لبني فاتح */
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            padding: 8px 16px;
            border-bottom: 1px solid #d5e8f7; /* خط بسيط للفصل */
        }
        /* Suspend Card */
        .suspend-card { background:#fff; border-radius:12px; box-shadow:0 12px 30px rgba(0,0,0,0.12); border:1px solid #E5E7EB; overflow:hidden; direction:rtl; position:fixed; top:90px; right:16px; width:520px; z-index:1000; }
        .suspend-top { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:14px 16px; border-bottom:1px solid #F3F4F6; }
        .suspend-user-name { font-size:18px; font-weight:600; color:#1a1a1a; }
        .suspend-user-email { font-size:14px; color:#6b7280; }
        .suspend-avatar img { width:56px; height:56px; border-radius:50%; object-fit:cover; }
        .suspend-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; padding:16px; }
        .suspend-field-label { text-align:center; color:#6b7280; margin-bottom:8px; font-size:15px; }
        .select-wrapper { position:relative; }
        .suspend-select { width:100%; height:48px; border:1px solid #D1D5DB; border-radius:12px; padding:12px 16px; background:#fff; color:#374151; font-size:15px; appearance:none; }
        .suspend-select:focus { outline:none; border-color:#9CA3AF; }
        .select-wrapper i { position:absolute; left:16px; top:50%; transform:translateY(-50%); color:#6b7280; pointer-events:none; }
        .suspend-actions { display:flex; justify-content:flex-start; padding:0 16px 16px; gap:10px; }
        .btn-suspend-confirm { display:inline-flex; align-items:center; gap:6px; padding:10px 16px; background:#E5E7EB; border:1px solid #D1D5DB; border-radius:10px; color:#111827; font-weight:600; font-size:13px; cursor:not-allowed; }
        .btn-suspend-confirm.enabled { background:#F3F4F6; cursor:pointer; }
        .btn-activate-confirm { display:inline-flex; align-items:center; gap:6px; padding:10px 16px; background:#10B981; border:1px solid #10B981; border-radius:10px; color:#fff; font-weight:600; font-size:13px; cursor:pointer; }
        @media (max-width:768px){ .suspend-card { right:8px; left:8px; width:auto; } .suspend-grid { grid-template-columns:1fr; } }
        .back-btn {
            background: #ffffff;
            color: #111111;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            padding: 8px 12px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
        }

        .back-btn i {
            color: #111111;
            font-size: 14px;
        }
        .back-btn:hover {
            background: #ffffff;
            border-color: #D1D5DB;
            color: #000000;
            text-decoration: none;
        }
        .property-item-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 8px 12px;
            width: 100%;
            max-width: 360px;
            direction: rtl;
        }

        .property-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
        }

        .property-image-wrapper img {
            width: 130px;
            height: 100px;
            border-radius: 8px;
            object-fit: cover;
        }

        .property-info-section {
            flex: 1;
        }

        .property-title-text {
            color: #1E3A8A;
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .property-location-text {
            color: #6B7280;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .property-meta-row {
            font-size: 13px;
            color: #374151;
            margin-bottom: 6px;
        }

        .property-meta-row .meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .property-price-row {
            font-size: 14px;
            font-weight: 600;
            color: #1E3A8A;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .property-actions-row {
            display: flex;
            justify-content: space-between;
            margin-top: 8px;
        }

        .btn-property-action {
            flex: 1;
            padding: 5px 10px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            border: 1.5px solid transparent;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            transition: 0.3s;
        }

        .btn-stop {
            border-color: #EF4444;
            color: #EF4444;
            background: rgba(239, 68, 68, 0.08);
        }

        .btn-delete {
            border-color: #dc2626;
            color: #fff;
            background: #dc2626;
        }

        .btn-property-action:hover {
            opacity: 0.9;
        }

        /* Matched user profile card styles */
        .profile-card-container {
            display: flex;
            flex-direction: column;
            gap: 16px;
            background-color: #f8f9fa;
            padding: .5rem;
        }

        .profile-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 0;
            width: 100%;
            direction: rtl;
        }

        .profile-side {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            padding: 12px;
            margin-top: -150px; /* يرفع الصورة والزر لأعلى */
        }

        .profile-image img {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e0e0e0;
        }

        .profile-action .btn-outline-danger {
            font-size: 0.85rem;
            border-radius: 8px;
            padding: 0.4rem 0.8rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .profile-info {
            flex: 1;
            margin-right: .5rem;
        }

        .profile-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.8rem;
            padding: 10px;
        }

        .profile-name {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a1a;
        }

        .profile-date {
            font-size: 0.85rem;
            color: #888;
        }

        .profile-details li {
            font-size: 0.95rem;
            color: #333;
            padding: 0.3rem 0;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #f0f0f0;
        }
        .profile-details li:last-child { border-bottom: none; }
        .profile-details i { color: #007bff; margin-left: 0.6rem; font-size: 1rem; }

        /* Stats row styles (under name) */
        .stats-row {
            display: flex;
            gap: 18px;
            padding: 0 10px 10px;
            margin-left: 50px;
        }
        .stat-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #333;
            background: #f7f9fc;
            border-radius: 10px;
            padding: 6px 10px;
        }
        .stat-icon { color: #ffb100; }
        .stat-value { font-weight: 700; }
        .stat-label { color: #666; }

        @media (max-width: 768px) {
            .profile-card-container { padding: 0; gap: 10px; }
            .profile-image img { width: 90px; height: 90px; }
            .profile-name { font-size: 16px; }
        }
        /* General Page Styles */
        .container-fluid {
            background: #F9FAFB;
            min-height: 100vh;
        }

        /* Page Header */
        .page-header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .page-main-title {
            font-size: 24px;
            font-weight: 700;
            color: #1F2937;
            margin: 0;
        }

        .btn-back-to-list {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: white;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            color: #374151;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-back-to-list:hover {
            background: #F9FAFB;
            border-color: #D1D5DB;
        }

        /* Profile Main Card */
        .profile-main-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 32px;
            margin-bottom: 24px;
        }

        .profile-header-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 24px;
            margin-bottom: 24px;
        }

        .profile-main-info {
            flex: 1;
            text-align: right;
        }

        .user-main-name {
            font-size: 24px;
            font-weight: 700;
            color: #1F2937;
            margin: 0 0 8px 0;
        }

        .registration-date {
            font-size: 13px;
            color: #6B7280;
            margin-bottom: 16px;
        }

        .date-label {
            margin-left: 4px;
        }

        .date-value {
            color: #1F2937;
            font-weight: 500;
        }

        .stats-row {
            display: flex;
            gap: 24px;
            justify-content: flex-end;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .stat-icon {
            color: #6B7280;
            font-size: 14px;
        }

        .stat-value {
            font-size: 18px;
            font-weight: 700;
            color: #1F2937;
        }

        .stat-label {
            font-size: 13px;
            color: #6B7280;
        }

        .profile-avatar-section {
            flex-shrink: 0;
        }

        .profile-main-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .avatar-placeholder-main {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Profile Details */
        .profile-details-section {
            padding: 24px 0;
            border-top: 1px solid #F3F4F6;
            border-bottom: 1px solid #F3F4F6;
        }

        .detail-row-item {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            padding: 12px 0;
        }

        .detail-row-icon {
            font-size: 18px;
            color: #6B7280;
            width: 20px;
            text-align: center;
        }

        .icon-verified {
            color: #2563EB !important;
        }

        .detail-row-text {
            font-size: 15px;
            color: #1F2937;
        }

        /* Profile Actions */
        .profile-actions-section {
            padding-top: 24px;
            display: flex;
            justify-content: center;
        }

        .btn-suspend-user {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 32px;
            background: white;
            border: 2px solid #EF4444;
            border-radius: 10px;
            color: #EF4444;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-suspend-user:hover {
            background: #EF4444;
            color: white;
        }

        /* Active Properties Section */
        .active-properties-section {
            background: white;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .section-header-title {
            font-size: 18px;
            font-weight: 700;
            color: #1F2937;
            margin: 10px 10px 20px 10px;
            text-align: right;
        }

        .properties-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .property-item-card {
            background: white;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s;
        }

        .property-item-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .property-image-wrapper {
            width: 100px;
            height: 60px;
            overflow: hidden;
        }

        .property-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .property-info-section {
            padding: 10px;
        }

        .property-title-text {
            font-size: 15px;
            font-weight: 600;
            color: #2563EB;
            margin: 0 0 4px 0;
        }

        .property-location-text {
            font-size: 12px;
            color: #6B7280;
            margin: 0 0 8px 0;
        }

        .property-meta-row {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 8px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #6B7280;
        }

        .meta-item i {
            color: #9CA3AF;
            font-size: 13px;
        }

        .property-price-row {
            display: flex;
            align-items: baseline;
            gap: 6px;
            margin-bottom: 16px;
        }

        .price-value {
            font-size: 18px;
            font-weight: 700;
            color: #1F2937;
        }

        .price-unit {
            font-size: 12px;
            color: #2563EB;
            font-weight: 500;
        }

        .property-actions-row {
            display: flex;
            gap: 4px;
        }

        .btn-property-action {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 5px 9px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: 1px solid;
        }

        .btn-stop {
            background: white;
            color: #DC2626;
            border-color: #DC2626;
        }

        .btn-stop:hover {
            background: #DC2626;
            color: white;
        }

        .btn-delete {
            background: white;
            color: #EF4444;
            border-color: #EF4444;
        }

        .btn-delete:hover {
            background: #EF4444;
            color: white;
        }

        /* Reviews Main Card */
        .reviews-main-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .reviews-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .reviews-main-title {
            font-size: 20px;
            font-weight: 700;
            color: #1F2937;
            margin: 8px;
        }

        .reviews-count-link {
            font-size: 14px;
            color: #2563EB;
            text-decoration: none;
        }

        .reviews-count-link:hover {
            text-decoration: underline;
        }

        /* Rating Bars */
        .rating-bars-section {
            margin-bottom: 32px;
        }

        .rating-bar-row {
            display: grid;
            grid-template-columns: 200px 1fr 50px;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
        }

        .rating-bar-label {
            font-size: 14px;
            color: #374151;
            text-align: right;
        }

        .rating-bar-wrapper {
            flex: 1;
        }

        .rating-bar-bg {
            width: 100%;
            height: 8px;
            background: #E5E7EB;
            border-radius: 10px;
            overflow: hidden;
        }

        .rating-bar-fill {
            height: 100%;
            background: #2563EB;
            border-radius: 10px;
            transition: width 0.3s ease;
        }

        .rating-bar-value {
            font-size: 14px;
            font-weight: 700;
            color: #1F2937;
            text-align: center;
        }

        /* Reviews List */
        .reviews-list-section {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .review-item-box {
            padding: 20px;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
        }

        .review-header-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
        }

        .reviewer-info-section {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .reviewer-avatar-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
        }

        .reviewer-avatar-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .reviewer-details {
            flex: 1;
        }

        .reviewer-name-text {
            font-size: 15px;
            font-weight: 600;
            color: #1F2937;
            margin: 0 0 4px 0;
        }

        .review-stay-date {
            font-size: 12px;
            color: #6B7280;
            margin: 0;
        }

        .review-rating-badge {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .review-rating-badge i {
            color: #FDB022;
            font-size: 16px;
        }

        .review-rating-badge span {
            font-size: 15px;
            font-weight: 700;
            color: #1F2937;
        }

        .review-time-text {
            font-size: 12px;
            color: #9CA3AF;
            margin-bottom: 12px;
        }

        .review-comment-text {
            font-size: 14px;
            color: #4B5563;
            line-height: 1.7;
            text-align: justify;
            margin: 0;
        }

        /* Profits Card */
        .profits-main-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-top: 16px;
        }

        .profits-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .profits-main-title {
            font-size: 18px;
            font-weight: 700;
            color: #1F2937;
            margin: 0;
        }

        .profits-stats-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .profit-stat-box {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            padding: 14px 16px;
        }

        .profit-stat-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: #EFF6FF;
            color: #2563EB;
            font-size: 18px;
        }

        .profit-stat-content {
            display: flex;
            flex-direction: column;
            padding: 10px;
            gap: 1px; /* بدل 2px */
            font-size: 13px; /* تصغير حجم النص */
            line-height: 1.2; /* تقليل المسافة الرأسية بين الأسطر */
        }


        .profit-stat-label {
            font-size: 13px;
            color: #6B7280;
        }

        .profit-stat-value {
            font-size: 20px;
            font-weight: 700;
            color: #1F2937;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .profile-header-section {
                flex-direction: column-reverse;
                align-items: center;
                text-align: center;
            }

            .profile-main-info {
                text-align: center;
            }

            .stats-row {
                justify-content: center;
            }

            .rating-bar-row {
                grid-template-columns: 150px 1fr 50px;
                gap: 12px;
            }

            .profits-stats-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767px) {
            .page-header-section {
                flex-direction: column;
                gap: 16px;
            }

            .btn-back-to-list {
                width: 100%;
                justify-content: center;
            }

            .properties-grid {
                grid-template-columns: 1fr;
            }

            .rating-bar-row {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .property-actions-row {
                flex-direction: column;
            }
        }
    </style>

    <style>
        .avg-rating-badge { display:inline-block; background:#F3F4F6; color:#111827; font-weight:700; border-radius:8px; padding:4px 8px; margin-inline-start:8px; }
        .modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,0.4); display:none; align-items:center; justify-content:center; z-index:1000; }
        .modal-content { background:#fff; border-radius:12px; width:min(720px, 92vw); max-height:80vh; overflow:auto; box-shadow:0 10px 30px rgba(0,0,0,0.15); }
        .modal-header { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-bottom:1px solid #F3F4F6; }
        .modal-title { font-size:18px; font-weight:700; color:#1F2937; }
        .close-btn { border:none; background:#F9FAFB; border-radius:8px; width:32px; height:32px; cursor:pointer; }
        .modal-body { padding:16px; }
    </style>

    <div class="modal-overlay" id="reviewsModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">كل المراجعات ({{ $totalReviews ?? 0 }})</h3>
                <button class="close-btn" type="button" id="closeReviewsModal">×</button>
            </div>
            <div class="modal-body">
                @forelse(($reviews ?? collect()) as $review)
                    <div class="review-item-box" style="border-bottom:1px solid #F3F4F6; padding-bottom:12px; margin-bottom:12px;">
                        <div class="review-header-row">
                            <div class="reviewer-info-section">
                                <div class="reviewer-details">
                                    <h4 class="reviewer-name-text">{{ $review->reviewer_name ?? 'اسم المستأجر' }}</h4>
                                    <div class="small text-muted">{{ optional($review->created_at)->format('Y-m-d') ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="review-rating-badge">
                                <i class="fas fa-star"></i>
                                <span>{{ $review->rating ?? 0 }}</span>
                            </div>
                        </div>
                        <p class="review-comment-text">{{ $review->comment ?? '' }}</p>
                    </div>
                @empty
                    <div class="text-muted">لا توجد مراجعات</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Transactions card styles and toggle script -->
    <style>
        .transactions-main-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-top: 16px;
        }
        .transactions-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #E5E7EB;
            gap: 16px;
            padding: 16px;
            border-radius: 12px;
            margin: 8px;
            transition: all 0.3s;
        }
        .transactions-main-title { font-size: 18px; color: #1F2937; font-weight: 700; margin: 0; }
        .transactions-filter-btn { border: 1px solid #E5E7EB; background: #F9FAFB; color: #374151; border-radius: 10px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; }
        .transactions-list { display: flex; flex-direction: column; gap: 8px; }
        .transaction-item { background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 12px; overflow: hidden; }
        .transaction-top { display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; }
        .transaction-amount { display: flex; align-items: center; gap: 8px; font-weight: 600; }
        .transaction-amount i { font-size: 16px; }
        .transaction-amount span { font-size: 15px; }
        .transaction-amount.positive { color: #16a34a; }
        .transaction-amount.negative { color: #dc2626; }
        .transaction-amount.points { color: #7c3aed; }
        .transaction-toggle { background: transparent; border: none; color: #6b7280; font-size: 16px; }
        .transaction-details { display: none; border-top: 1px solid #edf2f7; padding: 8px 10px; }
        .transaction-item.open .transaction-details { display: block; }
        .transaction-detail-row { display: flex; align-items: center; justify-content: space-between; padding: 3px 0; }
        .detail-label { color: #6b7280; font-size: 12px; }
        .detail-value { color: #111827; font-weight: 600; font-size: 13px; }
        @media (max-width: 768px) { .transactions-main-title { font-size: 15px; } .transaction-detail-row { flex-direction: column; align-items: flex-start; gap: 4px; } }
    </style>

        <script>
            (function(){
            // Toggle transactions
            document.addEventListener('click', function(e){
                var btn = e.target.closest('.transaction-toggle');
                if (btn) {
                    var item = btn.closest('.transaction-item');
                    if (item) item.classList.toggle('open');
                }
            });

                // Toggle suspend card
                var suspendToggle = document.querySelector('.btn-suspend-toggle');
                var card = document.getElementById('suspend-card');
                var duration = document.getElementById('suspend-duration-select');
                var reason = document.getElementById('suspend-reason-select');
                var applyBtn = document.getElementById('suspend-apply-btn');
                function updateState(){
                    var ready = duration && reason && duration.value && reason.value;
                    if (applyBtn) {
                        applyBtn.disabled = !ready;
                        applyBtn.classList.toggle('enabled', !!ready);
                    }
                }
                if (suspendToggle && card) {
                    suspendToggle.addEventListener('click', function(e){
                        e.stopPropagation();
                        var hidden = card.style.display === '' || card.style.display === 'none';
                        card.style.display = hidden ? 'block' : 'none';
                        if (hidden) { card.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
                    });
                }
                if (duration && reason) {
                    duration.addEventListener('change', updateState);
                    reason.addEventListener('change', updateState);
                }
                document.addEventListener('click', function(e){
                    if (card && card.style.display === 'block' && !card.contains(e.target) && !e.target.closest('.btn-suspend-toggle')) {
                        card.style.display = 'none';
                    }
                });

                var openBtn = document.querySelector('.view-all-link');
                var modal = document.getElementById('reviewsModal');
                var closeBtn = document.getElementById('closeReviewsModal');
                if (openBtn && modal) {
                    openBtn.addEventListener('click', function(e){ e.preventDefault(); modal.style.display = 'flex'; });
                }
                if (closeBtn && modal) {
                    closeBtn.addEventListener('click', function(){ modal.style.display = 'none'; });
                }
                if (modal) {
                    modal.addEventListener('click', function(e){ if (e.target === modal) { modal.style.display = 'none'; } });
                }
            })();
        </script>
@endsection
