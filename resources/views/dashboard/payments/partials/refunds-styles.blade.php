<style>
    /* Main Container */
    .refunds-container {
        padding: 16px;
        background: #F3F4F6;
        min-height: 100vh;
        direction: rtl;
    }

    /* Page Header */
    .page-header {
        margin-bottom: 24px;
    }

    .page-title {
        font-size: 24px;
        font-weight: 700;
        color: #1F2937;
        margin: 0;
    }

    /* Statistics Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 16px;
    }

    .stat-card {
        background: white;
        padding: 16px;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .stat-title {
        font-size: 13px;
        color: #6B7280;
        margin: 0 0 12px 0;
        font-weight: 500;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: #1F2937;
        margin: 0;
    }

    /* Table Section */
    .table-section {
        background: white;
        padding: 16px;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 16px;
    }

    .table-wrapper {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #E5E7EB;
    }

    .refunds-table {
        width: 100%;
        border-collapse: collapse;
        direction: rtl;
    }

    .refunds-table thead th {
        background: #EEF2F7;
        color: #374151;
        font-size: 13px;
        font-weight: 600;
        padding: 12px 16px;
        border-bottom: 1px solid #E5E7EB;
        text-align: right;
    }

    .refunds-table tbody td {
        padding: 12px 16px;
        border-bottom: 1px solid #F3F4F6;
        font-size: 13px;
        color: #1F2937;
        vertical-align: middle;
    }

    .refunds-table .col-actions { text-align: left; }
    .action-cell { display: flex; align-items: center; gap: 6px; }
    .action-cell .icon-btn { margin-left: 0; }
    .preview-cell { text-align: left; }

    .icon-btn {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: none;
        background: #fff;
        box-shadow: 0 1px 2px rgba(0,0,0,0.06);
        margin-left: 6px;
        cursor: pointer;
    }

    .icon-btn i { font-size: 14px; }
    .icon-btn.reject i { color: #EF4444; }
    .icon-btn.approve i { color: #22C55E; }
    .icon-btn.view i { color: #3B82F6; }

    .user-cell { display: flex; align-items: center; gap: 10px; }
    .user-cell .avatar { width: 36px; height: 36px; border-radius: 50%; }
    .user-texts { display: flex; flex-direction: column; }
    .user-texts .name { font-weight: 600; font-size: 13px; }
    .user-texts .email { font-size: 12px; color: #6B7280; }

    .preview-link {
        color: #2563EB;
        text-decoration: none;
        font-weight: 600;
    }
    .preview-link:hover { text-decoration: underline; }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        gap: 16px;
    }

    .section-title {
        font-size: 16px;
        font-weight: 700;
        color: #1F2937;
        margin: 0;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* Filter Dropdown */
    .filter-dropdown {
        position: relative;
    }

    .filter-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: 6px;
        color: #374151;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .filter-btn:hover {
        background: #F9FAFB;
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        margin-top: 4px;
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        min-width: 160px;
        z-index: 100;
    }

    .dropdown-menu.show {
        display: block;
    }

    .dropdown-item {
        display: block;
        padding: 10px 16px;
        color: #374151;
        text-decoration: none;
        font-size: 13px;
        transition: all 0.3s;
    }

    .dropdown-item:hover {
        background: #F9FAFB;
    }

    /* Search Box */
    .search-box {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: 6px;
        min-width: 300px;
    }

    .search-box i {
        color: #9CA3AF;
    }

    .search-box input {
        flex: 1;
        border: none;
        outline: none;
        font-size: 13px;
        color: #374151;
    }

    .table-search { min-width: 380px; }

    @media (max-width: 768px) {
        .table-search { min-width: 100%; }
        .table-header { flex-direction: column; align-items: stretch; }
    }

    .search-box input::placeholder {
        color: #9CA3AF;
    }

    /* Refunds Grid */
    .refunds-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 16px;
    }

    .refund-card {
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        padding: 16px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .refund-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .refund-card .card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
        padding-bottom: 16px;
        border-bottom: 1px solid #F3F4F6;
    }

    .refund-card .user-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .refund-card .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }

    .refund-card .user-details {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .refund-card .user-name {
        font-size: 14px;
        font-weight: 600;
        color: #1F2937;
    }

    .refund-card .user-email {
        font-size: 12px;
        color: #6B7280;
    }

    /* Status Badge */
    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }

    .status-pending {
        background: #FEF3C7;
        color: #D97706;
    }

    .status-approved {
        background: #D1FAE5;
        color: #059669;
    }

    .status-rejected {
        background: #FEE2E2;
        color: #DC2626;
    }

    /* Card Body */
    .refund-card .card-body {
        margin-bottom: 12px;
    }

    .refund-card .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        border-bottom: 1px solid #F9FAFB;
    }

    .refund-card .info-row:last-child {
        border-bottom: none;
    }

    .refund-card .info-label {
        font-size: 12px;
        color: #6B7280;
    }

    .refund-card .info-value {
        font-size: 13px;
        font-weight: 600;
        color: #1F2937;
    }

    .refund-card .info-value.amount {
        color: #059669;
        font-size: 15px;
    }

    /* Card Footer */
    .refund-card .card-footer {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #F3F4F6;
    }

    .refund-card .view-btn {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px;
        background: #EFF6FF;
        color: #3B82F6;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .refund-card .view-btn:hover {
        background: #DBEAFE;
    }

    /* Pagination */
    .pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #E5E7EB;
    }

    .pagination-info {
        font-size: 13px;
        color: #6B7280;
    }

    .pagination {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .page-btn {
        padding: 6px 12px;
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: 6px;
        color: #374151;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .page-btn:hover {
        background: #F9FAFB;
    }

    .page-btn.active {
        background: #3B82F6;
        color: white;
        border-color: #3B82F6;
    }

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1000;
    }

    .modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        position: relative;
        background: white;
        border-radius: 12px;
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        z-index: 1001;
    }

    .modal-refund {
        max-width: 520px;
        max-height: 75vh;
        overflow-y: hidden;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 16px;
        border-bottom: 1px solid #E5E7EB;
    }

    .modal-title {
        font-size: 16px;
        font-weight: 700;
        color: #1F2937;
        margin: 0;
    }

    .close-btn {
        background: none;
        border: none;
        color: #6B7280;
        cursor: pointer;
        font-size: 20px;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
    }

    .close-btn:hover {
        color: #1F2937;
    }

    /* Modal Tabs */
    .modal-tabs {
        display: flex;
        border-bottom: 1px solid #E5E7EB;
        overflow-x: auto;
    }

    .tab-btn {
        flex: 1;
        padding: 10px 12px;
        background: none;
        border: none;
        color: #6B7280;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        transition: all 0.3s;
        white-space: nowrap;
    }

    .tab-btn:hover {
        color: #3B82F6;
    }

    .tab-btn.active {
        color: #3B82F6;
        border-bottom-color: #3B82F6;
    }

    .modal-body {
        padding: 12px;
    }

    /* Tab Content */
    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .detail-item {
        margin-bottom: 12px;
    }

    .detail-item label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #6B7280;
        margin-bottom: 6px;
    }

    .detail-value {
        font-size: 13px;
        font-weight: 600;
        color: #1F2937;
    }

    .detail-value.amount {
        color: #059669;
        font-size: 14px;
    }

    /* User Info Detail */
    .user-info-detail {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        background: #F9FAFB;
        border-radius: 8px;
    }

    .user-info-detail .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }

    .user-text {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .user-text .user-name {
        font-size: 13px;
        font-weight: 600;
        color: #1F2937;
    }

    .user-text .user-email {
        font-size: 11px;
        color: #6B7280;
    }

    /* Account Type */
    .account-type {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px;
        background: #F9FAFB;
        border-radius: 8px;
    }

    .account-type input[type="radio"] {
        width: 18px;
        height: 18px;
    }

    .radio-label {
        font-size: 14px;
        color: #374151;
        margin: 0;
    }

    /* Account Details Section */
    .account-details-section {
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px solid #E5E7EB;
    }

    .section-subtitle {
        font-size: 15px;
        font-weight: 700;
        color: #1F2937;
        margin: 0 0 16px 0;
    }

    /* Approver Badge */
    .approver-badge {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px;
        background: #EFF6FF;
        border-radius: 8px;
    }

    .approver-badge i {
        color: #3B82F6;
        font-size: 18px;
    }

    .approver-badge span {
        font-size: 13px;
        color: #3B82F6;
        font-weight: 600;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding: 12px 16px;
        border-top: 1px solid #E5E7EB;
    }

    .btn-reject {
        padding: 8px 16px;
        background: white;
        color: #DC2626;
        border: 1px solid #DC2626;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-reject:hover {
        background: #FEE2E2;
    }

    .btn-approve {
        padding: 8px 16px;
        background: #3B82F6;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-approve:hover {
        background: #2563EB;
    }

    /* Modal details grid */
    .details-grid { border: 1px solid #E5E7EB; border-radius: 10px; overflow: hidden; }
    .details-row { display: grid; grid-template-columns: repeat(4, 1fr); }
    .details-row.details-header { background: #E8EEF7; }
    .details-row .details-cell { padding: 12px 14px; border-bottom: 1px solid #E5E7EB; font-size: 13px; color: #1F2937; }
    .details-row:last-child .details-cell { border-bottom: none; }

    /* Responsive */
    @media (max-width: 1400px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .refunds-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 1024px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .refunds-grid {
            grid-template-columns: 1fr;
        }

        .table-header {
            flex-direction: column;
            align-items: stretch;
        }

        .header-actions {
            flex-direction: column;
        }

        .search-box {
            min-width: 100%;
        }
    }

    @media (max-width: 768px) {
        .refunds-container {
            padding: 16px;
        }

        .modal-tabs {
            overflow-x: scroll;
        }

        .tab-btn {
            min-width: 120px;
        }
    }
</style>
