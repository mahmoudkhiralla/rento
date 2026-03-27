<script>
    // Toggle Filter Dropdown
    function toggleFilterDropdown() {
        const dropdown = document.getElementById('filterDropdown');
        dropdown.classList.toggle('show');
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.filter-dropdown')) {
            document.getElementById('filterDropdown')?.classList.remove('show');
        }
    });

    // Apply filter
    function applyTransactionsFilter(key, value) {
        const url = new URL(window.location.href);
        if (value === 'all') {
            url.searchParams.delete(key);
        } else {
            url.searchParams.set(key, value);
        }
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }

    // Search functionality
    document.getElementById('searchInput')?.addEventListener('input', function(e) {
        const searchValue = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('.data-table tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchValue) ? '' : 'none';
        });
    });

    // Open Transaction Modal
    let __txnRefreshTimer = null;
    let __txnRefreshUrl = null;

    function openTransactionModal(transactionId) {
        if (transactionId === 0) {
            // Show sample data
            document.getElementById('transactionModal').classList.add('show');
            return;
        }
        const idStr = String(transactionId);
        let source = 'txn';
        let realId = idStr;
        if (idStr.includes('-')) {
            const parts = idStr.split('-');
            source = parts[0];
            realId = parts[1];
        }
        let url = `/dashboard/payments/transactions/${realId}`;
        if (source === 'penalty') url = `/dashboard/payments/penalties/${realId}`;
        else if (source === 'refund') url = `/dashboard/payments/refunds/${realId}`;
        __txnRefreshUrl = url;
        // Fetch details
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const entity = data.transaction || data.penalty || data.refund;
                    const normalized = {
                        user: entity.user,
                        amount: entity.amount,
                        created_at: entity.created_at,
                        id: realId,
                        type: source === 'commission' ? 'commission' : (entity.type || source),
                        status: source === 'refund' ? (entity.status === 'approved' ? 'completed' : (entity.status === 'rejected' ? 'failed' : 'pending')) : (entity.status || 'completed')
                    };

                    // Update modal content
                    if (normalized.user) {
                        document.getElementById('transModalUserName').textContent = normalized.user.name || '';
                        document.getElementById('transModalUserEmail').textContent = normalized.user.email || '';
                        const avatar = normalized.user.avatar || '';
                        const isUrl = /^https?:\/\//.test(avatar || '');
                        document.getElementById('transModalUserAvatar').src = isUrl ? avatar : ('https://ui-avatars.com/api/?name=' + encodeURIComponent(normalized.user.name || 'User') + '&background=3B82F6&color=fff');
                        const verified = !!(normalized.user.email_verified_at || normalized.user.id_verified || normalized.user.face_verified);
                        document.getElementById('transModalVerification').innerHTML = verified
                            ? '<i class="fas fa-check-circle" style="color:#3B82F6"></i>'
                            : '<i class="fas fa-exclamation-circle" style="color:#EF4444"></i>';
                        const userType = normalized.user.user_type || '';
                        const accTypeMap = { tenant: 'مستأجر', landlord: 'مؤجر', investor: 'مستثمر' };
                        document.getElementById('transModalAccountType').textContent = accTypeMap[userType] || 'غير محدد';
                    }

                    const amountNum = Number(normalized.amount || 0);
                    document.getElementById('transModalAmount').textContent = amountNum + ' د.ل';
                    const elCurrency = document.getElementById('transModalCurrency');
                    if (elCurrency) elCurrency.textContent = 'د.ل';
                    const dt = normalized.created_at ? new Date(normalized.created_at) : null;
                    const d = dt ? dt.getDate() : 25;
                    const m = dt ? (dt.getMonth() + 1) : 5;
                    const y = dt ? dt.getFullYear() : 2025;
                    document.getElementById('transModalDate').textContent = `${d} / ${m} / ${y}`;
                    const elRef = document.getElementById('transModalRef');
                    if (elRef) elRef.textContent = normalized.id || '636054';
                    const balance = (normalized.user && normalized.user.wallet && normalized.user.wallet.balance) ? Number(normalized.user.wallet.balance) : 0;
                    document.getElementById('transModalBalance').textContent = balance + ' د.ل';

                    // Update type
                    const typeMap = {
                        'payment': 'Payment Received من عميل',
                        'commission': 'Commission Added عمولة',
                        'penalty': 'Fine Collected غرامة من مالك',
                        'refund': 'Refund to Client تعويض عميل',
                        'withdraw': 'Withdrawal سحب بنكي',
                        'deposit': 'Wallet Recharge شحن محفظة'
                    };
                    document.getElementById('transModalType').textContent = typeMap[normalized.type] || 'عملية مالية';

                    // Update status
                    const statusBadge = document.getElementById('transModalStatus');
                    const statusMap = {
                        'pending': { text: 'قيد الانتظار', class: 'status-pending' },
                        'completed': { text: 'تم الاكتمال', class: 'status-completed' },
                        'failed': { text: 'فشل التحويل', class: 'status-failed' },
                        'cancelled': { text: 'ملغي', class: 'status-cancelled' },
                        'paid': { text: 'تم الدفع', class: 'status-completed' },
                        'approved': { text: 'تمت الموافقة', class: 'status-completed' },
                        'rejected': { text: 'مرفوض', class: 'status-failed' }
                    };
                    const status = statusMap[normalized.status] || statusMap['pending'];
                    statusBadge.textContent = status.text;
                    statusBadge.className = 'status-badge ' + status.class;

                    document.getElementById('transactionModal').classList.add('show');

                    if (__txnRefreshTimer) clearInterval(__txnRefreshTimer);
                    __txnRefreshTimer = setInterval(() => {
                        if (!__txnRefreshUrl) return;
                        fetch(__txnRefreshUrl)
                            .then(r => r.json())
                            .then(d => {
                                const ent = d.transaction || d.penalty || d.refund;
                                const user = ent && ent.user ? ent.user : null;
                                const b = (user && user.wallet && user.wallet.balance) ? Number(user.wallet.balance) : null;
                                if (b !== null) {
                                    document.getElementById('transModalBalance').textContent = b + ' د.ل';
                                }
                                const amt = ent && ent.amount ? Number(ent.amount) : null;
                                if (amt !== null) {
                                    document.getElementById('transModalAmount').textContent = amt + ' د.ل';
                                }
                                const st = ent && ent.status ? String(ent.status) : null;
                                if (st) {
                                    const statusBadge = document.getElementById('transModalStatus');
                                    const statusMap = {
                                        'pending': { text: 'قيد الانتظار', class: 'status-pending' },
                                        'completed': { text: 'تم الاكتمال', class: 'status-completed' },
                                        'failed': { text: 'فشل التحويل', class: 'status-failed' },
                                        'cancelled': { text: 'ملغي', class: 'status-cancelled' },
                                        'paid': { text: 'تم الدفع', class: 'status-completed' },
                                        'approved': { text: 'تمت الموافقة', class: 'status-completed' },
                                        'rejected': { text: 'مرفوض', class: 'status-failed' }
                                    };
                                    const sm = statusMap[st] || statusMap['pending'];
                                    statusBadge.textContent = sm.text;
                                    statusBadge.className = 'status-badge ' + sm.class;
                                }
                            })
                            .catch(() => {});
                    }, 4000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Show modal with sample data on error
                document.getElementById('transactionModal').classList.add('show');
            });
    }

    // Close Transaction Modal
    function closeTransactionModal() {
        document.getElementById('transactionModal').classList.remove('show');
        if (__txnRefreshTimer) { clearInterval(__txnRefreshTimer); __txnRefreshTimer = null; }
        __txnRefreshUrl = null;
    }

    // Switch Tabs
    function switchTransactionTab(tabName) {
        // Remove active class from all tabs
        document.querySelectorAll('.modal-tabs .tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        // Remove active class from all tab contents
        document.querySelectorAll('#transactionModal .tab-content').forEach(content => {
            content.classList.remove('active');
        });

        // Add active class to clicked tab
        event.target.classList.add('active');

        // Show corresponding tab content
        document.getElementById('trans-tab-' + tabName).classList.add('active');
    }

    // Confirm/Edit functions removed as per request; only cancel remains
</script>
