@extends('dashboard.layouts.app')

@section('title', 'الإشعارات المرسلة')

@section('content')
    <div class="notifications-page-container">
        <!-- Header Section -->
        <div class="page-header">
            <div class="header-left">
                <button type="button" class="btn-create" onclick="openCreateModal()">
                    <i class="fas fa-plus"></i>
                    إنشاء إشعار جديد
                </button>
            </div>
        </div>
        <div class="table-section">
                <div class="table-header">
                    <h3 class="page-title">الإشعارات المرسلة</h3>
                    <div class="search-box table-search">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="ابحث عن اسم مستخدم أو بريد إلكتروني" class="js-refunds-search" value="{{ request('search') }}">
                    </div>
                    <div class="filter-area">
                        <button type="button" class="filter-button" onclick="toggleFilterMenu(event)">
                            <i class="fas fa-filter"></i>
                            فلتر
                        </button>
                        <div class="dropdown-menu dropdown-menu-end p-3" id="filterMenu" style="min-width: 200px;">
                            <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['target_users' => 'all']) }}">الكل</a>
                            <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['target_users' => 'landlords']) }}">المؤجرين</a>
                            <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['target_users' => 'tenants']) }}">المستأجرين</a>
                        </div>
                    </div>
                </div>
            <div class="table-section">
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                        <tr>
                            <th>نوع الإشعار</th>
                            <th>القناة</th>
                            <th>المستخدمين</th>
                            <th>نص الإشعار</th>
                            <th>تاريخ الإنشاء</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($notifications as $notification)
                            <tr>
                                <td>
                                    <span class="notification-type">{{ $notification->type_name }}</span>
                                </td>
                                <td>
                                    <span class="channel-badge channel-{{ $notification->channel }}">
                                        {{ $notification->channel_name }}
                                    </span>
                                </td>
                                <td>
                                    <span class="target-badge">
                                        {{ $notification->target_users_name }}
                                    </span>
                                </td>
                                <td>
                                    <div class="notification-content">
                                        <div class="notification-title">{{ $notification->title }}</div>
                                        <div class="notification-message">{{ Str::limit($notification->message, 80) }}</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="date-text">
                                        {{ $notification->sent_at ? $notification->sent_at->format('Y/n/j') : $notification->created_at->format('Y/n/j') }}
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn-action btn-view"
                                            data-notification="{{ json_encode([
                                            'id' => $notification->id,
                                            'type_name' => $notification->type_name,
                                            'channel_name' => $notification->channel_name,
                                            'target_users_name' => $notification->target_users_name,
                                            'date' => $notification->sent_at ? $notification->sent_at->format('Y/n/j') : $notification->created_at->format('Y/n/j'),
                                            'title' => $notification->title,
                                            'message' => $notification->message,
                                            'delete_url' => route('admin.notifications.destroy', $notification->id)
                                        ]) }}"
                                            onclick="openViewModal(JSON.parse(this.getAttribute('data-notification')))">
                                        معاينة
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-state">
                                    <i class="fas fa-bell-slash"></i>
                                    <p>لا توجد إشعارات حتى الآن</p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($notifications->hasPages())
                    <div class="pagination-section">
                        <div class="pagination-info">
                            عرض {{ $notifications->firstItem() }} إلى {{ $notifications->lastItem() }} من {{ $notifications->total() }}
                        </div>
                        <div class="pagination-links">
                            {{ $notifications->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>


        <style>
            /* Main Container */
            .notifications-page-container {
                padding: 20px;
                background: #f8f9fa;
                min-height: 100vh;
                direction: rtl;
            }

            /* Page Header */
            .page-header {
                display: flex;
                justify-content: flex-start;
                align-items: center;
                margin-bottom: 20px;
            }

            .header-left {
                margin-right: 0;
            }

            .btn-create {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 10px 20px;
                background: #3B82F6;
                color: white;
                border: none;
                border-radius: 8px;
                font-weight: 600;
                font-size: 14px;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .btn-create:hover {
                background: #2563EB;
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            }

            /* Table Section */
            .table-section {
                background: white;
                border-radius: 12px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
                overflow: hidden;
            }

            .table-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 20px 24px;
                border-bottom: 1px solid #e5e7eb;
                background: #fafbfc;
            }

            .page-title {
                font-size: 20px;
                font-weight: 700;
                color: #1F2937;
                margin: 0;
            }

            .filter-area {
                position: relative;
            }

            .filter-button {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 10px 16px;
                background: #6B7280;
                color: white;
                border: none;
                border-radius: 8px;
                font-weight: 600;
                font-size: 14px;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .filter-button:hover {
                background: #4B5563;
            }

            .dropdown-menu {
                position: absolute;
                top: 100%;
                right: 0;
                display: none;
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                box-shadow: 0 8px 24px rgba(0,0,0,0.08);
                z-index: 30;
            }

            .dropdown-menu.active {
                display: block;
            }

            .dropdown-item {
                display: block;
                padding: 8px 12px;
                color: #374151;
                text-decoration: none;
                border-radius: 6px;
            }

            .dropdown-item:hover {
                background: #F3F4F6;
            }

            .table-search {
                position: relative;
                width: 300px;
            }

            .table-search i {
                position: absolute;
                right: 12px;
                top: 50%;
                transform: translateY(-50%);
                color: #9CA3AF;
                font-size: 14px;
            }

            .table-search input {
                width: 100%;
                padding: 8px 35px 8px 12px;
                border: 1px solid #E5E7EB;
                border-radius: 8px;
                font-size: 14px;
                color: #1F2937;
                direction: rtl;
                transition: all 0.3s;
            }

            .table-search input:focus {
                outline: none;
                border-color: #3B82F6;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            }

            .table-container {
                overflow-x: auto;
            }

            .data-table {
                width: 100%;
                border-collapse: collapse;
                table-layout: auto;
            }

            .data-table thead {
                background: #f9fafb;
            }

            .data-table th {
                padding: 14px 16px;
                text-align: right;
                font-size: 13px;
                font-weight: 700;
                color: #374151;
                white-space: nowrap;
                border-bottom: 2px solid #e5e7eb;
            }

            .data-table td {
                padding: 16px;
                text-align: right;
                font-size: 14px;
                color: #4B5563;
                border-bottom: 1px solid #f3f4f6;
                vertical-align: middle;
            }

            .data-table tbody tr {
                transition: background-color 0.2s ease;
            }

            .data-table tbody tr:hover {
                background: #fafbfc;
            }

            /* Notification Type */
            .notification-type {
                display: inline-block;
                font-weight: 600;
                color: #1F2937;
                white-space: nowrap;
            }

            /* Channel Badge */
            .channel-badge {
                display: inline-flex;
                align-items: center;
                padding: 4px 12px;
                border-radius: 6px;
                font-size: 13px;
                font-weight: 600;
                white-space: nowrap;
            }

            .channel-sms {
                background: #dbeafe;
                color: #1e40af;
            }

            .channel-push {
                background: #fef3c7;
                color: #92400e;
            }

            .channel-app {
                background: #dcfce7;
                color: #166534;
            }

            .channel-email {
                background: #f3e8ff;
                color: #6b21a8;
            }

            /* Target Badge */
            .target-badge {
                display: inline-flex;
                align-items: center;
                padding: 4px 12px;
                background: #f3f4f6;
                color: #374151;
                border-radius: 6px;
                font-size: 13px;
                font-weight: 600;
                white-space: nowrap;
            }

            /* Notification Content */
            .notification-content {
                text-align: right;
                max-width: 350px;
            }

            .notification-title {
                font-weight: 600;
                color: #1F2937;
                margin-bottom: 4px;
                line-height: 1.4;
            }

            .notification-message {
                font-size: 13px;
                color: #6B7280;
                line-height: 1.5;
                word-break: break-word;
            }

            /* Date */
            .date-text {
                color: #6B7280;
                font-size: 13px;
                white-space: nowrap;
            }

            /* Action Button */
            .btn-action {
                display: inline-block;
                padding: 6px 16px;
                border-radius: 6px;
                font-size: 13px;
                font-weight: 600;
                text-decoration: none;
                transition: all 0.2s ease;
                border: none;
                cursor: pointer;
            }

            .btn-view {
                background: transparent;
                color: #3B82F6;
                text-decoration: none;
                padding: 6px 12px;
                border: 1px solid #3B82F6;
            }

            .btn-view:hover {
                background: #3B82F6;
                color: white;
                transform: translateY(-1px);
            }

            /* Empty State */
            .empty-state {
                padding: 60px 20px !important;
                text-align: center;
                color: #9CA3AF;
            }

            .empty-state i {
                font-size: 48px;
                margin-bottom: 16px;
                opacity: 0.4;
                display: block;
            }

            .empty-state p {
                font-size: 15px;
                margin: 0;
                color: #6B7280;
            }

            /* Pagination */
            .pagination-section {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 16px 24px;
                border-top: 1px solid #f3f4f6;
                background: #fafbfc;
            }

            .pagination-info {
                font-size: 14px;
                color: #6B7280;
            }

            .pagination-links {
                display: flex;
                gap: 4px;
            }

            .pagination .page-link {
                border-radius: 6px !important;
                border: 1px solid #E5E7EB;
                color: #374151;
                padding: 6px 12px;
                transition: all 0.2s ease;
            }

            .pagination .page-link:hover {
                background: #f9fafb;
                border-color: #3B82F6;
                color: #3B82F6;
            }

            .pagination .page-item.active .page-link {
                background: #3B82F6;
                border-color: #3B82F6;
                color: #fff;
            }

            .pagination .page-item.disabled .page-link {
                background: #f9fafb;
                border-color: #E5E7EB;
                color: #9CA3AF;
            }

            /* Modal Overlay */
            .modal-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.6);
                z-index: 9999;
                align-items: center;
                justify-content: center;
                backdrop-filter: blur(2px);
                overflow-y: auto;
                padding: 16px;
            }

            .modal-overlay.active {
                display: flex;
            }

            /* Modal Container */
            .modal-container {
                background: white;
                border-radius: 8px;
                width: 90%;
                max-width: 450px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                animation: modalSlideIn 0.3s ease;
            }

            @keyframes modalSlideIn {
                from {
                    opacity: 0;
                    transform: translateY(-20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Modal Header */
            .modal-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 12px 16px;
                border-bottom: 1px solid #E5E7EB;
                background: #fafbfc;
            }

            .modal-title {
                font-size: 16px;
                font-weight: 700;
                color: #1F2937;
                margin: 0;
            }

            .modal-close {
                background: none;
                border: none;
                font-size: 22px;
                color: #6B7280;
                cursor: pointer;
                padding: 0;
                width: 28px;
                height: 28px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 6px;
                transition: all 0.2s;
            }

            .modal-close:hover {
                background: #F3F4F6;
                color: #1F2937;
            }

            /* Modal Body */
            .modal-body {
                padding: 14px;
            }

            /* Form Group */
            .form-group {
                margin-bottom: 12px;
            }

            .form-group label {
                display: block;
                font-size: 14px;
                font-weight: 600;
                color: #374151;
                margin-bottom: 8px;
                text-align: right;
            }

            .form-control {
                width: 100%;
                padding: 10px 14px;
                border: 1px solid #E5E7EB;
                border-radius: 8px;
                font-size: 14px;
                color: #1F2937;
                direction: rtl;
                transition: all 0.2s;
                background: white;
            }

            .form-control:focus {
                outline: none;
                border-color: #3B82F6;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            }

            textarea.form-control {
                resize: vertical;
                min-height: 100px;
                font-family: inherit;
                line-height: 1.6;
            }

            /* Detail Group */
            .detail-group {
                margin-bottom: 12px;
            }

            .detail-group label {
                display: block;
                font-size: 13px;
                font-weight: 600;
                color: #6B7280;
                margin-bottom: 8px;
                text-align: right;
            }

            .detail-group p {
                font-size: 14px;
                color: #1F2937;
                margin: 0;
                padding: 10px 14px;
                background: #f9fafb;
                border: 1px solid #f3f4f6;
                border-radius: 8px;
                text-align: right;
                line-height: 1.6;
                word-break: break-word;
            }

            /* Modal Footer */
            .modal-footer {
                display: flex;
                justify-content: flex-end;
                gap: 12px;
                padding: 12px 16px;
                border-top: 1px solid #E5E7EB;
                background: #fafbfc;
            }

            .btn-cancel {
                padding: 10px 20px;
                background: white;
                border: 1px solid #E5E7EB;
                border-radius: 8px;
                color: #374151;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s;
            }

            .btn-cancel:hover {
                background: #F9FAFB;
                border-color: #D1D5DB;
            }

            .btn-submit {
                padding: 10px 20px;
                background: #3B82F6;
                border: none;
                border-radius: 8px;
                color: white;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s;
            }

            .btn-submit:hover {
                background: #2563EB;
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            }

            .btn-delete {
                padding: 10px 20px;
                background: #EF4444;
                border: none;
                border-radius: 8px;
                color: white;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s;
            }

            .btn-delete:hover {
                background: #DC2626;
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
            }

            /* Responsive */
            @media (max-width: 1024px) {
                .page-header {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 16px;
                }

                .header-left {
                    margin-right: 0;
                    width: 100%;
                }

                .btn-create {
                    width: 100%;
                    justify-content: center;
                }

                .table-header {
                    flex-direction: column;
                    gap: 16px;
                    align-items: stretch;
                }

                .table-search {
                    width: 100%;
                }

                .notification-content {
                    max-width: 250px;
                }
            }

            @media (max-width: 768px) {
                .notifications-page-container {
                    padding: 12px;
                }

                .table-header {
                    padding: 16px;
                }

                .page-title {
                    font-size: 18px;
                }

                .data-table th,
                .data-table td {
                    padding: 12px 8px;
                    font-size: 12px;
                }

                .notification-content {
                    max-width: 180px;
                }

                .btn-view {
                    padding: 4px 10px;
                    font-size: 12px;
                }

                .modal-container {
                    width: 95%;
                    max-height: 95vh;
                }

                .modal-header,
                .modal-body,
                .modal-footer {
                    padding: 16px;
                }

                .modal-title {
                    font-size: 16px;
                }
            }

            @media (max-width: 480px) {
                .table-container {
                    overflow-x: scroll;
                }

                .data-table {
                    min-width: 800px;
                }

                .channel-badge,
                .target-badge {
                    font-size: 11px;
                    padding: 3px 8px;
                }
            }
        </style>

        <!-- Create Notification Modal -->
        <div id="createModal" class="modal-overlay" onclick="closeCreateModal(event)">
            <div class="modal-container" onclick="event.stopPropagation()">
                <div class="modal-header">
                    <h2 class="modal-title">إنشاء إشعار جديد</h2>
                    <button type="button" class="modal-close" onclick="closeCreateModal()">&times;</button>
                </div>
                <form method="POST" action="{{ route('admin.notifications.store') }}" class="modal-form">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="type">نوع الإشعار</label>
                            <select name="type" id="type" class="form-control" required>
                                <option value="">اختر نوع الإشعار</option>
                                <option value="alert">تنبيه</option>
                                <option value="booking_confirm">تأكيد حجز</option>
                                <option value="booking_completed">اكتمال حجز</option>
                                <option value="booking_new_request">طلب حجز جديد</option>
                                <option value="booking_cancelled">إلغاء حجز</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="channel">القناة</label>
                            <select name="channel" id="channel" class="form-control" required>
                                <option value="">اختر القناة</option>
                                <option value="sms">SMS</option>
                                <option value="email">Email</option>
                                <option value="app">داخل التطبيق</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="target_users">المستخدمين</label>
                            <select name="target_users" id="target_users" class="form-control" required>
                                <option value="">اختر المستخدمين</option>
                                <option value="all">الكل</option>
                                <option value="tenants">المستأجرين</option>
                                <option value="landlords">المؤجرين</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="title">عنوان الإشعار</label>
                            <input type="text" name="title" id="title" class="form-control" required placeholder="أدخل عنوان الإشعار">
                        </div>

                        <div class="form-group">
                            <label for="message">نص الإشعار</label>
                            <textarea name="message" id="message" class="form-control" rows="4" required placeholder="أدخل نص الإشعار"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn-cancel" onclick="closeCreateModal()">إلغاء</button>
                        <button type="submit" class="btn-submit">إرسال الإشعار</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- View Notification Modal -->
        <div id="viewModal" class="modal-overlay" onclick="closeViewModal(event)">
            <div class="modal-container" onclick="event.stopPropagation()">
                <div class="modal-header">
                    <h2 class="modal-title">تفاصيل الإشعار</h2>
                    <button type="button" class="modal-close" onclick="closeViewModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="detail-group">
                        <label>نوع الإشعار</label>
                        <p id="viewType"></p>
                    </div>

                    <div class="detail-group">
                        <label>القناة</label>
                        <p id="viewChannel"></p>
                    </div>

                    <div class="detail-group">
                        <label>المستخدمين</label>
                        <p id="viewTargetUsers"></p>
                    </div>

                    <div class="detail-group">
                        <label>تاريخ الإنشاء</label>
                        <p id="viewDate"></p>
                    </div>

                    <div class="detail-group">
                        <label>عنوان الإشعار</label>
                        <p id="viewTitle"></p>
                    </div>

                    <div class="detail-group">
                        <label>نص الإشعار</label>
                        <p id="viewMessage"></p>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeViewModal()">إغلاق</button>
                    <form method="POST" action="" id="deleteForm" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete" onclick="return confirm('هل أنت متأكد من حذف هذا الإشعار؟')">حذف</button>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function clearFilters() {
                window.location.href = "{{ route('admin.notifications.index') }}";
            }

            function openCreateModal() {
                document.getElementById('createModal').classList.add('active');
            }

            function closeCreateModal(event) {
                if (!event || event.target === event.currentTarget) {
                    document.getElementById('createModal').classList.remove('active');
                }
            }

            function openViewModal(notification) {
                document.getElementById('viewType').textContent = notification.type_name;
                document.getElementById('viewChannel').textContent = notification.channel_name;
                document.getElementById('viewTargetUsers').textContent = notification.target_users_name;
                document.getElementById('viewDate').textContent = notification.date;
                document.getElementById('viewTitle').textContent = notification.title;
                document.getElementById('viewMessage').textContent = notification.message;
                document.getElementById('deleteForm').action = notification.delete_url;
                document.getElementById('viewModal').classList.add('active');
            }

            function closeViewModal(event) {
                if (!event || event.target === event.currentTarget) {
                    document.getElementById('viewModal').classList.remove('active');
                }
            }

            // Close modals on ESC key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeCreateModal();
                    closeViewModal();
                }
            });

            (function() {
                var input = document.querySelector('.js-refunds-search');
                if (input) {
                    input.addEventListener('keydown', function(event) {
                        if (event.key === 'Enter') {
                            var q = input.value.trim();
                            var url = new URL(window.location.href);
                            if (q.length) {
                                url.searchParams.set('search', q);
                            } else {
                                url.searchParams.delete('search');
                            }
                            window.location.href = url.toString();
                        }
                    });
                }
            })();

            function toggleFilterMenu(e) {
                e.stopPropagation();
                var menu = document.getElementById('filterMenu');
                if (menu) {
                    menu.classList.toggle('active');
                }
            }

            document.addEventListener('click', function() {
                var menu = document.getElementById('filterMenu');
                if (menu) {
                    menu.classList.remove('active');
                }
            });
        </script>
@endsection
