@extends('dashboard.layouts.app')

@section('title', 'تفاصيل الطلب')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="request-card">
            <div class="request-card-top">
                <div class="request-card-title">تفاصيل الطلب</div>
                <a href="{{ route('dashboard.properties.requests') }}" class="btn back-btn back-btn-top" title="رجوع" aria-label="رجوع">
                    <i class="fas fa-times"></i>
                </a>
            </div>

            <!-- معلومات العميل والتحقق -->
            <div class="info-table">
                <div class="table-row table-head">
                    <div class="cell cell-flex"><span class="head-label">اسم العميل</span></div>
                    <div class="cell cell-small">تعريف الوجه</div>
                    <div class="cell cell-small">إثبات الشخصية</div>
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
                                <img src="{{ $avatarSrc }}" alt="{{ $landlord->name ?? 'العميل' }}">
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
                    <div class="cell cell-small">{{ optional($landlord?->created_at)->format('d/m/Y') ?? '—' }}</div>
                </div>
            </div>

            <!-- تفاصيل العقار الأساسية -->
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
                    <div class="cell cell-small">{{ number_format($property->price ?? 0, 0) }} <span class="muted">د.ل / اليوم</span></div>
                </div>
            </div>

            <!-- عنوان الإعلان -->
            <div class="section">
                <h4 class="section-title">عنوان الاعلان</h4>
                <div class="content-title">{{ $property->title ?? '—' }}</div>
            </div>

            <!-- وصف العقار -->
            <div class="section">
                <h4 class="section-title">وصف العقار</h4>
                <p class="section-text">{{ $property->description ?? 'لا يوجد وصف لهذا العقار.' }}</p>
            </div>

            <!-- العدادات (مكدّسة تحت بعض) -->
            @php
                $specGuests = $property->capacity ?? 6;
                $specBedrooms = $property->bedrooms ?? 2;
                $specBathrooms = $property->bathrooms ?? 1;
            @endphp
            <div class="counters">
                <div class="counter">
                    <div class="counter-label">عدد الغرف</div>
                    <div class="counter-value">{{ $specBedrooms }}</div>
                </div>
                <div class="counter">
                    <div class="counter-label">عدد الحمامات</div>
                    <div class="counter-value">{{ $specBathrooms }}</div>
                </div>
                <div class="counter">
                    <div class="counter-label">عدد الأفراد</div>
                    <div class="counter-value">{{ $specGuests }}</div>
                </div>
            </div>

            <!-- المرافق والملحقات -->
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
            </div>

            <!-- الكلمات المفتاحية (من قاعدة البيانات) -->
            @php
                $searchTags = [];
                // اقرأ الكلمات المفتاحية من عمود keywords (JSON) إن وُجدت
                if (is_array($property->keywords ?? null)) {
                    $searchTags = array_values(array_unique(array_filter(array_map('trim', $property->keywords))));
                }
                // في حال عدم توفر كلمات، نعتمد على بدائل خفيفة ذات صلة
                if (!count($searchTags)) {
                    $searchTags = array_filter([
                        $type->name ?? null,
                        $property->city ?? null,
                    ]);
                }
            @endphp
            <div class="section">
                <h4 class="section-title">الكلمات المفتاحية</h4>
                <div class="tags-row">
                    @foreach($searchTags as $tag)
                        <a class="tag-chip"> {{ $tag }}</a>
                    @endforeach
                </div>
            </div>

            <!-- الصور المرفقة -->
            <div class="section">
                <h4 class="section-title">الصور المرفقة</h4>
                <div class="attached-photos">
                    @php
                        // دمج الصورة الأساسية ضمن الصور المرفقة
                        $primary = $property->image ?? null;
                        $dbImages = $property->images?->pluck('url')->filter()->values()->all() ?? [];
                        $gallery = $galleryImages ?? [];
                        $thumbs = [];

                        if ($primary) { $thumbs[] = $primary; }
                        foreach ($dbImages as $u) { $thumbs[] = $u; }
                        // إذا لم توجد صور بقاعدة البيانات، نستعين بالصور القادمة من الكنترولر
                        if (!count($dbImages) && count($gallery)) {
                            foreach ($gallery as $u) { $thumbs[] = $u; }
                        }
                        // إزالة التكرارات مع الحفاظ على الترتيب
                        $thumbs = array_values(array_unique($thumbs));
                        // ضمان وجود صورة واحدة على الأقل إن توفر الأساسية
                        if (!count($thumbs) && $primary) { $thumbs = [$primary]; }

                        $thumbs = array_slice($thumbs, 0, 5);
                    @endphp
                    @foreach($thumbs as $img)
                        <div class="attached-photo">
                            @if($img)
                                @php
                                    $isUrl = \Illuminate\Support\Str::startsWith($img, ['http://','https://']);
                                    $isAbs = \Illuminate\Support\Str::startsWith($img, ['/']);
                                @endphp
                                @if($isUrl)
                                    <img src="{{ $img }}" alt="صورة">
                                @elseif($isAbs)
                                    <img src="{{ asset(ltrim($img, '/')) }}" alt="صورة">
                                @else
                                    <img src="{{ asset($img) }}" alt="صورة">
                                @endif
                            @else
                                <div class="attached-photo-placeholder"><i class="fas fa-image"></i></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>



            <!-- أزرار الإجراءات -->
            <div class="request-actions">
                <button type="button" class="btn btn-reject" id="openRejectModalBtn">رفض</button>
                <form method="POST" action="{{ route('dashboard.properties.approve', $property) }}">
                    @csrf
                    <button type="submit" class="btn btn-approve">موافقة</button>
                </form>
            </div>

            <!-- مودال رفض نشر الإعلان -->
            @php
                $rejectionReasons = [
                    'انتهاك السياسات',
                    'معلومات خاطئة',
                    'صور غير مناسبة',
                    'مخالفة الشروط',
                    'إعادة التقييم',
                    'أخرى',
                ];
            @endphp

            <div class="modal-backdrop" id="rejectModal" aria-hidden="true">
                <div class="modal-sheet" role="dialog" aria-labelledby="rejectModalTitle" aria-modal="true">
                    <div class="modal-header">
                        <div class="modal-title" id="rejectModalTitle">رفض نشر الاعلان</div>
                        <button type="button" class="modal-close" id="closeRejectModalBtn" aria-label="إلغاء">
                            إلغاء <span class="x">×</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="property-line">
                            <div class="thumbnail">
                                @php
                                    $thumb = $property->image ?? null;
                                    $isUrl = \Illuminate\Support\Str::startsWith($thumb, ['http://','https://']);
                                    $isAbs = \Illuminate\Support\Str::startsWith($thumb, ['/']);
                                @endphp
                                @if($thumb)
                                    @if($isUrl)
                                        <img src="{{ $thumb }}" alt="صورة"/>
                                    @elseif($isAbs)
                                        <img src="{{ asset(ltrim($thumb, '/')) }}" alt="صورة"/>
                                    @else
                                        <img src="{{ asset($thumb) }}" alt="صورة"/>
                                    @endif
                                @else
                                    <span class="thumb-placeholder"><i class="fas fa-image"></i></span>
                                @endif
                            </div>
                            <div class="prop-text">
                                <div class="prop-name">{{ $property->title ?? '—' }}</div>
                                <div class="host-name">{{ $landlord->name ?? '—' }}</div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('dashboard.properties.reject', $property) }}" id="rejectForm">
                            @csrf
                            <label for="rejectReason" class="reason-label">سبب الرفض</label>
                            <div class="reason-row">
                                <div class="select-wrap">
                                    <select id="rejectReason" class="reason-select">
                                        <option value="" selected disabled>اختر سبب الرفض</option>
                                        @foreach($rejectionReasons as $r)
                                            <option value="{{ $r }}">{{ $r }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="confirm-btn" id="confirmRejectBtn" disabled>تأكيد</button>
                            </div>

                            <div class="other-reason" id="otherReasonWrap" hidden>
                                <input type="text" id="otherReasonInput" class="other-input" placeholder="اكتب سبب الرفض" dir="rtl" />
                            </div>

                            <input type="hidden" name="reason" id="finalReason" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .request-card { background: #fff; border: 1px solid #E5E7EB; border-radius: 14px; padding: 18px; }
    .request-card-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; flex-direction: row; direction: rtl; }
    .request-card-title { color: #1F2937; font-family: Cairo, serif; font-weight: 400; font-size: 24px; line-height: 120%; }
    .back-btn { background: #fff; color: #374151; padding: 8px 10px; display: inline-flex; align-items: center; }

    .info-table { border: 1px solid #E5E7EB; border-radius: 12px; overflow: hidden; margin-top: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); direction: rtl; }
    .info-table .table-row { display: grid; grid-template-columns: 1fr 120px 120px 120px; border-top: 1px solid #E5E7EB; }
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

    .check-dot { width: 22px; height: 22px; border-radius: 999px; display: inline-flex; align-items: center; justify-content: center; background: #3B82F6; color: #fff; box-shadow: 0 0 0 3px rgba(59,130,246,0.15); }
    .check-dot.no { background: #E5E7EB; color: #6B7280; box-shadow: none; }

    .info-table.details-card .table-row { grid-template-columns: 120px 1fr 120px 120px 160px; }
    .info-table.details-card .cell.address { justify-content: flex-start; }
    .info-table .muted { color: #6B7280; }

    .section { margin-top: 16px; }
    .section-title { font-size: 16px; font-weight: 400; margin-bottom: 8px; color: rgba(30, 108, 181, 1); font-family: Cairo, serif; }
    .section-text { color: rgb(159, 158, 158); }
    .content-title { color: #374151; font-weight: 600; margin-bottom: 8px; }

    .counters { display: flex; flex-direction: column; gap: 8px; margin-top: 8px; direction: rtl; }
    .counters .counter { display: inline-flex; align-items: center; gap: 30px; justify-content: flex-start; }
    .counters .counter > * { flex: 0 0 auto; }
    .counters .counter .counter-label { margin-inline-start: 0; }
    .counters .counter .counter-value { font-weight: 600; }
    .counter { display: inline-flex; align-items: center; background: transparent; border: none; padding: 4px 0; }
    .counter-label { color: rgba(30, 108, 181, 1); font-size: 14px; width: 160px; text-align: right; }
    .counter-value { font-weight: 700; color: #1F2937; font-size: 16px; }

    .amenities { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; justify-items: right; direction: rtl; text-align: center; }
    .amenity { background: transparent; border: none; border-radius: 0; padding: 0; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; justify-content: center; color: #374151; }
    .amenity i { font-size: 18px; color: #6B7280; margin-left: 6px; }

    .tags-row { margin-top: 8px; display: flex; gap: 10px; flex-wrap: wrap; direction: rtl; }
    .tag-chip { font-size: 12px; font-weight: 600; color: #0E7490; background: #E0F2FE; border: 1px dashed #0E7490; border-radius: 24px; padding: 6px 10px; text-decoration: none; display: inline-flex; align-items: center; }
    .tag-chip:hover { background: #BAE6FD; }

    .attached-photos { display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; margin-top: 10px; }
    .attached-photo { border: 1px solid #E5E7EB; border-radius: 10px; overflow: hidden; background: #F9FAFB; display: flex; align-items: center; justify-content: center; height: 110px; }
    .attached-photo img { width: 100%; height: 100%; object-fit: cover; }
    .attached-photo-placeholder { color: #9CA3AF; font-size: 18px; }

    .request-actions { display: flex; justify-content: space-between; margin-top: 18px; direction: rtl; }
    .request-actions .btn { border-radius: 10px; padding: 8px 16px; }
    .btn-reject { color: #f80000; border: 1px solid #f80000; background: rgba(254, 242, 241, 1); }
    .btn-reject:hover { background: #FFF5F5; }
    .btn-approve { background: #2B7FE6; color: #fff; border: 1px solid #2563EB; }

    /* Modal styles */
    .modal-backdrop { position: fixed; inset: 0; background: rgba(17,24,39,0.35); display: none; align-items: center; justify-content: center; z-index: 2001; }
    .modal-backdrop[aria-hidden="false"] { display: flex; }
    .modal-sheet { width: 520px; max-width: 92vw; background: #fff; border: 1px solid #E5E7EB; border-radius: 14px; box-shadow: 0 10px 20px rgba(0,0,0,0.08); overflow: hidden; direction: rtl; }
    .modal-header { background: #EAF3FE; padding: 10px 12px; display: flex; align-items: center; justify-content: space-between; }
    .modal-title { font-size: 16px; font-weight: 600; color: #1F2937; }
    .modal-close { background: #fff; color: #374151; border: 1px solid #E5E7EB; border-radius: 8px; padding: 4px 10px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px; }
    .modal-close .x { font-weight: 700; font-size: 14px; }
    .modal-body { padding: 16px; }
    .property-line { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
    .thumbnail { width: 48px; height: 48px; border-radius: 8px; overflow: hidden; border: 1px solid #E5E7EB; background: #F3F4F6; display: flex; align-items: center; justify-content: center; }
    .thumbnail img { width: 100%; height: 100%; object-fit: cover; }
    .thumb-placeholder { color: #9CA3AF; }
    .prop-text .prop-name { font-weight: 600; color: #1F2937; }
    .prop-text .host-name { font-size: 12px; color: #6B7280; }
    .reason-label { font-size: 13px; color: #374151; margin-bottom: 6px; display: block; }
    .select-wrap { border: 1px solid #E5E7EB; border-radius: 12px; padding: 2px 12px; background: #fff; min-height: 44px; }
    .reason-select { width: 100%; padding: 8px 10px; border: none; outline: none; background: transparent; font-size: 15px; color: #374151; }
    .other-reason { margin-top: 10px; }
    .other-input { width: 100%; border: 1px solid #E5E7EB; border-radius: 10px; padding: 10px; font-size: 14px; }
    .reason-row { display: flex; align-items: center; gap: 8px; }
    .reason-row .select-wrap { flex: 1; width: 100%; }
    .modal-actions { display: none; }
    .confirm-btn { background: #ddd; color: #777; border: none; border-radius: 10px; padding: 10px 18px; min-height: 44px; cursor: not-allowed; }
    .confirm-btn.enabled { background: rgba(254, 242, 241, 1); color: #f80000; border: 1px solid #f80000; cursor: pointer; }
</style>
@endpush

@push('scripts')
<script>
    (function(){
        const modal = document.getElementById('rejectModal');
        const openBtn = document.getElementById('openRejectModalBtn');
        const closeBtn = document.getElementById('closeRejectModalBtn');
        const reasonSel = document.getElementById('rejectReason');
        const otherWrap = document.getElementById('otherReasonWrap');
        const otherInput = document.getElementById('otherReasonInput');
        const finalReason = document.getElementById('finalReason');
        const confirmBtn = document.getElementById('confirmRejectBtn');

        function setEnabled(enabled){
            if(enabled){
                confirmBtn.classList.add('enabled');
                confirmBtn.disabled = false;
                confirmBtn.style.cursor = 'pointer';
            } else {
                confirmBtn.classList.remove('enabled');
                confirmBtn.disabled = true;
                confirmBtn.style.cursor = 'not-allowed';
            }
        }

        openBtn && openBtn.addEventListener('click', function(){
            modal.setAttribute('aria-hidden', 'false');
        });
        closeBtn && closeBtn.addEventListener('click', function(){
            modal.setAttribute('aria-hidden', 'true');
        });

        reasonSel && reasonSel.addEventListener('change', function(){
            const val = reasonSel.value || '';
            if(val === 'أخرى'){
                otherWrap.hidden = false;
                setEnabled(!!otherInput.value.trim());
            } else {
                otherWrap.hidden = true;
                setEnabled(!!val);
            }
        });

        otherInput && otherInput.addEventListener('input', function(){
            if((reasonSel.value || '') === 'أخرى'){
                setEnabled(!!otherInput.value.trim());
            }
        });

        // قبل الإرسال: ضع القيمة النهائية في الحقل المخفي
        const form = document.getElementById('rejectForm');
        form && form.addEventListener('submit', function(e){
            const val = reasonSel.value || '';
            if(!val) { e.preventDefault(); return; }
            if(val === 'أخرى'){
                const other = (otherInput.value || '').trim();
                finalReason.value = other ? other : 'أخرى';
            } else {
                finalReason.value = val;
            }
        });
    })();
</script>
@endpush
