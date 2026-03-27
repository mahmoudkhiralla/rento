@extends('dashboard.layouts.app')

@section('title', 'تفاصيل المستخدم')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="page-header mb-4 d-flex justify-content-between align-items-center">
            <h4 class="page-title m-0">تفاصيل حساب مستخدم</h4>
            <a href="{{ route('dashboard.users.list') }}" class="btn back-btn">
                <i class="fas fa-chevron-right"></i>
                عودة الي صفحة المستخدمين
            </a>
        </div>
        <!-- كارد تعليق الحساب مستقل خارج أي كارد -->
        <div id="suspend-card" class="suspend-card" style="display:none;" aria-hidden="true">
            <div class="suspend-top">
                <div class="suspend-user">
                    <div class="suspend-user-name">{{ $user->name ?? 'اسم المستخدم' }}</div>
                    <div class="suspend-user-email">{{ $user->email ?? 'user@example.com' }}</div>
                </div>
                <div class="suspend-avatar">
                    @php
                        $avatarForSuspend = !empty($user->avatar)
                            ? (\Illuminate\Support\Str::startsWith($user->avatar, ['http://','https://'])
                                ? $user->avatar
                                : asset('storage/' . $user->avatar))
                            : ('https://i.pravatar.cc/160?u=' . urlencode($user->email ?? ($user->id ?? uniqid())));
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
                                <option value="" selected disabled>اختر سبب التعليق</option>
                                <option value="property_damage">إتلاف ممتلكات</option>
                                <option value="tos_violation">انتهاك اتفاقية الاستخدام</option>
                                <option value="repeated_cancellation">إلغاء متكرر</option>
                                <option value="misconduct">سوء معاملة</option>
                                <option value="misleading_info">تقديم معلومات غير دقيقة أو مضللة</option>
                                <option value="fraud_suspicion">محاولة الاحتيال / أنشطة مشبوهة</option>
                            </select>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                </div>
                <div class="suspend-actions">
                    <button id="suspend-apply-btn" class="btn btn-suspend-confirm" type="submit" disabled>تعليق الحساب</button>
                </div>
            </form>
        </div>
        <div class="row g-4 align-items-start">
            <div class="col-lg-6 col-xl-6 profile-card-container">
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
                            <button class="btn btn-suspend">
                                <i class="fas fa-ban"></i>
                                إيقاف / تعليق
                            </button>
                        </div>
                    </div>

                    <!-- المحتوى -->
                    <div class="profile-info">
                        <div class="profile-header">
                            <div class="profile-name">{{ $user->name ?? 'اسم المستخدم' }}</div>
                            <p class="profile-date">تاريخ التسجيل:
                                <span class="date-value">{{ optional($user->created_at)->format('Y/m/d') ?? '-' }}</span>
                            </p>
                        </div>

                        <ul class="profile-details list-unstyled">
                            <li><i class="fas fa-briefcase"></i> يعمل في {{ $user->job ?? 'غير محدد' }}</li>
                            <li><i class="fas fa-home"></i> يسكن في مدينة {{ $user->city ?? 'غير محدد' }}</li>
                            <li><i class="fas fa-user-tag"></i> نوع المستخدم: {{ $user->user_type === 'tenant' ? 'مستأجر' : ($user->user_type === 'landlord' ? 'مؤجر' : 'كلاهما') }}</li>
                            <li><i class="fas fa-paw"></i> {{ $user->has_pet ? 'يمتلك حيوان أليف' : 'لا يمتلك حيوان أليف' }}</li>
                            <li><i class="fas fa-id-card"></i> إثبات الشخصية: {{ $user->id_verified ? 'مُوثَّق' : 'غير مُوثَّق' }}</li>
                            <li><i class="fas fa-user-check"></i> التعرف على الوجه: {{ $user->face_verified ? 'تم' : 'لم يتم' }}</li>
                            <li><i class="fas fa-bolt"></i> الحالة: {{ $user->status ?? 'غير محدد' }}</li>
                            <li><i class="fas fa-certificate"></i> مؤثر: {{ $user->is_influencer ? 'نعم' : 'لا' }}</li>
                            <li><i class="fas fa-sync-alt"></i> يحتاج تجديد: {{ $user->needs_renewal ? 'نعم' : 'لا' }}</li>
                        </ul>
                    </div>
                </div>
                <div class="previous-card mt-1">
                    <div class="previous-properties">
                        <div class="section-header">
                            <h3 class="section-title">الأماكن السابقة</h3>
                        </div>
                        @forelse ($previousProperties ?? [] as $property)
                            <div class="property-card">
                                <!-- الصورة يسارًا -->
                                <div class="property-image">
                                    @if($property->image)
                                        @php $img = $property->image; $isUrl = \Illuminate\Support\Str::startsWith($img, ['http://', 'https://']); $isLocalPath = \Illuminate\Support\Str::startsWith($img, ['/', 'images/', 'img/']); @endphp
                                        @if($isUrl)
                                            <img src="{{ $img }}" alt="{{ $property->name }}">
                                        @elseif($isLocalPath)
                                            <img src="{{ asset($img) }}" alt="{{ $property->name }}">
                                        @else
                                            <img src="{{ asset('storage/' . $img) }}" alt="{{ $property->name }}">
                                        @endif
                                    @else
                                        <div class="property-placeholder">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif
                                </div>
                                <!-- الاسم والمكان يسار (نص بمحاذاة بداية) -->
                                <div class="property-right text-start">
                                    <h4 class="property-name mb-1">{{ $property->name ?? 'اسم العقار' }}</h4>
                                    <div class="property-location text-muted">{{ $property->location ?? 'طرابلس' }}</div>
                                </div>
                                <!-- التقييم والسعر يمين (نص بمحاذاة نهاية) -->
                                <div class="property-left text-end">
                                    <div class="property-rating mb-1">
                                        <span>{{ $property->rating ?? '0' }}</span>
                                        <i class="fas fa-star text-warning"></i>
                                    </div>
                                    <div class="property-price">
                                        <span class="price-value fw-bold">{{ $property->price ?? '0' }}</span>
                                        <span class="price-unit text-muted">د.ل / ليلة</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state text-center py-3">
                                <i class="fas fa-home" style="font-size: 22px; color: #888;"></i>
                                <p class="mt-2 mb-0">لا توجد أماكن سابقة</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-xl-6">
                <div class="reviews-card compact">
                    <!-- Reviews Header -->
                    <div class="section-header">

                    <div class="reviews-header">
                        <h2 class="reviews-title">التقييم</h2>
                        <span class="avg-rating-badge">{{ number_format($avgRating ?? 0, 1) }}</span>
                        <a href="#" class="view-all-link">
                            قراءة ({{ $totalReviews ?? 0 }}) مراجعة
                        </a>
                    </div>
                    </div>

                    <!-- Rating Bars -->
                    <div class="rating-bars">
                        <div class="rating-bar-item">
                            <div class="rating-bar-wrapper">
                                <span class="rating-badge">{{ number_format(($ratings['inquiry_response'] ?? 0), 1) }}</span>
                                <div class="rating-bar">
                                    <div class="rating-bar-fill" style="width: {{ (($ratings['inquiry_response'] ?? 0) * 20) }}%"></div>
                                </div>
                                <span class="rating-label">الرد على الاستفسارات</span>
                            </div>
                        </div>

                        <div class="rating-bar-item">
                            <div class="rating-bar-wrapper">
                                <span class="rating-badge">{{ number_format(($ratings['booking_acceptance_speed'] ?? 0), 1) }}</span>
                                <div class="rating-bar">
                                    <div class="rating-bar-fill" style="width: {{ (($ratings['booking_acceptance_speed'] ?? 0) * 20) }}%"></div>
                                </div>
                                <span class="rating-label">سرعة قبول الحجز</span>
                            </div>
                        </div>

                        <div class="rating-bar-item">
                            <div class="rating-bar-wrapper">
                                <span class="rating-badge">{{ number_format(($ratings['timely_delivery'] ?? $avgRating ?? 0), 1) }}</span>
                                <div class="rating-bar">
                                    <div class="rating-bar-fill" style="width: {{ (($ratings['timely_delivery'] ?? $avgRating ?? 0) * 20) }}%"></div>
                                </div>
                                <span class="rating-label">التسليم في الموعد</span>
                            </div>
                        </div>
                    </div>

                    <!-- Reviews List -->
                    <div class="reviews-list">
                        @forelse ($reviews ?? [] as $review)
                            <div class="review-item">
                                <div class="review-header-info">
                                    <div class="reviewer-info">
                                        <div class="reviewer-avatar">
                                            @if(!empty($review->reviewer_avatar_src))
                                                <img src="{{ $review->reviewer_avatar_src }}" alt="{{ $review->reviewer_name }}">
                                            @else
                                                <div class="reviewer-placeholder">
                                                    {{ substr($review->reviewer_name ?? 'M', 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="reviewer-details">
                                            <h4 class="reviewer-name">{{ $review->reviewer_name ?? 'اسم المستأجر' }}</h4>
                                        </div>
                                    </div>
                                    <div class="review-rating">
                                        <i class="fas fa-star"></i>
                                        <span>{{ $review->rating ?? '0' }}</span>
                                    </div>
                                </div>
                                <div class="review-meta">
                                    <span>اليوم: {{ optional($review->created_at)->diffForHumans() ?? '12:05 م' }}</span>
                                </div>
                                <p class="review-text">
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
            </div>
        </div>
    </div>

    <style>
        /* Suspend Card */
        .suspend-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border: 1px solid #E5E7EB;
            overflow: hidden;
            direction: rtl;
        }

        .suspend-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
        }

        .suspend-user {
            text-align: right;
        }

        .suspend-user-name {
            font-size: 20px;
            font-weight: 600;
            color: #1a1a1a;
        }

        .suspend-user-email {
            font-size: 14px;
            color: #6b7280;
        }

        .suspend-avatar img {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
        }

        .suspend-content {
            padding: 12px 16px;
            border-top: 1px solid #F3F4F6;
        }

        .suspend-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .suspend-label {
            min-width: 160px;
            color: #6b7280;
            font-size: 14px;
        }

        .suspend-actions {
            padding-top: 4px;
        }

        .btn-suspend-confirm {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            background: #E5E7EB;
            border: 1px solid #D1D5DB;
            border-radius: 10px;
            color: #111827;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-suspend-confirm:hover {
            background: #D1D5DB;
        }
        .property-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            direction: rtl; /* علشان اليمين يبقى البداية */
            margin-bottom: 10px;
        }

        .property-info {
            flex: 1;
            text-align: right;
        }

        .property-meta {
            text-align: left;
            flex-shrink: 0;
        }

        .property-rating i {
            color: #fbbf24;
            margin-left: 4px;
        }

        .property-price {
            font-size: 15px;
            color: #1a3d6f;
        }

        .property-name {
            font-weight: 600;
            color: #1a1a1a;
        }

        .property-location {
            font-size: 14px;
            color: #6b7280;
        }
        .section-header {
            background-color: #e9f4ff; /* لبني فاتح */
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            padding: 8px 16px;
            border-bottom: 1px solid #d5e8f7; /* خط بسيط للفصل */
        }

        .section-title {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            color: #1a3d6f; /* أزرق غامق مشابه للصورة */
            text-align: right;
        }
        .reviews-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 680px; /* نفس الـ profile-card */
            height: 100%; /* يخليها تاخد نفس ارتفاع الكولم */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .reviews-card {
            padding: 0;
            max-width: 640px;
        }
        .row.align-items-stretch {
            align-items: stretch !important;
        }

        .profile-side {
            display: flex;
            flex-direction: column; /* يخليهم فوق بعض */
            align-items: center; /* يوسّط الصورة والزر */
            gap: 15px; /* مسافة بسيطة بينهم */
            margin-top: -150px; /* يرفع الصورة والزر لأعلى */
        }

        /* الصورة */
        .profile-image img {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e0e0e0;
        }

        /* الزر */
        .profile-action .btn-outline-danger {
            font-size: 0.85rem;
            border-radius: 8px;
            padding: 0.4rem 0.8rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }
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


        .profile-image img {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            object-fit: cover;
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

        .profile-details li:last-child {
            border-bottom: none;
        }

        .profile-details i {
            color: #007bff;
            margin-left: 0.6rem;
            font-size: 1rem;
        }

        .profile-action {
            margin-right: 1rem;
        }

        .btn-outline-danger {
            font-size: 0.85rem;
            border-radius: 8px;
            padding: 0.4rem 0.8rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        /* Back Button Styles */
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

        /* Profile Card Styles */
        .profile-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .profile-header {
            text-align: center;
            background: linear-gradient(to bottom, #f8f9fa 0%, white 100%);
        }

        .profile-avatar-large {
            width: 100px;
            height: 100px;
            margin: 0 auto 20px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .profile-avatar-large img {
            width: 70%;
            height: 70%;
            object-fit: cover;
        }

        .avatar-placeholder-large {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
            font-weight: 600;
        }

        .profile-name {
            font-size: 20px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: -15px;
        }

        .profile-date {
            font-size: 14px;
            color: #666;
            margin-bottom: -15px;
        }

        .profile-date .date-value {
            color: #1a1a1a;
            font-weight: 500;
        }

        .btn-suspend {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 16px; /* ← كان 10px 24px */
            background: rgba(255, 60, 60, 0.07);
            border: 2px solid #EF4444;
            border-radius: 8px;
            color: #EF4444;
            font-weight: 600;
            font-size: 13px; /* ← كان 14px */
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-suspend:hover {
            background: #EF4444;
            color: white;
        }

        /* Profile Details */
        .profile-details {
            padding: 30px;
            border-top: 1px solid #f0f0f0;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 0;
            border-bottom: 1px solid #f5f5f5;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-icon {
            font-size: 20px;
            color: #666;
            width: 24px;
            text-align: center;
        }

        .detail-item.verified .detail-icon {
            color: #2B7FE6;
        }

        .detail-item.inactive .detail-icon {
            color: #D0D0D0;
        }

        .detail-label {
            font-size: 15px;
            color: #1a1a1a;
        }

        /* Previous Properties */
        .previous-properties {
            border-top: 1px solid #f0f0f0;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 20px;
        }

        .property-card {
            display: flex;
            gap: 16px;
            padding: 16px;
            border: 1px solid #f0f0f0;
            border-radius: 12px;
            margin: 8px;
            transition: all 0.3s;
            align-items: center;            /* تمركز عمودي */
            justify-content: space-between; /* توزيع أفقي بين اليسار/اليمين/الصورة */
        }

        .property-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-color: #e0e0e0;
        }

        .property-image {
            flex-shrink: 0;
            width: 80px;
            height: 80px;
            border-radius: 10px;
            overflow: hidden;
        }

        .property-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .property-placeholder {
            width: 100%;
            height: 100%;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 24px;
        }

        /* أعمدة الكارد: يسار (تقييم/سعر) ويمين (اسم/مكان) */
        .property-left {
            flex: 0 0 28%;
            display: flex;
            flex-direction: column;
            align-items: flex-end;   /* بعد العكس: يمين */
            justify-content: center;
            text-align: end;
        }

        .property-right {
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            align-items: flex-start; /* بعد العكس: يسار */
            justify-content: center;
            text-align: start;
        }

        .property-rating {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 8px;
        }

        .property-rating i {
            color: #FDB022;
            font-size: 14px;
        }

        .property-rating span {
            font-weight: 600;
            color: #1a1a1a;
            font-size: 14px;
        }

        .property-name {
            font-size: 16px;
            font-weight: 600;
            color: #2B7FE6;
            margin-bottom: 4px;
        }

        .property-location {
            font-size: 13px;
            color: #666;
            margin-bottom: 8px;
        }

        .property-price {
            display: flex;
            align-items: baseline;
            gap: 4px;
        }

        .price-value {
            font-size: 16px;
            font-weight: 700;
            color: #1a1a1a;
        }

        .price-unit {
            font-size: 13px;
            color: #2B7FE6;
            font-weight: 500;
        }

        /* Reviews Card */
        .reviews-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            padding: 0;
        }

        .reviews-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .reviews-title {
            font-size: 22px;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0;
        }

        .view-all-link {
            color: #2B7FE6;
            font-size: 14px;
            text-decoration: none;
            font-weight: 500;
        }

        .view-all-link:hover {
            text-decoration: underline;
        }

        /* Rating Bars */
        .rating-bars {
            margin-bottom: 40px;
            padding: 8px;
        }

        .rating-bar-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .rating-label {
            font-size: 14px;
            color: #2B7FE6;
            min-width: 180px;
            text-align: start;
        }

        .rating-bar-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
            justify-content: space-between;
            flex-direction: row-reverse;
        }

        .rating-bar {
            flex: 1;
            height: 8px;
            background: #E5E7EB;
            border-radius: 10px;
            overflow: hidden;
        }

        .rating-bar-fill {
            height: 100%;
            background: #2B7FE6;
            border-radius: 10px;
            transition: width 0.3s ease;
        }

        .rating-value { display: none; }

        .rating-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #F3F4F6;
            color: #111827;
            font-weight: 700;
            font-size: 12px;
        }

        /* Reviews List */
        .reviews-list {
            display: flex;
            flex-direction: column;
            padding: 12px;
            gap: 10px;
        }

        .review-item {
            padding-bottom: 30px;
            border-bottom: 1px solid #f0f0f0;
        }

        .review-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .review-header-info {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
        }

        .reviewer-info {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .reviewer-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
        }

        .reviewer-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .reviewer-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 20px;
        }

        .reviewer-details {
            flex: 1;
        }

        .reviewer-name {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 4px;
        }

        .review-date {
            font-size: 13px;
            color: #666;
        }

        .review-rating {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .review-rating i {
            color: #FDB022;
            font-size: 16px;
        }

        .review-rating span {
            font-weight: 600;
            color: #1a1a1a;
            font-size: 15px;
        }

        .review-meta {
            margin-bottom: 12px;
            font-size: 13px;
            color: #999;
        }

        .review-text {
            font-size: 14px;
            color: #555;
            line-height: 1.7;
            text-align: justify;
            margin: 0;
        }

        /* Compact Reviews Card adjustments */
        .reviews-card.compact .reviews-header {
            margin-bottom: 16px;
        }
        .reviews-card.compact .reviews-title {
            font-size: 18px;
        }
        .reviews-card.compact .rating-bars {
            margin-bottom: 16px;
        }
        .reviews-card.compact .rating-bar-item {
            margin-bottom: 12px;
        }
        .reviews-card.compact .rating-bar {
            height: 6px;
        }

        /* Previous Properties Card */
        .previous-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .profile-card,
            .reviews-card {
                margin-bottom: 20px;
            }
        }

        @media (max-width: 768px) {
            .rating-bar-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .rating-bar-wrapper {
                width: 100%;
            }

            .property-card {
                flex-direction: column;
                align-items: stretch;
            }

            .property-left,
            .property-right {
                align-items: flex-start;
                text-align: start;
                width: 100%;
            }

            .property-image {
                width: 100%;
                height: 200px;
            }
            /* Suspend card responsive handled in standalone styles below */
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
                @forelse (($reviews ?? collect()) as $review)
                    <div class="review-item" style="border-bottom:1px solid #F3F4F6; padding-bottom:12px; margin-bottom:12px;">
                        <div class="review-header-info">
                            <div class="reviewer-info">
                                <div class="reviewer-details">
                                    <h4 class="reviewer-name">{{ $review->reviewer_name ?? 'اسم المستأجر' }}</h4>
                                    <div class="small text-muted">{{ optional($review->created_at)->format('Y-m-d') ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="review-rating">
                                <i class="fas fa-star"></i>
                                <span>{{ $review->rating ?? 0 }}</span>
                            </div>
                        </div>
                        <p class="review-text">{{ $review->comment ?? '' }}</p>
                    </div>
                @empty
                    <div class="text-muted">لا توجد مراجعات</div>
                @endforelse
            </div>
        </div>
    </div>
    <style>
        /* Suspend card: standalone full-width block */
        .container-fluid { position: relative; }
        .suspend-card {
            position: absolute;
            top: 72px; /* تحت الهيدر مباشرة */
            right: 16px;
            width: 520px;
            background: #fff;
            border: 1px solid #E5E7EB;
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
            padding: 0;
            direction: rtl;
            z-index: 1000;
        }
        .suspend-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 16px;
            border-bottom: 1px solid #F3F4F6;
        }
        .suspend-user-name { font-size: 22px; font-weight: 700; color: #111; }
        .suspend-user-email { font-size: 14px; color: #6b7280; }
        .suspend-avatar img { width: 56px; height: 56px; border-radius: 50%; object-fit: cover; }
        .suspend-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; padding: 16px; }
        .suspend-field-label { text-align: center; color: #6b7280; margin-bottom: 8px; font-size: 15px; }
        .select-wrapper { position: relative; }
        .suspend-select { width: 100%; height: 48px; border: 1px solid #D1D5DB; border-radius: 12px; padding: 12px 16px; background: #fff; color: #9CA3AF; font-size: 16px; appearance: none; }
        .suspend-select:focus { outline: none; border-color: #9CA3AF; }
        .select-wrapper i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #6b7280; pointer-events: none; }
        .suspend-actions { display: flex; justify-content: flex-start; padding: 0 16px 16px; }
        .btn-suspend-confirm { background: #E5E7EB; color: #111827; border: 1px solid #D1D5DB; border-radius: 10px; padding: 10px 16px; font-weight: 600; cursor: not-allowed; }
        .btn-suspend-confirm:disabled { opacity: 1; }
        .btn-suspend-confirm.enabled { background: #F3F4F6; cursor: pointer; }
        @media (max-width: 768px) { .suspend-card { right: 8px; left: 8px; width: auto; } .suspend-grid { grid-template-columns: 1fr; } }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.querySelector('.btn-suspend');
            const card = document.getElementById('suspend-card');
            const duration = document.getElementById('suspend-duration-select');
            const reason = document.getElementById('suspend-reason-select');
            const applyBtn = document.getElementById('suspend-apply-btn');

            function updateState() {
                const ready = duration && reason && duration.value && reason.value;
                if (applyBtn) {
                    applyBtn.disabled = !ready;
                    applyBtn.classList.toggle('enabled', !!ready);
                }
            }

            if (duration && reason) {
                duration.addEventListener('change', updateState);
                reason.addEventListener('change', updateState);
            }

            if (btn && card) {
                btn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const hidden = card.style.display === '' || card.style.display === 'none';
                    card.style.display = hidden ? 'block' : 'none';
                    if (hidden) {
                        card.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });

                // إغلاق عند الضغط خارج الكارد
                document.addEventListener('click', function (e) {
                    if (card.style.display === 'block' && !card.contains(e.target) && !btn.contains(e.target)) {
                        card.style.display = 'none';
                    }
                });
            }

            const openBtn = document.querySelector('.view-all-link');
            const modal = document.getElementById('reviewsModal');
            const closeBtn = document.getElementById('closeReviewsModal');
            if (openBtn && modal) {
                openBtn.addEventListener('click', function(e){ e.preventDefault(); modal.style.display = 'flex'; });
            }
            if (closeBtn && modal) {
                closeBtn.addEventListener('click', function(){ modal.style.display = 'none'; });
            }
            if (modal) {
                modal.addEventListener('click', function(e){ if (e.target === modal) { modal.style.display = 'none'; } });
            }
        });
    </script>
@endsection
