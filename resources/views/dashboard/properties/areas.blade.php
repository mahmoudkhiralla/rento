@extends('dashboard.layouts.app')

@section('title', 'تصنيفات الإيجارات والعقارات')

@section('content')
    <div class="areas-container">
        <h1 class="page-main-title">تصنيفات الإيجارات والعقارات</h1>

        <!-- Monthly Rental Card -->
        @php
            $monthlyTypes = collect($monthlyRentalTypes ?? []);
            $monthlyAllActive = $monthlyTypes->every(function($t){ return (bool)($t->is_active ?? true); });
        @endphp
        <div class="rental-category-card" data-rental="شهري">
            <div class="card-header-section">
                <div class="header-left">
                    <button class="swap-btn">
                        <i class="fas fa-arrows-up-down"></i>
                    </button>
                    <h2 class="category-title">الإيجار الشهري</h2>
                </div>
                <div class="header-actions">
                    <button class="btn-action btn-add-type">
                        <i class="fas fa-plus"></i>
                        إضافة نوع عقار
                    </button>
                    <button class="btn-action btn-stop-category {{ $monthlyAllActive ? '' : 'btn-activate-category' }}" data-rental-type="شهري">
                        {{ $monthlyAllActive ? 'إيقاف التصنيفات' : 'تفعيل التصنيفات' }}
                    </button>
                </div>
            </div>

            <div class="table-wrapper">
                <div class="table-head-row">
                    <div class="col-type">أنواع العقارات</div>
                    <div class="col-ads">الإعلانات المنشورة</div>
                    <div class="col-actions"></div>
                </div>

                @forelse($monthlyTypes as $type)
                    <div class="table-body-row" data-id="{{ $type->id }}" data-active="{{ ($type->is_active ?? true) ? 'true' : 'false' }}" data-rental="شهري">
                        <div class="col-type">
                            <span class="type-name">{{ $type->name }}</span>
                        </div>
                        <div class="col-ads">
                            <span class="ads-count">{{ number_format((int)($type->ads_count ?? 0)) }}</span>
                        </div>
                        <div class="col-actions">
                            <a href="#" class="action-link link-edit">تعديل</a>
                            @if(($type->is_active ?? true))
                                <a href="#" class="action-link link-stop">إيقاف</a>
                            @else
                                <a href="#" class="action-link link-activate">تفعيل</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="table-body-row"><div class="col-type">لا توجد أنواع مسجلة</div></div>
                @endforelse
            </div>
        </div>

        <!-- Daily Rental Card -->
        @php
            $dailyTypes = collect($dailyRentalTypes ?? []);
            $dailyAllActive = $dailyTypes->every(function($t){ return (bool)($t->is_active ?? true); });
        @endphp
        <div class="rental-category-card" data-rental="يومي">
            <div class="card-header-section">
                <div class="header-left">
                    <button class="swap-btn">
                        <i class="fas fa-arrows-up-down"></i>
                    </button>
                    <h2 class="category-title">الإيجار اليومي</h2>
                </div>
                <div class="header-actions">
                    <button class="btn-action btn-add-type">
                        <i class="fas fa-plus"></i>
                        إضافة نوع عقار
                    </button>
                    <button class="btn-action btn-stop-category {{ $dailyAllActive ? '' : 'btn-activate-category' }}" data-rental-type="يومي">
                        {{ $dailyAllActive ? 'إيقاف التصنيفات' : 'تفعيل التصنيفات' }}
                    </button>
                </div>
            </div>

            <div class="table-wrapper">
                <div class="table-head-row">
                    <div class="col-type">أنواع العقارات</div>
                    <div class="col-ads">الإعلانات المنشورة</div>
                    <div class="col-actions"></div>
                </div>

                @forelse($dailyTypes as $type)
                    <div class="table-body-row" data-id="{{ $type->id }}" data-active="{{ ($type->is_active ?? true) ? 'true' : 'false' }}" data-rental="يومي">
                        <div class="col-type">
                            <span class="type-name">{{ $type->name }}</span>
                        </div>
                        <div class="col-ads">
                            <span class="ads-count">{{ number_format((int)($type->ads_count ?? 0)) }}</span>
                        </div>
                        <div class="col-actions">
                            <a href="#" class="action-link link-edit">تعديل</a>
                            @if(($type->is_active ?? true))
                                <a href="#" class="action-link link-stop">إيقاف</a>
                            @else
                                <a href="#" class="action-link link-activate">تفعيل</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="table-body-row"><div class="col-type">لا توجد أنواع مسجلة</div></div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Edit Type Modal -->
    <div id="editTypeModal" class="edit-modal-overlay" style="display: none;">
        <div class="edit-modal" role="dialog" aria-modal="true" aria-labelledby="editTypeTitle">
            <div class="modal-header">
                <h3 id="editTypeTitle">تعديل نوع العقار</h3>
            </div>
            <div class="modal-body">
                <label for="editTypeName">اسم نوع العقار</label>
                <div class="select-wrapper">
                    <input id="editTypeName" class="form-select" type="text" placeholder="اكتب اسم التصنيف" />
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel">إلغاء</button>
                <button type="button" class="btn-save"><i class="fas fa-check"></i> حفظ التعديل</button>
            </div>
        </div>
    </div>

    <!-- Add Type Modal -->
    <div id="addTypeModal" class="add-modal-overlay" style="display: none;">
        <div class="add-modal-card">
            <div class="add-modal-header">
                <h3 class="add-modal-title">إضافة نوع عقار</h3>
            </div>
            <div class="add-modal-body">
                <form id="addTypeForm">
                    <div class="add-form-group">
                        <label class="add-form-label">نوع العقار</label>
                        <input id="newTypeName" class="add-form-input" type="text" placeholder="ادخل اسم التصنيف" required />
                    </div>
                    <div class="add-modal-actions">
                        <button type="submit" class="add-btn-submit">
                            <i class="fas fa-plus"></i>
                            إضافة
                        </button>
                        <button type="button" class="add-btn-cancel" onclick="closeAddModal()">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('editTypeModal');
            const nameInput = document.getElementById('editTypeName');
            const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const ROUTE_UPDATE_TYPE = (id) => `/dashboard/property-types/${id}/update`;

            const openModal = (defaultName, typeId) => {
                nameInput.value = defaultName || '';
                modal.dataset.typeId = typeId || '';
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                nameInput.focus();
            };
            const closeModal = () => {
                modal.style.display = 'none';
                document.body.style.overflow = '';
                modal.dataset.typeId = '';
            };

            // تفويض النقر على روابط التعديل ليعمل مع الصفوف الجديدة أيضًا
            document.querySelector('.areas-container').addEventListener('click', function (e) {
                const link = e.target.closest('.link-edit');
                if (!link) return;
                e.preventDefault();
                const row = link.closest('.table-body-row');
                const id = row?.getAttribute('data-id');
                const nameEl = row ? row.querySelector('.type-name') : null;
                const name = nameEl ? nameEl.textContent.trim() : '';
                openModal(name, id);
            });

            // إغلاق بالنقر على الخلفية
            modal.addEventListener('click', function (e) {
                if (e.target === modal) closeModal();
            });
            // إلغاء
            modal.querySelector('.btn-cancel').addEventListener('click', closeModal);
            // حفظ: إرسال الاسم الجديد وتحديث الصف
            modal.querySelector('.btn-save').addEventListener('click', async function () {
                const id = modal.dataset.typeId;
                const newName = (nameInput.value || '').trim();
                if (!id) return closeModal();
                if (newName.length < 2) { alert('فضلاً اكتب اسمًا صالحًا.'); return; }
                try {
                    const res = await fetch(ROUTE_UPDATE_TYPE(id), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': CSRF_TOKEN
                        },
                        body: new URLSearchParams({ name: newName })
                    });
                    if (!res.ok) throw new Error('تعذر حفظ التعديل');
                    const data = await res.json();
                    const row = document.querySelector(`.table-body-row[data-id="${id}"]`);
                    if (row) {
                        const nameEl = row.querySelector('.type-name');
                        if (nameEl) nameEl.textContent = data.name || newName;
                    }
                    closeModal();
                } catch (err) {
                    alert(err.message || 'حدث خطأ أثناء حفظ التعديل');
                }
            });
        });
    </script>

    <script>
        // Close Add Modal Function
        function closeAddModal() {
            const modal = document.getElementById('addTypeModal');
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const addOverlay = document.getElementById('addTypeModal');
            const addInput = document.getElementById('newTypeName');
            const addForm = document.getElementById('addTypeForm');
            const addSubmit = addOverlay ? addOverlay.querySelector('.add-btn-submit') : null;

            if (!addOverlay || !addInput || !addForm || !addSubmit) return;

            let currentAddTarget = null;

            const openAdd = (targetWrapper) => {
                currentAddTarget = targetWrapper || null;
                addInput.value = '';
                addSubmit.disabled = true;
                addOverlay.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                addInput.focus();
            };

            // Open modal when clicking add button
            document.querySelector('.areas-container').addEventListener('click', function (e) {
                const addTrigger = e.target.closest('.btn-add-type');
                if (addTrigger) {
                    e.preventDefault();
                    const card = addTrigger.closest('.rental-category-card');
                    const targetWrapper = card ? card.querySelector('.table-wrapper') : null;
                    openAdd(targetWrapper);
                }
            });

            // Close modal when clicking overlay
            addOverlay.addEventListener('click', function (e) {
                if (e.target === addOverlay) {
                    closeAddModal();
                }
            });

            // Enable/disable submit button based on input
            addInput.addEventListener('input', function () {
                const hasText = addInput.value.trim().length > 0;
                addSubmit.disabled = !hasText;
            });

            // Handle form submission: حفظ عبر الباك إند وإنشاء صف جديد
            addForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                if (!currentAddTarget) return;
                const name = addInput.value.trim();
                if (!name || addSubmit.disabled) return;

                const card = currentAddTarget.closest('.rental-category-card');
                const rentalType = card ? (card.dataset.rental || 'شهري') : 'شهري';
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                try {
                    const res = await fetch("{{ route('dashboard.property-types.store') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken || ''
                        },
                        body: JSON.stringify({ name, rental_type: rentalType })
                    });
                    if (!res.ok) throw new Error('فشل حفظ النوع');
                    const data = await res.json();

                    const row = document.createElement('div');
                    row.className = 'table-body-row';
                    row.setAttribute('data-id', data.id);
                    row.setAttribute('data-active', String(!!data.is_active));
                    row.setAttribute('data-rental', rentalType);
                    row.innerHTML = `
                        <div class="col-type"><span class="type-name">${escapeHtml(data.name)}</span></div>
                        <div class="col-ads"><span class="ads-count">${Number(data.ads_count || 0).toLocaleString()}</span></div>
                        <div class="col-actions">
                            <a href="#" class="action-link link-edit">تعديل</a>
                            <a href="#" class="action-link link-stop">إيقاف</a>
                        </div>
                    `;
                    currentAddTarget.appendChild(row);

                    // بعد الإضافة، حدّث زر إيقاف/تفعيل التصنيفات في الهيدر بناءً على الحالة العامة
                    updateCategoryToggleButton(card);

                    closeAddModal();
                } catch (err) {
                    alert(err.message || 'حدث خطأ أثناء إضافة النوع');
                }
            });

            function escapeHtml(str) {
                const div = document.createElement('div');
                div.textContent = str;
                return div.innerHTML;
            }
        });
    </script>

    <script>
        // راوتات
        const ROUTE_TOGGLE_ONE = (id) => `/dashboard/property-types/${id}/toggle`;
        const ROUTE_TOGGLE_BY_RENTAL = `{{ route('dashboard.property-types.toggle-by-rental') }}`;
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        function setLinkState(linkEl, isActive) {
            if (!linkEl) return;
            if (isActive) {
                linkEl.textContent = 'إيقاف';
                linkEl.classList.remove('link-activate');
                linkEl.classList.add('link-stop');
            } else {
                linkEl.textContent = 'تفعيل';
                linkEl.classList.remove('link-stop');
                linkEl.classList.add('link-activate');
            }
        }

        function updateCategoryToggleButton(card) {
            if (!card) return;
            const btn = card.querySelector('.btn-stop-category');
            const rows = card.querySelectorAll('.table-body-row');
            let allActive = true;
            rows.forEach(r => { if (r.getAttribute('data-active') !== 'true') { allActive = false; } });
            if (allActive) {
                btn.textContent = 'إيقاف التصنيفات';
                btn.classList.remove('btn-activate-category');
            } else {
                btn.textContent = 'تفعيل التصنيفات';
                btn.classList.add('btn-activate-category');
            }
        }

        // تبديل حالة نوع منفرد
        document.querySelector('.areas-container').addEventListener('click', async function (e) {
            const toggleLink = e.target.closest('.link-stop, .link-activate');
            if (!toggleLink) return;
            e.preventDefault();

            const row = toggleLink.closest('.table-body-row');
            const id = row?.getAttribute('data-id');
            const activeNow = row?.getAttribute('data-active') === 'true';
            if (!id) return;

            try {
                const res = await fetch(ROUTE_TOGGLE_ONE(id), {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: new URLSearchParams({ active: String(!activeNow) })
                });
                if (!res.ok) throw new Error('تعذر تبديل حالة النوع');
                const data = await res.json();
                const newActive = !!data.is_active;
                row.setAttribute('data-active', String(newActive));
                setLinkState(toggleLink, newActive);
                updateCategoryToggleButton(row.closest('.rental-category-card'));
            } catch (err) {
                alert(err.message || 'حدث خطأ أثناء التبديل');
            }
        });

        // تبديل جماعي حسب نوع الإيجار (زر الهيدر)
        document.querySelector('.areas-container').addEventListener('click', async function (e) {
            const btn = e.target.closest('.btn-stop-category');
            if (!btn) return;
            e.preventDefault();
            const card = btn.closest('.rental-category-card');
            const rentalType = btn.getAttribute('data-rental-type') || card?.dataset.rental || 'شهري';
            const rows = card.querySelectorAll('.table-body-row');
            // إن كان هناك أي صف غير نشط، سنفعّل الجميع، وإلا سنوقف الجميع
            let allActive = true;
            rows.forEach(r => { if (r.getAttribute('data-active') !== 'true') { allActive = false; } });
            const targetActive = !allActive;

            try {
                const res = await fetch(ROUTE_TOGGLE_BY_RENTAL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: new URLSearchParams({ rental_type: rentalType, active: String(targetActive) })
                });
                if (!res.ok) throw new Error('تعذر التبديل الجماعي');
                // حدّث واجهة كل الصفوف
                rows.forEach(r => {
                    r.setAttribute('data-active', String(targetActive));
                    const link = r.querySelector('.col-actions .action-link:last-child');
                    setLinkState(link, targetActive);
                });
                updateCategoryToggleButton(card);
            } catch (err) {
                alert(err.message || 'حدث خطأ أثناء التبديل الجماعي');
            }
        });
    </script>

    <style>
        .swap-btn {
            background: #fff;
            border: 1px solid #3B82F6; /* الأزرق */
            color: #3B82F6;
            font-size: 18px;
            border-radius: 12px;
            padding: 6px 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s ease-in-out;
        }

        .swap-btn:hover {
            background: #3B82F6;
            color: #fff;
            transform: scale(1.05);
        }

        /* Container */
        .areas-container {
            padding: 16px;
            background: #F3F4F6;
            min-height: 100vh;
        }

        /* Page Title */
        .page-main-title {
            font-size: 18px;
            font-weight: 700;
            color: #1F2937;
            margin: 0 0 16px 0;
            text-align: right;
        }

        /* Rental Category Card */
        .rental-category-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 16px;
            overflow: hidden;
        }

        /* Card Header */
        .card-header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            border-bottom: 1px solid #E5E7EB;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .category-title {
            font-size: 16px;
            font-weight: 700;
            color: #1F2937;
            margin: 0;
        }

        .btn-sort-order {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #EEF2FF;
            border: none;
            color: #4F46E5;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-sort-order:hover {
            background: #E0E7FF;
        }

        .header-actions {
            display: flex;
            gap: 8px;
        }

        .btn-action {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: 1px solid;
        }

        .btn-stop-category {
            background: white;
            color: #DC2626;
            border-color: #DC2626;
        }

        .btn-stop-category:hover {
            background: #FEE2E2;
        }

        /* زر التفعيل على مستوى الفئة */
        .btn-activate-category {
            background: white;
            color: #10B981;
            border-color: #10B981;
        }
        .btn-activate-category:hover { background: #D1FAE5; }

        .btn-add-type {
            background: white;
            color: #2563EB;
            border-color: #2563EB;
        }

        .btn-add-type:hover {
            background: #DBEAFE;
        }

        /* Edit Modal */
        .edit-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        .edit-modal {
            width: 420px;
            max-width: 92vw;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .modal-header {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px 16px;
            border-bottom: 1px solid #E5E7EB;
            font-weight: 700;
            color: #111827;
        }
        .modal-body { padding: 12px 16px; }
        .modal-body label {
            display: block;
            font-size: 12px;
            color: #6B7280;
            margin-bottom: 6px;
            text-align: right;
        }
        .select-wrapper { position: relative; }
        .form-select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            font-size: 14px;
            color: #111827;
            background: #fff;
        }
        .modal-actions {
            display: flex;
            justify-content: space-between; /* إلغاء يسار، حفظ يمين */
            padding: 10px 16px 12px;
            border-top: 1px solid #F3F4F6;
        }
        .btn-cancel {
            background: #fff;
            color: #DC2626;
            border: 1px solid #DC2626;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 600;
        }
        .btn-cancel:hover { background: #FEE2E2; }
        .btn-save {
            background: #fff;
            color: #2563EB;           /* أزرق مماثل لزر الإضافة */
            border: 1px solid #2563EB;/* إطار أزرق */
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-save:hover { background: #DBEAFE; }

        /* Add Button (Modal) */
        .btn-add {
            background: #F3F4F6;
            color: #9CA3AF; /* رمادي خافت عند التعطيل */
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-add:disabled { opacity: 1; cursor: not-allowed; }
        .btn-add.is-active {
            background: #fff;
            color: #2563EB;
            border-color: #2563EB;
        }
        .btn-add.is-active:hover { background: #DBEAFE; }
        .btn-add .btn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            border-radius: 6px;
            background: #E5E7EB;
            color: #9CA3AF;
            font-size: 12px;
        }
        .btn-add.is-active .btn-icon {
            background: #DBEAFE;
            color: #2563EB;
        }

        /* Table Wrapper */
        .table-wrapper {
            padding: 0;
        }

        /* Table Head Row */
        .table-head-row {
            display: grid;
            grid-template-columns: 1fr 160px 1fr; /* يمين ويسار متساويان لضمان تمركز الوسط */
            gap: 10px;
            padding: 10px 16px;
            background: #EFF6FF;
            color: #1E40AF;
            font-weight: 600;
            font-size: 13px;
        }

        .col-type {
            text-align: right;
        }

        .col-ads {
            text-align: center;
            display: flex;             /* ضمان تمركز المحتوى بصرف النظر عن اتجاه النص */
            justify-content: center;
        }

        .col-actions {
            text-align: left;
            display: flex;             /* عرض أفقي للعناصر */
            justify-content: flex-end; /* محاذاة العناصر لليسار فعليًا */
            justify-self: left;         /* تثبيت الخلية نفسها بمحاذاة يسار الشبكة */
        }

        /* Table Body Row */
        .table-body-row {
            display: grid;
            grid-template-columns: 1fr 160px 1fr; /* تماثل الأعمدة لتمركز الوسط */
            gap: 10px;
            padding: 10px 16px;
            border-bottom: 1px solid #F3F4F6;
            align-items: center;
        }

        .table-body-row:last-child {
            border-bottom: none;
        }

        .table-body-row:hover {
            background: #F9FAFB;
        }

        .type-name {
            font-size: 14px;
            font-weight: 600;
            color: #1F2937;
        }

        .ads-count {
            font-size: 14px;
            color: #4B5563;
            font-weight: 500;
        }

        /* Action Links */
        .col-actions {
            display: flex;
            gap: 8px;
            justify-content: flex-start;
        }

        .action-link {
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            padding-left: 5px;
        }

        .link-edit {
            color: #2563EB;
            padding-left: 35px;
        }

        .link-edit:hover {
            color: #1D4ED8;
            text-decoration: underline;
        }

        .link-stop {
            color: #DC2626;
        }

        .link-stop:hover {
            color: #B91C1C;
            text-decoration: underline;
        }

        .link-activate { color: #10B981; }
        .link-activate:hover { color: #059669; text-decoration: underline; }

        /* Responsive */
        @media (max-width: 768px) {
            .areas-container {
                padding: 16px;
            }

            .card-header-section {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .header-actions {
                width: 100%;
                flex-direction: column;
            }

            .btn-action {
                width: 100%;
                justify-content: center;
            }

            .table-head-row,
            .table-body-row {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .col-type,
            .col-ads,
            .col-actions {
                text-align: left;
            }

            .col-actions {
                justify-content: flex-end;
            }
        }

        /* Add Type Modal Styles */
        .add-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(17, 17, 17, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1050;
        }

        .add-modal-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            width: 320px;
            padding: 18px 20px;
            animation: fadeInUp 0.25s ease;
        }

        /* إلغاء حدود الهيدر الداخلية لأن البطاقة أصبحت ذات حشوة شاملة */
        .add-modal-header { padding: 0; border: none; }
        .add-modal-body { padding: 0; }

        .add-modal-title {
            font-size: 16px;
            font-weight: 700;
            color: #333;
            text-align: center;
            margin-bottom: 14px;
        }

        .add-form-group { margin-bottom: 14px; }

        .add-form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
        }

        .add-form-input {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            color: #333;
            outline: none;
            transition: border-color 0.2s;
        }
        .add-form-input:focus { border-color: #3B82F6; }

        .add-modal-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 16px;
        }

        .add-btn-cancel {
            border: 1px solid #fca5a5;
            background: #fff;
            color: #dc2626;
            font-size: 13px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 6px;
            transition: 0.2s;
        }
        .add-btn-cancel:hover { background: #fee2e2; }

        .add-btn-submit {
            border: 1px solid #ddd;
            background: #f9f9f9;
            color: #777;
            font-size: 13px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: 0.2s;
        }
        .add-btn-submit i { font-size: 12px; }
        .add-btn-submit:hover { background: #f3f4f6; }

        @keyframes fadeInUp {
            from { transform: translateY(15px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
@endsection
