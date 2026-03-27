<script>
    let currentCardId = null;
    let currentCardStatus = null; // الحالة الحالية للبطاقة
    // Toggle Filter Dropdown
    function toggleFilterDropdown() {
        const dropdown = document.getElementById('filterDropdown');
        dropdown.classList.toggle('show');
    }

    // Toggle Card Count Dropdown
    function toggleCardCountDropdown() {
        const dropdown = document.getElementById('cardCountDropdown');
        dropdown.classList.toggle('show');
    }

    // Select Card Value
    function selectCardValue(value) {
        document.querySelector('[name="export_limit"][value="' + value + '"]').checked = true;
        toggleCardCountDropdown();
        event.preventDefault();
    }

    // Open Issue Card Modal
    function openIssueCardModal() {
        const modal = document.getElementById('issueCardModal');
        if (modal) {
            modal.style.display = 'flex';
        }
    }

    // Close Issue Card Modal
    function closeIssueCardModal() {
        const modal = document.getElementById('issueCardModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    // Submit Issue Card Form
    function submitIssueCard(event) {
        event.preventDefault();

        // Get form data
        const formData = new FormData(event.target);

        // Send AJAX request
        fetch('{{ route('dashboard.payments.cards') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('تم إصدار بطاقة الدفع بنجاح');
                closeIssueCardModal();
                location.reload();
            } else {
                alert('حدث خطأ أثناء إصدار البطاقة');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء إصدار البطاقة');
        });
    }

    // Open Card Details Modal
    function openCardDetailsModal(cardId) {
        currentCardId = cardId;
        if (cardId === 0) {
            // Show sample data
            const modal = document.getElementById('cardDetailsModal');
            if (modal) modal.style.display = 'flex';
            return;
        }

        // Fetch card details
        fetch(`/dashboard/payments/cards/${cardId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const card = data.card;
                    // Helpers: date format and status mapping
                    const formatDate = (d) => {
                        if (!d) return '-';
                        const dt = new Date(d);
                        if (isNaN(dt.getTime())) return '-';
                        const day = dt.getDate();
                        const month = dt.getMonth() + 1;
                        const year = dt.getFullYear();
                        return `${day} / ${month} / ${year}`;
                    };
                    const statusMap = {
                        'pending': { text: 'مشحون', cls: 'status-pending' },
                        'active': { text: 'مصدر', cls: 'status-issued' },
                        'expired': { text: 'منتهي', cls: 'status-expired' },
                        'cancelled': { text: 'ملغي', cls: 'status-cancelled-red' },
                    };

                    document.getElementById('modalCardNumber').textContent = card.card_number;
                    document.getElementById('modalAmount').textContent = card.amount;
                    document.getElementById('modalBalance').textContent = card.balance + ' د.ل';
                    const rewardsEl = document.getElementById('modalRewards');
                    if (rewardsEl) {
                        const rewardsVal = (card.rewards !== undefined && card.rewards !== null) ? card.rewards : '—';
                        rewardsEl.textContent = rewardsVal + ' د.ل';
                    }
                    document.getElementById('modalIssueDate').textContent = formatDate(card.issue_date);
                    document.getElementById('modalExpiryDate').textContent = formatDate(card.expiry_date);
                    const statusBadge = document.getElementById('modalStatus');
                    const sm = statusMap[card.status] || statusMap['pending'];
                    statusBadge.textContent = sm.text;
                    statusBadge.className = 'status-badge ' + sm.cls;
                    currentCardStatus = card.status;
                    const notesEl = document.getElementById('modalNotes');
                    if (notesEl) {
                        notesEl.textContent = card.notes || 'لا توجد ملاحظات';
                    }

                    if (card.user) {
                        document.getElementById('modalUserName').textContent = card.user.name || 'اسم المستخدم';
                        document.getElementById('modalUserEmail').textContent = card.user.email || '—';
                        // Resolve avatar: prefer user's uploaded/avatar URL, fallback to UI Avatars
                        const storageBase = "{{ asset('storage') }}";
                        let avatarSrc = null;
                        const av = card.user.avatar;
                        if (av && typeof av === 'string' && av.trim().length > 0) {
                            const trimmed = av.trim().replace(/^\/+/, '');
                            if (/^https?:\/\//i.test(trimmed)) {
                                avatarSrc = trimmed;
                            } else {
                                avatarSrc = `${storageBase}/${trimmed}`;
                            }
                        } else {
                            avatarSrc = `https://ui-avatars.com/api/?name=${encodeURIComponent(card.user.name || 'User')}&background=3B82F6&color=fff`;
                        }
                        const avatarEl = document.getElementById('modalUserAvatar');
                        if (avatarEl) {
                            avatarEl.src = avatarSrc;
                        }
                    }

                    // Enable/disable Stop button based on business rules
                    const stopBtn = document.getElementById('stopCardBtn');
                    const isSold = !!card.user_id; // مباع إذا كان مرتبط بمستخدم
                    const canStop = (card.status === 'active') && !isSold; // يُفعّل فقط في حالة الإصدار وغير مباع
                    if (stopBtn) {
                        stopBtn.disabled = !canStop;
                        stopBtn.title = canStop ? '' : 'لا يمكن إيقاف البطاقة إلا في حالة الإصدار. حالات: مباع / مشحون / ملغي / منتهي غير قابلة للإيقاف';
                    }

                    const modal = document.getElementById('cardDetailsModal');
                    if (modal) modal.style.display = 'flex';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('حدث خطأ أثناء تحميل تفاصيل البطاقة');
            });
    }

    // Close Card Details Modal
    function closeCardDetailsModal() {
        const modal = document.getElementById('cardDetailsModal');
        if (modal) modal.style.display = 'none';
    }

    // Confirm Card Details
    function confirmCardDetails() {
        if (!currentCardId) {
            closeCardDetailsModal();
            return;
        }
        const stopBtn = document.getElementById('stopCardBtn');
        if (stopBtn && stopBtn.disabled) {
            alert('لا يمكن إيقاف هذه البطاقة إلا في حالة الإصدار');
            return;
        }
        if (currentCardStatus === 'cancelled' || currentCardStatus === 'expired') {
            alert('البطاقات الملغية أو المنتهية لا يمكن إعادة تفعيلها');
            return;
        }
        fetch(`/dashboard/payments/cards/${currentCardId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ status: 'cancelled' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('تم إيقاف البطاقة بنجاح');
                closeCardDetailsModal();
                location.reload();
            } else {
                alert(data.message || 'تعذر إيقاف البطاقة');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء تحديث حالة البطاقة');
        });
    }

    // Copy Card Number
    function copyCardNumber() {
        const cardNumber = document.getElementById('modalCardNumber').textContent;
        navigator.clipboard.writeText(cardNumber).then(() => {
            alert('تم نسخ رقم البطاقة');
        });
    }

    // Show Notes
    function showNotes(notes) {
        alert(notes);
    }

    // Toggle Action Menu
    function toggleActionMenu(cardId) {
        alert('قائمة الإجراءات للبطاقة رقم: ' + cardId);
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.filter-dropdown')) {
            document.getElementById('filterDropdown')?.classList.remove('show');
        }
        if (!event.target.closest('.input-with-dropdown')) {
            document.getElementById('cardCountDropdown')?.classList.remove('show');
        }
    });

    // Search functionality
    document.getElementById('searchInput')?.addEventListener('input', function(e) {
        const searchValue = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('.data-table tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchValue) ? '' : 'none';
        });
    });

    // Suggested value → amount sync
    document.getElementById('suggestedValue')?.addEventListener('change', function(e) {
        const amountInput = document.querySelector('input[name="amount"]');
        if (amountInput && e.target.value) {
            amountInput.value = e.target.value;
        }
    });
</script>
