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

    // Search functionality
    document.querySelectorAll('.js-refunds-search').forEach(function(el){
        el.addEventListener('keydown', function(e){
            if (e.key === 'Enter') {
                const v = e.target.value;
                const url = new URL(window.location.href);
                if (v) { url.searchParams.set('search', v); } else { url.searchParams.delete('search'); }
                window.location.href = url.toString();
            }
        });
    });

    // Open Refund Details Modal
    function openRefundDetailsModal(refundId) {
        window.currentRefundId = refundId;
        if (refundId === 0) {
            // Show sample data
            document.getElementById('refundDetailsModal').classList.add('show');
            return;
        }

        // Fetch refund details
        fetch(`/dashboard/payments/refunds/${refundId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const refund = data.refund;

                    // Update modal content
                    if (refund.user) {
                        document.getElementById('modalUserName').textContent = refund.user.name;
                        document.getElementById('modalUserEmail').textContent = refund.user.email;
                        document.getElementById('modalUserAvatar').src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(refund.user.name) + '&background=3B82F6&color=fff';
                    }

                    document.getElementById('modalAmount').textContent = (refund.amount || 0) + ' د.ل';
                    document.getElementById('modalAccountNumber').textContent = refund.account_number || '9274639463-32084';
                    document.getElementById('modalBankName').textContent = refund.bank_name || '-';

                    document.getElementById('modalAccountType').textContent = refund.account_type || 'مستأجر';
                    const transferType = refund.request_type === 'wallet' ? 'محفظة إلكترونية' : (refund.request_type === 'cash' ? 'نقدي' : 'حساب بنكي');
                    document.getElementById('modalTransferType').textContent = transferType;
                    const createdAt = refund.created_at ? new Date(refund.created_at) : null;
                    document.getElementById('modalTransferDate').textContent = createdAt ? `${createdAt.getDate()} / ${createdAt.getMonth()+1} / ${createdAt.getFullYear()}` : '25 / 5 / 2025';
                    const balance = (refund.user && refund.user.wallet) ? refund.user.wallet.balance : refund.amount;
                    document.getElementById('modalCurrentBalance').textContent = (balance || 0) + ' د.ل';

                    // Update status
                    const statusBadge = document.getElementById('modalStatus');
                    const statusMap = {
                        'pending': { text: 'بإنتظار الموافقة', class: 'status-pending' },
                        'approved': { text: 'تم التحويل', class: 'status-approved' },
                        'rejected': { text: 'مرفوض', class: 'status-rejected' }
                    };
                    const status = statusMap[refund.status] || statusMap['pending'];
                    statusBadge.textContent = status.text;
                    statusBadge.className = 'status-badge ' + status.class;

                    document.getElementById('refundDetailsModal').classList.add('show');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Show modal with sample data on error
                document.getElementById('refundDetailsModal').classList.add('show');
            });
    }

    // Close Refund Details Modal
    function closeRefundDetailsModal() {
        document.getElementById('refundDetailsModal').classList.remove('show');
    }

    // Switch Tabs
    function switchTab(tabName) {
        // Remove active class from all tabs
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        // Remove active class from all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });

        // Add active class to clicked tab
        event.target.classList.add('active');

        // Show corresponding tab content
        document.getElementById('tab-' + tabName).classList.add('active');
    }

    // Approve Refund
    function approveRefund() {
        if (!window.currentRefundId || window.currentRefundId === 0) {
            alert('لا يوجد طلب محدد');
            return;
        }
        if (!confirm('هل أنت متأكد من الموافقة على طلب السحب؟')) return;

        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        fetch(`/dashboard/payments/refunds/${window.currentRefundId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf || ''
            },
            body: JSON.stringify({ status: 'approved' })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert('تمت الموافقة على الطلب بنجاح');
            } else {
                alert(data.message || 'تعذر تنفيذ العملية');
            }
            closeRefundDetailsModal();
            location.reload();
        })
        .catch(() => {
            alert('حدث خطأ أثناء تنفيذ العملية');
        });
    }

    // Reject Refund
    function rejectRefund() {
        if (!window.currentRefundId || window.currentRefundId === 0) {
            alert('لا يوجد طلب محدد');
            return;
        }
        if (!confirm('هل أنت متأكد من رفض طلب السحب؟')) return;

        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        fetch(`/dashboard/payments/refunds/${window.currentRefundId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf || ''
            },
            body: JSON.stringify({ status: 'rejected' })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert('تم رفض الطلب');
            } else {
                alert(data.message || 'تعذر تنفيذ العملية');
            }
            closeRefundDetailsModal();
            location.reload();
        })
        .catch(() => {
            alert('حدث خطأ أثناء تنفيذ العملية');
        });
    }

    function approveRefundDirect(id) {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        fetch(`/dashboard/payments/refunds/${id}/status`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf || '' },
            body: JSON.stringify({ status: 'approved' })
        }).then(r => r.json()).then(data => { location.reload(); });
    }

    function rejectRefundDirect(id) {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        fetch(`/dashboard/payments/refunds/${id}/status`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf || '' },
            body: JSON.stringify({ status: 'rejected' })
        }).then(r => r.json()).then(data => { location.reload(); });
    }
</script>
