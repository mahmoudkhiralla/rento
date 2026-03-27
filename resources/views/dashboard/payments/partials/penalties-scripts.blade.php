<script>
    // Toggle Filter Dropdown
    function toggleFilterDropdown() {
        const dd = document.getElementById('filterDropdown');
        if (!dd) return;
        dd.style.display = dd.style.display === 'none' ? 'block' : 'none';
    }

    document.getElementById('searchInput')?.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            const v = e.target.value;
            const url = new URL(window.location.href);
            if (v) { url.searchParams.set('search', v); } else { url.searchParams.delete('search'); }
            window.location.href = url.toString();
        }
    });

    (function(){
        const dd = document.getElementById('filterDropdown');
        if (!dd) return;
        const applyBtn = document.getElementById('applyFilters');
        const clearBtn = document.getElementById('clearFilters');
        function apply(){
            const url = new URL(window.location.href);
            const status = document.getElementById('fStatus').value;
            const type = document.getElementById('fType').value;
            const utype = document.getElementById('fUserType').value;
            const df = document.getElementById('fDateFrom').value;
            const dt = document.getElementById('fDateTo').value;
            const amin = document.getElementById('fAmountMin').value;
            const amax = document.getElementById('fAmountMax').value;
            const pp = document.getElementById('fPerPage').value;
            status && status !== 'all' ? url.searchParams.set('status', status) : url.searchParams.delete('status');
            type && type !== 'all' ? url.searchParams.set('type', type) : url.searchParams.delete('type');
            utype && utype !== 'all' ? url.searchParams.set('user_type', utype) : url.searchParams.delete('user_type');
            df ? url.searchParams.set('date_from', df) : url.searchParams.delete('date_from');
            dt ? url.searchParams.set('date_to', dt) : url.searchParams.delete('date_to');
            amin ? url.searchParams.set('amount_min', amin) : url.searchParams.delete('amount_min');
            amax ? url.searchParams.set('amount_max', amax) : url.searchParams.delete('amount_max');
            pp ? url.searchParams.set('per_page', pp) : url.searchParams.delete('per_page');
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }
        function clear(){
            const url = new URL(window.location.href);
            ['status','type','user_type','date_from','date_to','amount_min','amount_max','per_page','page'].forEach(k=>url.searchParams.delete(k));
            window.location.href = url.toString();
        }
        applyBtn?.addEventListener('click', apply);
        clearBtn?.addEventListener('click', clear);
    })();

    (function() {
        const compCard = document.querySelector('.compensations-card');
        if (!compCard) return;
        const rows = compCard.querySelectorAll('.row-grid');
        const percentPill = rows[0]?.querySelector('.row-left .value-pill');
        const fixedPill = rows[2]?.querySelector('.row-left .value-pill');
        function makeInput(pill, id, isPercent) {
            const parent = pill.parentElement;
            const input = document.createElement('input');
            input.type = 'text';
            input.className = 'form-input compact-input';
            input.id = id;
            input.placeholder = isPercent ? '% ' : 'أدخل القيمة';
            parent.replaceChild(input, pill);
            input.focus();
            input.addEventListener('input', function() {
                const numeric = (input.value || '').replace(/[^0-9.]/g, '');
                if (isPercent) {
                    input.value = numeric ? ('% ' + numeric) : '';
                } else {
                    input.value = numeric;
                }
            });
            input.addEventListener('keydown', function(e) { if (e.key === 'Enter') { e.preventDefault(); input.blur(); } });
            input.addEventListener('blur', function() {
                const val = (input.value || '').replace(/[^0-9.]/g, '');
                const payload = {};
                if (id === 'compPercentInline') payload.compensation_percentage = Number(val || 0);
                if (id === 'compFixedInline') payload.compensation_fixed_extra = Number(val || 0);
                fetch('/dashboard/payments/settings', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': (window.CSRF_TOKEN || '') },
                    body: JSON.stringify(payload)
                }).then(() => { pill.textContent = isPercent ? (val ? ('% ' + val) : 'أدخل القيمة') : (val || 'أدخل القيمة'); parent.replaceChild(pill, input); });
            });
        }
        percentPill?.addEventListener('click', function() { makeInput(percentPill, 'compPercentInline', true); });
        fixedPill?.addEventListener('click', function() { makeInput(fixedPill, 'compFixedInline', false); });

        // Apply settings on load
        const s = window.PAYMENT_SETTINGS || {};
        if (percentPill) percentPill.textContent = (s.compensation_percentage ? ('% ' + s.compensation_percentage) : 'أدخل القيمة');
        if (fixedPill) fixedPill.textContent = (s.compensation_fixed_extra ? s.compensation_fixed_extra : 'أدخل القيمة');

        document.querySelectorAll('input[name="compensation_method"]').forEach(r => {
            r.addEventListener('change', function() {
                fetch('/dashboard/payments/settings', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': (window.CSRF_TOKEN || '') },
                    body: JSON.stringify({ compensation_method: this.value })
                }).then(() => {});
            });
        });
    })();

    (function() {
        const penCard = document.querySelector('.penalties-card');
        if (!penCard) return;
        const rows = penCard.querySelectorAll('.row-grid');
        const percentPill = rows[0]?.querySelector('.row-left .value-pill');
        function makeInput(pill, id) {
            const parent = pill.parentElement;
            const input = document.createElement('input');
            input.type = 'text';
            input.className = 'form-input compact-input';
            input.id = id;
            input.placeholder = '% ';
            parent.replaceChild(input, pill);
            input.focus();
            input.addEventListener('input', function() {
                const numeric = (input.value || '').replace(/[^0-9.]/g, '');
                input.value = numeric ? ('% ' + numeric) : '';
            });
            input.addEventListener('keydown', function(e) { if (e.key === 'Enter') { e.preventDefault(); input.blur(); } });
            input.addEventListener('blur', function() {
                const val = (input.value || '').replace(/[^0-9.]/g, '');
                fetch('/dashboard/payments/settings', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': (window.CSRF_TOKEN || '') },
                    body: JSON.stringify({ cancel_penalty_percentage: Number(val || 0) })
                }).then(() => { pill.textContent = val ? ('% ' + val) : 'أدخل القيمة'; parent.replaceChild(pill, input); });
            });
        }
        percentPill?.addEventListener('click', function() { makeInput(percentPill, 'penaltyPercentInline'); });

        // Apply settings on load
        const s2 = window.PAYMENT_SETTINGS || {};
        if (percentPill) percentPill.textContent = (s2.cancel_penalty_percentage ? ('% ' + s2.cancel_penalty_percentage) : 'أدخل القيمة');

        document.querySelectorAll('input[name="cancel_penalty_method"]').forEach(r => {
            r.addEventListener('change', function() {
                fetch('/dashboard/payments/settings', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': (window.CSRF_TOKEN || '') },
                    body: JSON.stringify({ cancel_penalty_method: this.value })
                }).then(() => {});
            });
        });

        const fixedInput = penCard.querySelector('#penaltyFixedValueInput');
        if (fixedInput) {
            fixedInput.addEventListener('input', function() {
                this.value = (this.value || '').replace(/[^0-9.]/g, '');
            });
            fixedInput.addEventListener('blur', function() {
                fetch('/dashboard/payments/settings', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': (window.CSRF_TOKEN || '') },
                    body: JSON.stringify({ cancel_penalty_fixed_value: Number(this.value || 0) })
                }).then(() => {});
            });
        }
    })();

    // Switch main tabs
    document.querySelectorAll('.header-tabs .tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const tabName = this.dataset.tab;

            // Remove active class from all tabs
            document.querySelectorAll('.header-tabs .tab-btn').forEach(b => {
                b.classList.remove('active');
            });

            // Remove active class from all tab contents
            document.querySelectorAll('.main-section > .tab-content').forEach(content => {
                content.classList.remove('active');
            });

            // Add active class to clicked tab
            this.classList.add('active');

            // Show corresponding tab content
            document.getElementById(tabName + '-tab')?.classList.add('active');
        });
    });

    // Open Penalty Modal
    function openPenaltyModal(penaltyId) {

        fetch(`/dashboard/payments/penalties/${penaltyId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const penalty = data.penalty;

                    if (penalty.user) {
                        document.getElementById('prevUserName').textContent = penalty.user.name || '';
                        document.getElementById('prevUserEmail').textContent = penalty.user.email || '';
                        const avatar = penalty.user.avatar || '';
                        const isUrl = /^https?:\/\//.test(avatar || '');
                        document.getElementById('prevUserAvatar').src = isUrl ? avatar : ('https://ui-avatars.com/api/?name=' + encodeURIComponent(penalty.user.name || 'User') + '&background=3B82F6&color=fff');
                        const verified = !!(penalty.user.email_verified_at || penalty.user.id_verified || penalty.user.face_verified);
                        const verifiedEl = document.getElementById('prevVerified');
                        if (verifiedEl) {
                            verifiedEl.innerHTML = verified
                                ? '<i class="fas fa-check-circle" style="color:#3B82F6"></i>'
                                : '<i class="fas fa-exclamation-circle" style="color:#EF4444"></i>';
                        }
                        const accTypeMap = { tenant: 'مستأجر', landlord: 'مؤجر', investor: 'مستثمر', both: 'مؤجر' };
                        document.getElementById('prevAccountType').textContent = accTypeMap[penalty.user.user_type] || 'غير محدد';
                    }

                    const amountNum = Number(penalty.amount || 0);
                    document.getElementById('prevAmount').textContent = amountNum + ' د.ل';

                    const dt = penalty.created_at ? new Date(penalty.created_at) : null;
                    if (dt) {
                        const d = dt.getDate();
                        const m = dt.getMonth() + 1;
                        const y = dt.getFullYear();
                        const hours = dt.getHours();
                        const minutes = String(dt.getMinutes()).padStart(2, '0');
                        const period = hours >= 12 ? 'م' : 'ص';
                        const h12 = hours % 12 || 12;
                        document.getElementById('prevDate').textContent = `${d} / ${m} / ${y} - ${h12}:${minutes} ${period}`;
                    } else {
                        document.getElementById('prevDate').textContent = '—';
                    }

                    const balance = (penalty.user && penalty.user.wallet && penalty.user.wallet.balance) ? Number(penalty.user.wallet.balance) : 0;
                    document.getElementById('prevBalance').textContent = balance + ' د.ل';

                    const typeMap = {
                        'late_payment': 'تأخير الدفع على موعد الحجز',
                        'damage': 'تلف أو تخريب للمعدات',
                        'cancellation': 'إلغاء متأخر للحجز',
                        'violation': 'مخالفة شروط الاستخدام',
                        'compensation': 'تعويض عن الأضرار'
                    };
                    document.getElementById('prevType').textContent = typeMap[penalty.type] || 'غرامة عامة';

                    document.getElementById('prevReason').textContent = penalty.reason || '—';

                    const statusBadge = document.getElementById('prevStatus');
                    const statusMap = {
                        'pending': { text: 'قيد التنفيذ', class: 'status-pending' },
                        'paid': { text: 'تم الخصم', class: 'status-paid' },
                        'cancelled': { text: 'ملغي', class: 'status-cancelled' }
                    };
                    const status = statusMap[penalty.status] || statusMap['pending'];
                    const isCompPaid = (penalty.status === 'paid' && penalty.type === 'compensation');
                    statusBadge.textContent = isCompPaid ? 'تم الدفع' : status.text;
                    const badgeClass = (status.class === 'status-paid' && !isCompPaid) ? 'status-deducted' : status.class;
                    statusBadge.className = 'status-badge ' + badgeClass;

                    document.getElementById('penaltyModal').classList.add('show');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // فشل الجلب؛ لا نعرض بيانات ثابتة
            });
    }

    // Close Penalty Modal
    function closePenaltyModal() {
        document.getElementById('penaltyModal').classList.remove('show');
    }

    // Switch Modal Tabs
    function switchPenaltyTab(tabName) {
        // Remove active class from all tabs
        document.querySelectorAll('#penaltyModal .modal-tabs .tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        // Remove active class from all tab contents
        document.querySelectorAll('#penaltyModal .tab-content').forEach(content => {
            content.classList.remove('active');
        });

        // Add active class to clicked tab
        event.target.classList.add('active');

        // Show corresponding tab content
        document.getElementById('penalty-tab-' + tabName).classList.add('active');
    }

    // Confirm Penalty Payment
    function confirmPenaltyPayment() {
        alert('تم تأكيد دفع الغرامة');
        closePenaltyModal();
    }
</script>
