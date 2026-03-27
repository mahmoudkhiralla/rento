@extends('dashboard.layouts.app')

@section('title', 'الشكاوى والدعم')

@section('content')
    <div class="complaints-page-container">
        <!-- Statistics Cards -->
        <div class="statistics-row">
            <div class="stat-card-item">
                <div class="stat-card-content">
                    <div class="stat-title">إجمالي الشكاوى</div>
                    <div class="stat-number">{{ number_format($totalComplaints ?? 71897) }}</div>
                </div>
                <a href="{{ route('dashboard.support.tickets', ['status' => 'all']) }}" class="stat-view-btn" style="display:block;text-align:center;">عرض الكل</a>
            </div>

            <div class="stat-card-item">
                <div class="stat-card-content">
                    <div class="stat-title">الشكاوى المفتوحة</div>
                    <div class="stat-number">{{ number_format($openComplaints ?? 71897) }}</div>
                </div>
                <a href="{{ route('dashboard.support.tickets', ['status' => 'open']) }}" class="stat-view-btn" style="display:block;text-align:center;">عرض الكل</a>
            </div>

            <div class="stat-card-item">
                <div class="stat-card-content">
                    <div class="stat-title">الشكاوى المغلقة</div>
                    <div class="stat-number">{{ number_format($closedComplaints ?? 71897) }}</div>
                </div>
                <a href="{{ route('dashboard.support.tickets', ['status' => 'closed']) }}" class="stat-view-btn" style="display:block;text-align:center;">عرض الكل</a>
            </div>
        </div>

        <!-- Main Content Row -->
        <div class="content-main-row {{ !$complaint ? 'details-hidden' : '' }}">
            <!-- Right Side - Complaints List -->
            <div class="list-section">
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">قائمة الشكاوى</h2>
                        <div class="filter-wrapper">
                            <button class="btn-filter" id="btnFilter">
                                <i class="fas fa-filter"></i>
                                تصفية
                            </button>
                            <div class="filter-menu" id="filterMenu">
                                <a href="{{ route('dashboard.support.tickets', ['status' => 'all']) }}" class="filter-item">الكل</a>
                                <a href="{{ route('dashboard.support.tickets', ['status' => 'open']) }}" class="filter-item">المفتوحة</a>
                                <a href="{{ route('dashboard.support.tickets', ['status' => 'closed']) }}" class="filter-item">المغلقة</a>
                            </div>
                        </div>
                    </div>

                    <div class="complaints-list">
                        @forelse($complaintsList ?? [] as $item)
                            <a href="{{ route('dashboard.support.tickets.show', $item['id']) }}" class="complaint-item {{ isset($item['active']) && $item['active'] ? 'complaint-active' : '' }}">
                                <div class="complaint-left">
                                    <div class="status-indicator status-{{ $item['status'] }}"></div>
                                    <div class="complaint-avatar">
                                        @if(!empty($item['avatar_url']))
                                            <img src="{{ $item['avatar_url'] }}" alt="avatar">
                                        @elseif(!empty($item['avatar']))
                                            <span class="avatar-text">{{ $item['avatar'] }}</span>
                                        @else
                                            <img src="https://via.placeholder.com/40" alt="avatar">
                                        @endif
                                    </div>
                                    <div class="complaint-info">
                                        <div class="complaint-name">{{ $item['name'] }}</div>
                                        <div class="complaint-desc">{{ $item['desc'] }}</div>
                                    </div>
                                </div>
                                <div class="complaint-time">
                                    <span class="read-indicator {{ !empty($item['unread']) ? 'unread' : 'read' }}"></span>
                                    {{ $item['time'] }}
                                </div>
                            </a>
                        @empty
                            <div class="empty-state">
                                <p>لا توجد تذاكر دعم حتى الآن</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Left Side - Complaint Details -->
            <div class="details-section">
                @if($complaint)
                    <div class="section-card">
                        <div class="section-header">
                            <div class="header-user">
                                <div class="header-avatar">
                                    @if(!empty($complaint->user?->avatar))
                                        <img src="{{ $complaint->user->avatar }}" alt="avatar">
                                    @else
                                        <img src="https://via.placeholder.com/40" alt="avatar">
                                    @endif
                                </div>
                                <div class="header-name">{{ $complaint->user->name ?? 'مستخدم غير معروف' }}</div>
                            </div>
                            <div class="header-time">{{ $complaint->created_at->locale('ar')->diffForHumans() }}</div>
                        </div>

                        <div class="details-form">
                            <div class="form-row">
                                <h2 class="section-title">تفاصيل التذكرة #{{ $complaint->id }}</h2>
                                <button type="button" class="btn-filter" id="btnHideDetails">
                                    <i class="fas fa-eye-slash"></i>
                                    إخفاء التفاصيل
                                </button>
                            </div>
                            <div class="form-row-4">
                                <div class="form-field">
                                    <label class="field-label">اسم المؤجر</label>
                                    <input type="text" class="field-input" value="{{ $complaintContext['landlord_name'] ?? 'غير معروف' }}" readonly>
                                </div>
                                <div class="form-field">
                                    <label class="field-label">اسم المستأجر</label>
                                    <input type="text" class="field-input" value="{{ $complaintContext['tenant_name'] ?? 'غير معروف' }}" readonly>
                                </div>
                                <div class="form-field">
                                    <label class="field-label">اسم العقار</label>
                                    <input type="text" class="field-input" value="{{ $complaintContext['property_title'] ?? 'غير معروف' }}" readonly>
                                </div>
                                <div class="form-field">
                                    <label class="field-label">نوع الشكوى</label>
                                    <span class="complaint-badge type-badge">{{ $complaintContext['type'] ?? 'غير محدد' }}</span>
                                </div>
                            </div>

                            <div class="form-row-2">
                                <div class="form-field">
                                    <label class="field-label">مقدم الشكوي</label>
                                    <input type="text" class="field-input" value="{{ $complaintContext['submitter_role_name'] ?? 'غير متوفر' }}" readonly>
                                </div>
                                <div class="form-field">
                                    <label class="field-label">عنوان الشكوي</label>
                                    <input type="text" class="field-input" value="{{ $complaintContext['title'] ?? $complaint->subject }}" readonly>
                                </div>
                            </div>

                            <div class="form-field-full">
                                <label class="field-label">نص الشكوى</label>
                                <textarea class="field-textarea" readonly>{{ $complaint->description }}</textarea>
                            </div>

                            @if($complaint->replies && $complaint->replies->count() > 0)
                                <div class="form-field-full">
                                    <label class="field-label">الردود السابقة</label>
                                    <div class="replies-list">
                                        @foreach($complaint->replies as $reply)
                                            <div class="reply-item {{ $reply->is_admin_reply ? 'admin-reply' : 'user-reply' }}">
                                                <div class="reply-header">
                                                    <span class="reply-author">{{ $reply->is_admin_reply ? ($reply->admin->name ?? 'مشرف النظام') : ($reply->user->name ?? 'غير معروف') }}</span>
                                                    <span class="reply-time">{{ $reply->created_at->locale('ar')->diffForHumans() }}</span>
                                                </div>
                                                <div class="reply-message">{{ $reply->message }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('dashboard.support.tickets.reply', $complaint->id) }}">
                                @csrf
                                <div class="form-field-full">
                                    <label class="field-label">رسالة جديدة...</label>
                                    <textarea name="message" class="field-textarea-new" placeholder="نص رسالة جديدة..." required></textarea>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn-send-reply">إرسال الرد</button>
                                    <button type="submit" class="btn-close-complaint" form="close-ticket-{{ $complaint->id }}" {{ $complaint->status === 'closed' ? 'disabled' : '' }}>إغلاق الشكوي</button>
                                </div>
                            </form>
                            <form id="close-ticket-{{ $complaint->id }}" method="POST" action="{{ route('dashboard.support.tickets.close', $complaint->id) }}" style="display:none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                @else
                    <div class="section-card">
                        <div class="empty-state-details">
                            <i class="fas fa-ticket-alt"></i>
                            <p>اختر تذكرة لعرض تفاصيلها</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        /* Main Container */
        .complaints-page-container {
            padding: 24px;
            background: #F3F4F6;
            min-height: 100vh;
        }

        /* Statistics Row */
        .statistics-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }

        .stat-card-item {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .stat-card-content {
            padding: 24px;
            text-align: center;
        }

        .stat-title {
            font-size: 14px;
            color: #6B7280;
            margin-bottom: 8px;
        }

        .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: #1F2937;
        }

        .stat-view-btn {
            width: 100%;
            padding: 12px;
            background: #3B82F6;
            color: white;
            border: none;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .stat-view-btn:hover {
            background: #2563EB;
        }

        /* Main Content Row */
        .content-main-row {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 20px;
        }
        .content-main-row.details-hidden { grid-template-columns: 1fr; }
        .content-main-row.details-hidden .details-section { display: none; }
        .content-main-row.details-hidden .list-section { grid-column: 1 / -1; width: calc(70%); margin: 0 auto; }

        /* Section Card */
        .section-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            border-bottom: 1px solid #E5E7EB;
            background: #EFF6FF;
        }
        .header-user { display: flex; align-items: center; gap: 10px; }
        .header-avatar { width: 40px; height: 40px; border-radius: 50%; overflow: hidden; background: #E5E7EB; display: flex; align-items: center; justify-content: center; }
        .header-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .header-name { font-size: 14px; font-weight: 700; color: #1F2937; }
        .header-time { font-size: 12px; color: #6B7280; }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #1F2937;
            margin: 0;
        }

        .btn-filter {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: white;
            border: 1px solid #3B82F6;
            border-radius: 8px;
            color: #3B82F6;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-filter:hover {
            background: #EFF6FF;
        }

        .filter-wrapper { position: relative; }
        .filter-menu {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: #FFFFFF;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            min-width: 160px;
            display: none;
            overflow: hidden;
            z-index: 20;
        }
        .filter-menu.show { display: block; }
        .filter-item {
            display: block;
            padding: 10px 14px;
            color: #1F2937;
            text-decoration: none;
            font-size: 13px;
        }
        .filter-item:hover { background: #F9FAFB; }

        /* Details Form */
        .details-form {
            padding: 24px;
        }
        .form-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }

        .form-row-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 16px;
        }

        .form-row-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-bottom: 16px;
        }

        .form-field,
        .form-field-full {
            display: flex;
            flex-direction: column;
        }

        .form-field-full {
            margin-bottom: 16px;
        }

        .field-label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            text-align: right;
        }

        .field-input {
            padding: 10px 12px;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            font-size: 14px;
            color: #1F2937;
            background: #F9FAFB;
        }

        .field-input:focus {
            outline: none;
            border-color: #3B82F6;
        }

        .badge-wrapper {
            padding: 10px 0;
        }

        .complaint-badge {
            display: inline-block;
            padding: 6px 14px;
            background: #F3E8FF;
            color: #7C3AED;
            border-radius: 15px;
            font-size: 13px;
            font-weight: 500;
            white-space: nowrap;
        }
        .type-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            line-height: 1.2;
            display: inline-flex;
            align-items: center;
        }

        .field-textarea,
        .field-textarea-new {
            padding: 12px;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            font-size: 14px;
            color: #4B5563;
            line-height: 1.6;
            resize: vertical;
            min-height: 120px;
            background: #F9FAFB;
        }

        .field-textarea-new {
            background: white;
        }

        .field-textarea-new:focus {
            outline: none;
            border-color: #3B82F6;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .btn-close-complaint {
            padding: 12px 32px;
            background: #3B82F6;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-close-complaint:hover {
            background: #2563EB;
        }

        .btn-close-complaint:disabled {
            background: #9CA3AF;
            cursor: not-allowed;
            opacity: 0.75;
        }

        /* Complaints List */
        .complaints-list {
            max-height: 600px;
            overflow-y: auto;
        }

        .complaint-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 24px;
            border-bottom: 1px solid #F3F4F6;
            transition: all 0.3s;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        .complaint-item:hover {
            background: #F9FAFB;
        }

        .complaint-active {
            background: #EFF6FF !important;
            border-right: 3px solid #3B82F6;
        }

        .empty-state {
            padding: 40px 24px;
            text-align: center;
            color: #9CA3AF;
        }

        .empty-state-details {
            padding: 100px 40px;
            text-align: center;
            color: #9CA3AF;
        }

        .empty-state-details i {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .replies-list {
            margin-top: 12px;
            max-height: 300px;
            overflow-y: auto;
        }

        .reply-item {
            padding: 12px;
            margin-bottom: 12px;
            border-radius: 8px;
            background: #F9FAFB;
        }

        .admin-reply {
            background: #EFF6FF;
            border-left: 3px solid #3B82F6;
        }

        .user-reply {
            background: #F3F4F6;
            border-left: 3px solid #9CA3AF;
        }

        .reply-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .reply-author {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
        }

        .reply-time {
            font-size: 12px;
            color: #9CA3AF;
        }

        .reply-message {
            font-size: 14px;
            color: #4B5563;
            line-height: 1.6;
        }

        .btn-send-reply {
            padding: 12px 32px;
            background: #10B981;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            margin-left: 12px;
        }

        .btn-send-reply:hover {
            background: #059669;
        }

        .priority-low {
            background: #D1FAE5;
            color: #065F46;
        }

        .priority-medium {
            background: #FEF3C7;
            color: #92400E;
        }

        .priority-high {
            background: #FED7AA;
            color: #9A3412;
        }

        .priority-urgent {
            background: #FEE2E2;
            color: #991B1B;
        }

        .status-open {
            background: #DBEAFE;
            color: #1E40AF;
        }

        .status-in_progress {
            background: #FEF3C7;
            color: #92400E;
        }

        .status-resolved {
            background: #D1FAE5;
            color: #065F46;
        }

        .status-closed {
            background: #F3F4F6;
            color: #6B7280;
        }

        .complaint-left {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }

        .status-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .status-active {
            background: #22C55E;
        }

        .status-pending {
            background: #F59E0B;
        }

        .status-warning {
            background: #EF4444;
        }

        .status-inactive {
            background: #9CA3AF;
        }
        .status-indicator.status-open { background: #22C55E; }
        .status-indicator.status-closed { background: #EF4444; }
        .status-indicator.status-replied { background: #3B82F6; }

        .complaint-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
            background: #E5E7EB;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .complaint-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-text {
            font-size: 14px;
            font-weight: 600;
            color: #6B7280;
        }

        .complaint-info {
            flex: 1;
        }

        .complaint-name {
            font-size: 14px;
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 4px;
        }

        .complaint-desc {
            font-size: 12px;
            color: #6B7280;
        }

        .complaint-time {
            font-size: 12px;
            color: #9CA3AF;
            white-space: nowrap;
        }

        /* Scrollbar Styling */
        .complaints-list::-webkit-scrollbar {
            width: 6px;
        }

        .complaints-list::-webkit-scrollbar-track {
            background: #F3F4F6;
        }

        .complaints-list::-webkit-scrollbar-thumb {
            background: #D1D5DB;
            border-radius: 10px;
        }

        .complaints-list::-webkit-scrollbar-thumb:hover {
            background: #9CA3AF;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .content-main-row {
                grid-template-columns: 1fr;
            }

            .form-row-4 {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .complaints-page-container {
                padding: 16px;
            }

            .statistics-row {
                grid-template-columns: 1fr;
            }

            .form-row-4,
            .form-row-2 {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <script>
        (function () {
            var el = document.querySelector('.type-badge');
            if (!el) return;
            var len = (el.textContent || '').trim().length;
            var fs = 13, padY = 6, padX = 14;
            if (len >= 28) { fs = 10; padY = 4; padX = 10; }
            else if (len >= 20) { fs = 11; padY = 5; padX = 12; }
            else if (len >= 14) { fs = 12; padY = 6; padX = 12; }
            el.style.fontSize = fs + 'px';
            el.style.padding = padY + 'px ' + padX + 'px';
        })();

        (function () {
            var btn = document.getElementById('btnFilter');
            var menu = document.getElementById('filterMenu');
            if (!btn || !menu) return;
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                menu.classList.toggle('show');
            });
            document.addEventListener('click', function (e) {
                if (!menu.contains(e.target) && !btn.contains(e.target)) {
                    menu.classList.remove('show');
                }
            });
        })();

        (function () {
            var hideBtn = document.getElementById('btnHideDetails');
            var row = document.querySelector('.content-main-row');
            if (!hideBtn || !row) return;
            hideBtn.addEventListener('click', function () {
                row.classList.add('details-hidden');
            });
        })();
    </script>
@endsection
