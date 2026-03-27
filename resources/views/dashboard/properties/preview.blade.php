@extends('dashboard.layouts.app')

@section('title', 'معاينة العقار')

@section('content')
    <div class="container-fluid px-4 py-4">
        <!-- تمت إزالة زر الرجوع من الهيدر ليظهر داخل الكارت -->

        <!-- Preview Card -->َ
        <div class="preview-card">
            <div class="preview-card-top">
                <div class="preview-card-title">معاينة العقار</div>
                <a href="{{ route('dashboard.properties.index') }}" class="btn back-btn back-btn-top" title="رجوع" aria-label="رجوع">
                    <i class="fas fa-times"></i>
                </a>
            </div>
            <!-- Top Gallery -->
            <div class="preview-gallery">
                <div class="gallery-thumbs">
                    @php
                        $thumbs = count($galleryImages) ? $galleryImages : [];
                        if (!count($thumbs)) {
                            $thumbs = array_fill(0, 6, $property->image ?? null);
                        }
                        // حد أقصى 6 صور مصغرة على اليسار
                        $thumbs = array_slice($thumbs, 0, 6);
                    @endphp
                    @foreach($thumbs as $img)
                        <div class="thumb">
                            @if($img)
                                @php
                                    $isUrl = \Illuminate\Support\Str::startsWith($img, ['http://','https://']);
                                    $isAbs = \Illuminate\Support\Str::startsWith($img, ['/']);
                                @endphp
                                @if($isUrl)
                                    <img src="{{ $img }}" alt="صورة معاينة">
                                @elseif($isAbs)
                                    <img src="{{ asset(ltrim($img, '/')) }}" alt="صورة معاينة">
                                @else
                                    <img src="{{ asset($img) }}" alt="صورة معاينة">
                                @endif
                            @else
                                <div class="thumb-placeholder"><i class="fas fa-image"></i></div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="gallery-main">
                    @php
                        $main = $property->image ?? null;
                        $mainUrl = $main
                            ? (\Illuminate\Support\Str::startsWith($main, ['http://','https://'])
                                ? $main
                                : asset(ltrim($main, '/')))
                            : asset('images/rento-logo.svg');
                    @endphp
                    <img src="{{ $mainUrl }}" alt="{{ $property->title }}" class="main-img">
                </div>
            </div>

            <!-- Title and quick facts -->
            <div class="preview-title-row">
                <div class="left">
                    <h3 class="property-name">{{ $property->title ?? 'اسم العقار' }}</h3>
                    @php
                        $specGuests = $property->capacity ?? 6; // قيمة افتراضية إن لم تتوفر
                        $specBedrooms = $property->bedrooms ?? 3; // قيمة افتراضية
                        $specBathrooms = $property->bathrooms ?? 2; // قيمة افتراضية
                    @endphp
                    <div class="property-specs">
                        <span class="spec-item"><i class="fas fa-users"></i> أفراد {{ $specGuests }}</span>
                        <span class="spec-divider">|</span>
                        <span class="spec-item"><i class="fas fa-bed"></i> {{ $specBedrooms }} غرفة نوم</span>
                        <span class="spec-divider">|</span>
                        <span class="spec-item"><i class="fas fa-bath"></i> {{ $specBathrooms }} حمام</span>
                    </div>
                    @php
                        $searchTags = [];
                        // استخدم الكلمات المفتاحية من قاعدة البيانات
                        $kw = $property->keywords ?? null;
                        if (is_array($kw)) {
                            $searchTags = array_values(array_unique(array_filter(array_map('trim', $kw))));
                        } elseif (is_string($kw) && trim($kw) !== '') {
                            $parts = preg_split('/[,;\n]+/', $kw);
                            foreach ($parts as $p) { $t = trim($p); if ($t !== '') { $searchTags[] = $t; } }
                            $searchTags = array_values(array_unique($searchTags));
                        }
                        // بدائل خفيفة في حال عدم توفر كلمات
                        if (!count($searchTags)) {
                            $searchTags = array_filter([
                                $type->name ?? null,
                                $property->city ?? null,
                            ]);
                        }
                    @endphp
                    <div class="tags-row">
                        @foreach($searchTags as $tag)
                            <a class="tag-chip"> {{ $tag }}</a>
                        @endforeach
                    </div>
                </div>
                <div class="right">
                        <div class="reviews-badge">تقييمات العملاء {{ number_format($previewData['rating'] ?? 0, 1) }} <i class="fas fa-star"></i></div>
                    <div class="status-badge {{ $previewData['published'] ? 'published' : 'unpublished' }}">
                        {{ $previewData['published'] ? 'منشور' : 'غير منشور' }}
                    </div>
                </div>
            </div>

            <!-- Info table -->
            <div class="info-table">
                <div class="table-row table-head">
                    <div class="cell cell-flex"><span class="head-label">اسم المؤجر</span></div>
                    <div class="cell cell-small">تعريف الوجه</div>
                    <div class="cell cell-small">إثبات الشخصية</div>
                    <div class="cell cell-small">التقييم العام</div>
                    <div class="cell cell-small">تاريخ العضوية</div>
                </div>
                <div class="table-row">
                    <div class="cell cell-flex">
                        <div class="client-info">
                            <div class="avatar">
                                @php
                                    $avatar = $landlord->avatar ?? null;
                                    $avatarSrc = $avatar
                                        ? (\Illuminate\Support\Str::startsWith($avatar, ['http://','https://'])
                                            ? $avatar
                                            : asset('storage/' . ltrim($avatar, '/')))
                                        : ('https://i.pravatar.cc/80?u=' . urlencode($landlord->email ?? ($landlord->id ?? uniqid())));
                                @endphp
                                <img src="{{ $avatarSrc }}" alt="{{ $landlord->name ?? 'المؤجر' }}">
                            </div>
                            <div class="client-text">
                                <div class="client-name">{{ $landlord->name ?? '—' }}</div>
                                <div class="client-email">{{ $landlord->email ?? '—' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="cell cell-small">
                        @if($previewData['face_verified'])
                            <span class="check-dot yes"><i class="fas fa-check"></i></span>
                        @else
                            <span class="check-dot no"><i class="fas fa-times"></i></span>
                        @endif
                    </div>
                    <div class="cell cell-small">
                        @if($previewData['id_verified'])
                            <span class="check-dot yes"><i class="fas fa-check"></i></span>
                        @else
                            <span class="check-dot no"><i class="fas fa-times"></i></span>
                        @endif
                    </div>
                    <div class="cell cell-small">{{ $previewData['rating'] ? number_format($previewData['rating'], 1) : '—' }}</div>
                    <div class="cell cell-small">{{ optional($landlord?->created_at)->format('d/m/Y') ?? '—' }}</div>
                </div>
            </div>

            <!-- Property details card -->
            <div class="info-table details-card">
                <div class="table-row table-head">
                    <div class="cell cell-small">المدينة</div>
                    <div class="cell cell-small">العنوان</div>
                    <div class="cell cell-small">نوع الإيجار</div>
                    <div class="cell cell-small">نوع العقار</div>
                    <div class="cell cell-small">السعر</div>
                </div>
                <div class="table-row">
                    <div class="cell cell-small">{{ $property->city ?? '—' }}</div>
                    <div class="cell cell-small address">{{ $property->address ?? ($property->title ?? '—') }}</div>
                    <div class="cell cell-small">{{ $property->rental_type ?? 'يومي' }}</div>
                    <div class="cell cell-small">{{ $type->name ?? '—' }}</div>
                    <div class="cell cell-small">{{ number_format($property->price ?? 0, 0) }} د.ل <span class="muted">/ اليوم</span></div>
                </div>
            </div>

            <!-- Description -->
            <div class="section">
                <h4 class="section-title">وصف العقار</h4>
                <p class="section-text">{{ $property->description ?? 'لا يوجد وصف لهذا العقار.' }}</p>
                <div class="section-divider"></div>
            </div>

            <!-- Amenities -->
            <div class="section">
                <h4 class="section-title amenities-title">المرافق والملحقات</h4>
                @php
                    $amenitiesList = $property->amenities?->pluck('name')->filter()->values()->all() ?? [];
                    if (!count($amenitiesList)) {
                        $amenitiesList = [
                            'الانترنت لاسلكي',
                            'شاشة مسطحة',
                            'مكيف هواء مركزي',
                            'حمام سباحة خاص',
                            'حديقة خاصة',
                            'موقف خاص للسيارات',
                        ];
                    }
                    $amenityIcons = [
                        'الانترنت لاسلكي' => 'fas fa-wifi',
                        'واي فاي' => 'fas fa-wifi',
                        'شاشة مسطحة' => 'fas fa-tv',
                        'مكيف هواء مركزي' => 'fas fa-snowflake',
                        'حمام سباحة خاص' => 'fas fa-swimming-pool',
                        'حديقة خاصة' => 'fas fa-tree',
                        'موقف خاص للسيارات' => 'fas fa-parking',
                    ];
                @endphp
                <div class="amenities amenity-row">
                    @foreach($amenitiesList as $amen)
                        @php $icon = $amenityIcons[$amen] ?? 'fas fa-check-circle'; @endphp
                        <div class="amenity"><i class="{{ $icon }}"></i> {{ $amen }}</div>
                    @endforeach
                </div>
                <div class="section-divider"></div>
            </div>

            <!-- Actions row -->
            <div class="actions-row">
                @php
                    $hasStatus = \Illuminate\Support\Facades\Schema::hasColumn('properties', 'status');
                    $computedStatus = $hasStatus
                        ? ($property->status ?? 'unpublished')
                        : ((($property->approved ?? false) === true) ? 'published' : (is_null($property->approved) ? 'inprogress' : 'unpublished'));
                @endphp

                @if($computedStatus === 'published')
                    <button type="button" id="openStopModal" class="btn btn-stop"><i class="fas fa-times" style="font-size: 25px;width: 24px; height: 24px"></i><span class="btn-text"> إيقاف النشر</span></button>
                @elseif($computedStatus === 'unpublished')
                    <form method="POST" action="{{ route('dashboard.properties.approve', $property) }}">
                        @csrf
                        <button type="submit" class="btn btn-republish"><i class="fas fa-sync" style="font-size: 20px;width: 20px; height: 20px"></i><span class="btn-text"> إعادة النشر</span></button>
                    </form>
                @elseif($computedStatus === 'inprogress')
                    <form method="POST" action="{{ route('dashboard.properties.approve', $property) }}">
                        @csrf
                        <button type="submit" class="btn btn-approve"><i class="fas fa-check" style="font-size: 20px;width: 20px; height: 20px"></i><span class="btn-text"> موافقة</span></button>
                    </form>
                @endif
            </div>

            <!-- Stop Publish Modal -->
            <div id="stopModal" class="stop-modal" aria-hidden="true">
                <div class="stop-card">
                    <div class="stop-header">
                        <div class="stop-title">إيقاف نشر الاعلان</div>
                        <button type="button" class="stop-close" id="closeStopModal" aria-label="إلغاء"><span>إلغاء</span> <i class="fas fa-times"></i></button>
                    </div>
                    <div class="stop-body">
                        <div class="stop-prop">
                            @php
                                $img = $property->image ?? null;
                                $imgSrc = $img
                                    ? (\Illuminate\Support\Str::startsWith($img, ['http://','https://'])
                                        ? $img
                                        : asset(ltrim($img, '/')))
                                    : asset('images/rento-logo.svg');
                            @endphp
                            <img src="{{ $imgSrc }}" alt="{{ $property->title }}" class="stop-thumb">
                            <div class="stop-prop-text">
                                <div class="stop-prop-title">{{ $property->title ?? 'اسم العقار' }}</div>
                                <div class="stop-prop-owner">{{ optional($landlord)->name ?? 'اسم المؤجر' }}</div>
                            </div>
                        </div>

                        <div class="stop-label">سبب الإيقاف</div>
                        <form method="POST" action="{{ route('dashboard.properties.deactivate', $property) }}" id="stopForm">
                            @csrf
                            <div class="stop-select-row">
                                <select name="reason" id="stopReason" class="stop-select" required>
                                    <option value="" selected disabled>اختر سبب الإيقاف</option>
                                    @foreach(($deactivationReasons ?? []) as $r)
                                        <option value="{{ $r }}">{{ $r }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" id="confirmStopBtn" class="btn btn-confirm" disabled>تأكيد</button>
                            </div>

                            <div id="otherReasonBox" class="stop-other" style="display: none;">
                                <label for="otherReasonInput" class="stop-label">اكتب السبب</label>
                                <input type="text" name="other_reason" id="otherReasonInput" class="stop-input" placeholder="اكتب سبب الإيقاف" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .preview-card { background: #fff; border: 1px solid var(--border-color); border-radius: 14px; padding: 18px; }
    .preview-gallery { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; align-items: stretch; direction: ltr; --main-h: 280px; --thumbs-gap: 10px; }
    .preview-card-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; flex-direction: row; direction: rtl; }
    .preview-card-title {color: #1F2937;font-family: Cairo, serif;font-weight: 400;font-size: 24px;line-height: 120%;}
    .gallery-thumbs { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--thumbs-gap); grid-auto-rows: calc((var(--main-h) - var(--thumbs-gap)) / 2); }
    .thumb { border: 1px solid #E5E7EB; border-radius: 10px; overflow: hidden; background: #F9FAFB; display: flex; align-items: center; justify-content: center; }
    .thumb img { width: 100%; height: 100%; object-fit: cover; }
    .thumb-placeholder { color: #9CA3AF; font-size: 18px; }
    .gallery-main { position: relative; border: 1px solid #E5E7EB; border-radius: 12px; overflow: hidden; }
    .main-img { width: 100%; height: var(--main-h); object-fit: cover; display: block; }
    .gallery-rating { position: absolute; left: 12px; top: 12px; background: rgba(255,255,255,.9); border: 1px solid #E5E7EB; border-radius: 20px; padding: 6px 10px; font-weight: 600; display: flex; align-items: center; gap: 6px; }
    .back-btn {background: #fff; color: #374151; padding: 8px 10px; display: inline-flex; align-items: center; }

    .preview-title-row { display: flex; justify-content: space-between; align-items: start; margin-top: 14px; }
    .property-name { margin: 0 0 6px; font-size: 20px; font-weight: 700;color: rgba(30, 108, 181, 1) }
    .property-meta { display: flex; gap: 12px; color: #6B7280; }
    .property-meta .meta-item { display: inline-flex; align-items: center; gap: 6px; }
    .property-specs { display: flex; align-items: center; gap: 10px; color: #6B7280; font-size: 14px; }
    .property-specs .spec-item { display: inline-flex; align-items: center; gap: 6px; }
    .property-specs .spec-divider { color: #9CA3AF; }
    .status-badge { font-size: 12px; font-weight: 600;
        width: 125px;
        height: 30px;
        opacity: 1;
        border-radius: 24px;
        border: 1px solid #E5E7EB;
        padding-right: 40px;
        padding-top: 4px;
        margin-top: 8px;
    }
    .status-badge.unpublished{ color: #dc1111; background: rgba(255, 255, 255, 1); border-color: #fc8181; }
    .status-badge.published{ color: rgba(251, 185, 13, 1); background: rgba(255, 255, 255, 1); border-color: #FDE68A; }
    .reviews-badge {color: #374151; font-size: 12px; font-weight: 600;
        width: 128px;
        height: 30px;
        opacity: 1;
        border-radius: 24px;
        border: 1px solid #E5E7EB;
        padding-top: 4px;
        padding-right: 5px;
        box-shadow: 0px 0px 7px 0px rgba(0, 0, 0, 0.25);
    }

    .tags-row { margin-top: 8px; display: flex; gap: 10px; flex-wrap: wrap; direction: rtl; }
    .tag-chip { font-size: 12px; font-weight: 600; color: #0E7490; background: #E0F2FE; border: 1px Dashed #0E7490; border-radius: 24px; padding: 6px 10px; text-decoration: none; display: inline-flex; align-items: center; }
    .tag-chip:hover { background: #BAE6FD; }

    .verification-row { display: flex; gap: 10px; margin-top: 12px; }
    .verify-chip { display: inline-flex; align-items: center; gap: 6px; border: 1px solid #E5E7EB; color: #374151; padding: 6px 10px; border-radius: 999px; font-size: 12px; }
    .verify-chip.ok { border-color: #D1FAE5; color: #065F46; background: #ECFDF5; }

    .info-table { border: 1px solid #E5E7EB; border-radius: 12px; overflow: hidden; margin-top: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); direction: rtl; }
    .info-table .table-row { display: grid; grid-template-columns: 1fr 120px 120px 120px 120px; border-top: 1px solid #E5E7EB; }
    .info-table .table-row:first-child { border-top: none; }
    .info-table .table-head { background: #EAF3FE; font-weight: 600; }
    .info-table .cell { padding: 12px 14px; display: flex; align-items: center; gap: 8px; }
    .info-table .cell-small { font-size: 13px; color: #374151; justify-content: right; }
    .info-table .cell-flex { justify-content: flex-start; }
    .info-table .head-label{ font-size: 13px; color: #374151; }
    .client-info { display: flex; align-items: center; gap: 10px; justify-content: flex-start; }
    .client-info .avatar { width: 32px; height: 32px; border-radius: 999px; overflow: hidden; border: 1px solid #E5E7EB; }
    .client-info .avatar img { width: 100%; height: 100%; object-fit: cover; }
    .client-name { font-weight: 600; }
    .client-email { font-size: 12px; color: #6B7280; }
    .small-note { font-size: 12px; color: #6B7280; }

    .check-dot { width: 22px; height: 22px; border-radius: 999px; display: inline-flex; align-items: center; justify-content: center; background: #3B82F6; color: #fff; box-shadow: 0 0 0 3px rgba(59,130,246,0.15); }
    .check-dot.no { background: #E5E7EB; color: #6B7280; box-shadow: none; }

    /* Details card layout (city, address, rent-type, property-type, price) */
    .info-table.details-card .table-row { grid-template-columns: 120px 1fr 120px 120px 120px; }
    .info-table.details-card .cell.address { justify-content: flex-start; }

    .details-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-top: 14px; }
    .detail-item { background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 10px; padding: 12px; }
    .detail-item .label { font-size: 12px; color: #6B7280; margin-bottom: 4px; }
    .detail-item .value { font-weight: 600; color: #111827; }
    .detail-item .muted { color: #6B7280; font-weight: 500; }

    .section { margin-top: 16px; }
    .section-title {font-size: 16px; font-weight: 400; margin-bottom: 8px;color: rgba(30, 108, 181, 1); font-family: Cairo, serif;}
    .section-text { color: rgb(159, 158, 158); }
    .section-divider { border-top: 1px solid #E5E7EB; margin: 16px 0; }
    .amenities { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; justify-items: right; direction: rtl; text-align: center; }
    .amenities.amenity-row { grid-template-columns: repeat(3, 1fr); }
    .amenity { background: transparent; border: none; border-radius: 0; padding: 0; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; justify-content: center; color: #374151; }
    .amenity i { font-size: 18px; color: #6B7280; margin-left: 6px; }

    .actions-row { display: flex; justify-content: flex-end; margin-top: 16px; direction: rtl; }
    .actions-row .btn { border-radius: 10px; }
    .btn-republish { background: #10B981; color: #fff; border: 1px solid #059669; padding: 8px 16px; }
    .btn-approve { background: #3B82F6; color: #fff; border: 1px solid #2563EB; padding: 8px 16px; }
    .btn-stop { direction: rtl; display: inline-flex; align-items: center; gap: 8px; color: #ff0000; border: 1px solid #f80000; background: rgba(254, 242, 241, 1); padding: 8px 16px; border-radius: 10px; box-shadow: 0px 0px 7px rgba(0,0,0,0.08); }
    .btn-stop:hover { background: #FFF5F5; }
    .btn-stop i { color: #DC2626; }

    /* Stop modal styles */
    .stop-modal { position: fixed; inset: 0; background: rgba(0,0,0,0.45); display: none; align-items: center; justify-content: center; z-index: 1050; }
    .stop-modal.show { display: flex; }
    .stop-card { width: 720px; max-width: 95vw; background: #fff; border-radius: 12px; border: 1px solid #E5E7EB; box-shadow: 0 10px 30px rgba(0,0,0,0.15); overflow: hidden; direction: rtl; }
    .stop-header { display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background: #EAF3FE; }
    .stop-title { font-weight: 700; color: #1F2937; }
    .stop-close { display: inline-flex; align-items: center; gap: 8px; border: 1px solid rgba(63, 149, 253, 1); color: rgba(63, 149, 253, 1); background: #fff; padding: 6px 10px; border-radius: 8px; }
    .stop-body { padding: 16px; }
    .stop-prop { display: flex; align-items: center; gap: 12px; justify-content: flex-start; }
    .stop-thumb { width: 48px; height: 48px; border-radius: 10px; border: 1px solid #E5E7EB; object-fit: cover; }
    .stop-prop-title { font-weight: 700; color: #1F2937; }
    .stop-prop-owner { font-size: 12px; color: #6B7280; }
    .stop-label { margin-top: 14px; margin-bottom: 6px; color: #6B7280; }
    .stop-select { flex: 1; width: auto; border: 1px solid #E5E7EB; border-radius: 10px; padding: 10px 12px; font-size: 14px; color: #374151; }
    .stop-select-row { display: flex; align-items: center; gap: 12px; }
    .stop-other .stop-input { width: 100%; border: 1px solid #E5E7EB; border-radius: 10px; padding: 10px 12px; font-size: 14px; color: #374151; }
    .stop-actions { display: flex; justify-content: flex-start; margin-top: 12px; }
    .btn-confirm { background: #3B82F6; color: #fff; border: 1px solid #2563EB; padding: 8px 16px; border-radius: 10px; }
    .btn-confirm[disabled] { opacity: .65; cursor: not-allowed; }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const openBtn = document.getElementById('openStopModal');
        const closeBtn = document.getElementById('closeStopModal');
        const modal = document.getElementById('stopModal');
        const reason = document.getElementById('stopReason');
        const confirmBtn = document.getElementById('confirmStopBtn');
        const otherBox = document.getElementById('otherReasonBox');
        const otherInput = document.getElementById('otherReasonInput');

        function openModal(){ modal.classList.add('show'); modal.setAttribute('aria-hidden','false'); }
        function closeModal(){ modal.classList.remove('show'); modal.setAttribute('aria-hidden','true'); }

        if (openBtn) openBtn.addEventListener('click', openModal);
        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        document.addEventListener('keydown', function(e){ if(e.key==='Escape'){ closeModal(); }});
        modal?.addEventListener('click', function(e){ if(e.target === modal){ closeModal(); }});

        function updateConfirmState(){
            if (!reason || !confirmBtn) return;
            const val = reason.value;
            if (val === 'أخرى') {
                otherBox?.style && (otherBox.style.display = 'block');
                if (otherInput) {
                    otherInput.required = true;
                    confirmBtn.disabled = !(otherInput.value && otherInput.value.trim().length >= 3);
                } else {
                    confirmBtn.disabled = true;
                }
            } else {
                if (otherBox?.style) { otherBox.style.display = 'none'; }
                if (otherInput) { otherInput.required = false; }
                confirmBtn.disabled = !val;
            }
        }

        if (reason) reason.addEventListener('change', updateConfirmState);
        if (otherInput) otherInput.addEventListener('input', updateConfirmState);
    });

    // عرض رسالة نجاح إن وجدت
    @if(session('status'))
        setTimeout(function(){ alert(@json(session('status'))); }, 100);
    @endif
</script>
@endpush
