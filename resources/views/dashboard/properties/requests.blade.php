@extends('dashboard.layouts.app')

@section('title', 'طلبات نشر العقارات')

@section('content')
    <div class="properties-box">
        <div class="properties-header">
            <div class="properties-title">طلبات نشر العقارات</div>
            <div class="searchbar">
                <form action="{{ route('dashboard.properties.requests') }}" method="get" class="d-flex align-items-center gap-2">
                    <input type="text" name="q" value="{{ request('q') }}" class="search-input" placeholder="ابحث عن اسم مستخدم أو بريد إلكتروني">

                    <!-- Hidden filter fields that the panel will update -->
                    <input type="hidden" name="city" id="cityInput" value="{{ request('city') }}">
                    <input type="hidden" name="property_type_name" id="typeNameInput" value="{{ request('property_type_name') }}">
                    <input type="hidden" name="price_min" id="priceMinInput" value="{{ request('price_min') }}">
                    <input type="hidden" name="price_max" id="priceMaxInput" value="{{ request('price_max') }}">

                    <!-- Filter panel -->
                    <div class="filter-panel" id="filterDropdown" aria-hidden="true">
                        <div class="filter-row">
                            <div class="filter-label">نوع الإيجار</div>
                            <div class="filter-options">
                                <button type="button" class="chip" data-rent="daily">إيجار يومي</button>
                                <button type="button" class="chip" data-rent="monthly">إيجار شهري</button>
                            </div>
                        </div>
                        <div class="filter-row">
                            <div class="filter-label">نوع العقار</div>
                            <div class="filter-options">
                                <button type="button" class="chip" data-type="شقة سكنية">شقة سكنية</button>
                                <button type="button" class="chip" data-type="استراحة">استراحة</button>
                                <button type="button" class="chip" data-type="قاعة مناسبات">قاعة مناسبات</button>
                                <button type="button" class="chip" data-type="مكتب">مكتب</button>
                            </div>
                        </div>
                        <div class="filter-row">
                            <div class="filter-label">المدينة</div>
                            <div class="filter-options">
                                <button type="button" class="chip" data-city="طرابلس">طرابلس</button>
                                <button type="button" class="chip" data-city="بنغازي">بنغازي</button>
                                <button type="button" class="chip" data-city="مصراتة">مصراتة</button>
                                <button type="button" class="chip" data-city="سبها">سبها</button>
                            </div>
                        </div>
                        <div class="filter-row">
                            <div class="filter-label">متوسط السعر</div>
                            <div class="filter-range">
                                @php
                                    $initial = is_numeric(request('price_max')) ? (int) request('price_max') : 1500;
                                @endphp
                                <input type="range" min="0" max="5000" step="25" value="{{ $initial }}" id="priceRange">
                                <div class="price-value"><span id="priceRangeValue">{{ number_format($initial) }}</span> <span class="currency">د.ل</span></div>
                            </div>
                        </div>
                        <div class="filter-actions">
                            <button type="submit" class="apply-filters">تطبيق</button>
                            <button type="button" class="reset-filters" id="resetFilters">إعادة الضبط</button>
                        </div>
                    </div>
                    <button type="button" class="filter-btn" id="filterToggle">تنقية <i class="fas fa-filter ms-1"></i></button>
                    <button type="button" class="accept-btn" id="acceptSelected">قبول المحدد</button>
                </form>
            </div>
        </div>

        <div class="table-responsive table-area">
            <table class="table table-hover align-middle properties-table">
                <thead>
                <tr>
                    <th class="select-col"></th>
                    <th>اسم العقار</th>
                    <th>اسم المستخدم</th>
                    <th>نوع الإيجار</th>
                    <th>نوع العقار</th>
                    <th>المدينة والمنطقة</th>
                    <th>السعر</th>
                    <th>الحالة</th>
                    <th class="actions-col"></th>
                </tr>
                </thead>
                <tbody>
                @forelse($properties as $property)
                    <tr>
                        <td class="select-col"><input type="checkbox" class="row-select" value="{{ $property->id }}"></td>
                        <td>
                            <div class="property-name">
                                @php
                                    $img = $property->image ? asset($property->image) : asset('images/rento-logo.svg');
                                @endphp
                                <img src="{{ $img }}" alt="صورة {{ $property->title }}" class="property-thumb">
                                <span>{{ $property->title }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="user-cell">
                                @php
                                    $u = $property->user;
                                    $avatar = $u->avatar ?? null;
                                    $email = $u->email ?? ($u->id ?? null);
                                    $fallback = 'https://i.pravatar.cc/80?u=' . urlencode($email ?? uniqid());
                                    $avatarSrc = $avatar
                                        ? (\Illuminate\Support\Str::startsWith($avatar, ['http://','https://'])
                                            ? $avatar
                                            : asset('storage/' . ltrim($avatar, '/')))
                                        : $fallback;
                                @endphp
                                <div class="user-avatar">
                                    <img src="{{ $avatarSrc }}" alt="{{ $u->name ?? 'المستخدم' }}">
                                </div>
                                <div class="user-name">{{ $u->name ?? '—' }}</div>
                            </div>
                        </td>
                        <td class="text-muted small">{{ $property->rental_type ?? '—' }}</td>
                        <td>{{ optional($property->type)->name ?? '—' }}</td>
                        <td class="text-muted small">{{ $property->city ?? '—' }}</td>
                        <td class="fw-semibold">د.ل {{ number_format($property->price ?? 0) }}</td>
                        <td>
                            @php
                                $hasStatus = \Illuminate\Support\Facades\Schema::hasColumn('properties', 'status');
                                $computedStatus = $hasStatus ? ($property->status ?? 'inprogress') : (is_null($property->approved) ? 'inprogress' : (($property->approved ?? false) ? 'published' : 'unpublished'));
                                $statusLabel = $computedStatus === 'inprogress' ? 'قيد الإجراء' : ($computedStatus === 'unpublished' ? 'غير منشور' : 'منشور');
                                $statusClass = $computedStatus === 'inprogress' ? 'badge-inprogress' : ($computedStatus === 'unpublished' ? 'badge-unpublished' : 'badge-published');
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                        </td>
                        <td class="actions-col"><a href="{{ route('dashboard.properties.request', $property) }}" class="action-link">معاينة</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">لا توجد طلبات نشر حالياً</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if(isset($properties) && $properties->hasPages())
            <div class="table-footer">
                <div class="pagination-info">
                    عرض {{ $properties->firstItem() }} إلى {{ $properties->lastItem() }} من {{ $properties->total() }}
                </div>
                <div class="pagination-wrapper">
                    {{ $properties->onEachSide(1)->links('vendor.pagination.rento') }}
                </div>
            </div>
        @endif
    </div>
@endsection

@push('styles')
<style>
    .properties-box { background: #fff; border: 1px solid #E5E7EB; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 16px; position: relative; }
    .properties-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
    .properties-title { font-size: 18px; font-weight: 700; color: #1F2937; }
    .searchbar { display: flex; align-items: center; gap: 8px; position: relative; }
    .searchbar .accept-btn { border: 1px solid #2B7FE6; background: #2B7FE6; color: #fff; border-radius: 8px; padding: 8px 12px; font-size: 13px; }
    .searchbar .filter-btn { border: 1px solid #E5E7EB; border-radius: 8px; background: #fff; color: #1F2937; padding: 8px 12px; font-size: 13px; }
    .searchbar .search-input { width: 360px; max-width: 100%; border: 1px solid #E5E7EB; border-radius: 10px; padding: 9px 14px; outline: none; }

    /* Filter panel */
    .filter-panel { position: absolute; left: 0; top: 48px; min-width: 560px; background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; box-shadow: 0 8px 24px rgba(0,0,0,0.08); padding: 12px; display: none; z-index: 20; }
    .filter-panel.show { display: block; }
    .filter-row { display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; border-bottom: 1px solid #F3F4F6; }
    .filter-row:last-child { border-bottom: none; }
    .filter-label { color: #374151; font-weight: 600; }
    .filter-options { display: flex; gap: 8px; flex-wrap: wrap; }
    .chip { border: 1px solid #E5E7EB; background: #fff; border-radius: 8px; padding: 6px 10px; font-size: 12px; color: #1F2937; cursor: pointer; }
    .chip.active { background: #E9F3FF; border-color: #CDE3FF; }
    .filter-range { display: flex; align-items: center; gap: 12px; width: 100%; }
    .filter-range input[type=range] { flex: 1; }
    .price-value { background: #F3F4F6; border: 1px solid #E5E7EB; border-radius: 8px; padding: 6px 10px; font-size: 12px; color: #1F2937; }
    .filter-actions { display: flex; justify-content: flex-end; gap: 8px; padding: 10px 12px; }
    .apply-filters { background: #2B7FE6; color: #fff; border: none; border-radius: 8px; padding: 8px 12px; font-size: 13px; }
    .reset-filters { background: #fff; color: #374151; border: 1px solid #E5E7EB; border-radius: 8px; padding: 8px 12px; font-size: 13px; }
    .table-area { padding: 0; }
    .properties-table { direction: rtl; }
    .properties-table thead th { background: #E9F3FF; color: #1F2937; font-weight: 600; border-bottom: none; text-align: right; }
    .properties-table tbody td { color: #374151; text-align: right; }
    .properties-table thead .select-col, .properties-table tbody .select-col { width: 36px; }
    .property-name { display: flex; align-items: center; direction: rtl; flex-direction: row; gap: 8px; }
    .properties-table .property-thumb { width: 36px; height: 36px; border-radius: 10px; object-fit: cover; border: 1px solid #E5E7EB; background: #F3F4F6; }
    .user-cell { display: flex; align-items: center; gap: 8px; }
    .user-avatar { width: 28px; height: 28px; border-radius: 50%; background: #E5E7EB; color: #6B7280; display: flex; align-items: center; justify-content: center; }
    .user-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
    .action-link { color: #2B7FE6; font-weight: 600; text-decoration: none; }
    .status-badge { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; }
    .badge-inprogress { background: #E9F3FF; color: #1F4D8C; border: 1px solid #CDE3FF; }
    .badge-unpublished { background: #F3F4F6; color: #374151; border: 1px solid #E5E7EB; }
    .badge-published { background: #E8FFF3; color: #065F46; border: 1px solid #A7F3D0; }
    .table-footer { padding: 16px 24px; background: #F9FAFB; border-top: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; }
    .pagination-info { font-size: 14px; color: #555; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 14px; display: inline-block; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-top: 10px; }
    /* رينتو: تنسيق الترقيم بنفس صفحة الخصائص */
    .rento-pagination { direction: ltr; }
    .rento-pages { list-style: none; display: flex; align-items: center; gap: 8px; padding: 0; margin: 0; }
    .rento-item {}
    .rento-link { display: inline-flex; align-items: center; justify-content: center; min-width: 40px; height: 36px; padding: 0 12px; border: 1px solid #E0E0E0; border-radius: 8px; background: #ffffff; color: #555; text-decoration: none; font-size: 14px; font-weight: 600; }
    .rento-item .rento-link:hover { border-color: #D0D0D0; background: #F8F8F8; }
    .rento-item.active .rento-link { background: #F6F8FA; border-color: #31C48D; box-shadow: 0 0 0 2px rgba(49,196,141,0.25); color: #111; }
    .rento-item.disabled .rento-link { color: #AAA; background: #FAFAFA; cursor: not-allowed; }
    .rento-ellipsis .rento-link { pointer-events: none; cursor: default; }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('filterToggle');
        const panel = document.getElementById('filterDropdown');
        const priceRange = document.getElementById('priceRange');
        const priceMaxInput = document.getElementById('priceMaxInput');
        const priceMinInput = document.getElementById('priceMinInput');
        const priceRangeValue = document.getElementById('priceRangeValue');
        const typeNameInput = document.getElementById('typeNameInput');
        const cityInput = document.getElementById('cityInput');
        const resetBtn = document.getElementById('resetFilters');
        const acceptSelectedBtn = document.getElementById('acceptSelected');

        // Toggle filter panel
        if (toggleBtn && panel) {
            toggleBtn.addEventListener('click', function (e) {
                e.preventDefault();
                panel.classList.toggle('show');
                panel.setAttribute('aria-hidden', panel.classList.contains('show') ? 'false' : 'true');
            });
            document.addEventListener('click', function (e) {
                if (!panel.contains(e.target) && !toggleBtn.contains(e.target)) {
                    panel.classList.remove('show');
                    panel.setAttribute('aria-hidden', 'true');
                }
            });
        }

        // Range binding
        if (priceRange && priceRangeValue) {
            const updatePrice = (val) => {
                priceRangeValue.textContent = Number(val).toLocaleString();
                priceMaxInput.value = val;
            };
            updatePrice(priceRange.value);
            priceRange.addEventListener('input', (e) => updatePrice(e.target.value));
        }

        // Chips bindings
        document.querySelectorAll('.chip[data-type]').forEach(chip => {
            chip.addEventListener('click', () => {
                document.querySelectorAll('.chip[data-type]').forEach(c => c.classList.remove('active'));
                chip.classList.add('active');
                typeNameInput.value = chip.getAttribute('data-type');
            });
        });
        document.querySelectorAll('.chip[data-city]').forEach(chip => {
            chip.addEventListener('click', () => {
                document.querySelectorAll('.chip[data-city]').forEach(c => c.classList.remove('active'));
                chip.classList.add('active');
                cityInput.value = chip.getAttribute('data-city');
            });
        });

        // Reset filters
        if (resetBtn) {
            resetBtn.addEventListener('click', function () {
                [typeNameInput, cityInput, priceMaxInput, priceMinInput].forEach(i => i && (i.value = ''));
                if (priceRange) {
                    priceRange.value = 1500;
                    priceRangeValue.textContent = '1,500';
                }
            });
        }

        // Accept selected (mock)
        if (acceptSelectedBtn) {
            acceptSelectedBtn.addEventListener('click', function () {
                const selected = Array.from(document.querySelectorAll('.row-select:checked')).map(i => i.value);
                if (selected.length === 0) {
                    alert('لم يتم تحديد أي عنصر.');
                } else {
                    alert('سيتم قبول العناصر المحددة: ' + selected.join(', '));
                }
            });
        }
    });
</script>
@endpush
