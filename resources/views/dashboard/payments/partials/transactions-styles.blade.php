<style>
    /* Main Container */
    .transactions-container {
        padding: 24px;
        background: #F3F4F6;
        min-height: 100vh;
        direction: rtl;
    }

    /* Page Header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        gap: 20px;
    }

    .header-left {
        flex: 1;
    }

    .user-profile {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .user-profile .user-avatar {
        width: 56px;
        height: 56px;
        border-radius: 50%;
    }

    .user-profile .user-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .user-profile .user-name {
        font-size: 20px;
        font-weight: 700;
        color: #1F2937;
        margin: 0;
    }

    .user-profile .user-role {
        font-size: 13px;
        color: #6B7280;
    }

    /* Search Box in Header */
    .page-header .search-box {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        min-width: 400px;
    }

    .page-header .search-box i {
        color: #9CA3AF;
        font-size: 16px;
    }

    .page-header .search-box input {
        flex: 1;
        border: none;
        outline: none;
        font-size: 13px;
        color: #374151;
    }

    .page-header .search-box input::placeholder {
        color: #9CA3AF;
    }

    /* Statistics Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: white;
        padding: 24px;
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
        padding: 24px;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
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

    .filter-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: 6px;
        color: #374151;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .filter-btn:hover {
        background: #F9FAFB;
    }

    /* Filter Dropdown */
    .filter-dropdown { position: relative; }
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
        min-width: 200px;
        z-index: 1002;
    }
    .dropdown-menu.show { display: block; }
    .dropdown-item {
        display: block;
        padding: 10px 16px;
        color: #374151;
        text-decoration: none;
        font-size: 13px;
        transition: all 0.3s;
    }
    .dropdown-item:hover { background: #F9FAFB; }

    /* Table */
    .table-wrapper {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table thead {
        background: #F9FAFB;
        border-bottom: 2px solid #E5E7EB;
    }

    .data-table th {
        padding: 12px 16px;
        text-align: right;
        font-size: 13px;
        font-weight: 700;
        color: #374151;
        white-space: nowrap;
    }

    .data-table td {
        padding: 16px;
        text-align: right;
        font-size: 13px;
        color: #4B5563;
        border-bottom: 1px solid #F3F4F6;
    }

    .data-table tbody tr:hover {
        background: #F9FAFB;
    }

    /* User Cell */
    .user-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar-sm {
        width: 36px;
        height: 36px;
        border-radius: 50%;
    }

    /* Amount Cell */
    .amount-cell {
        font-weight: 700;
        color: #059669;
        font-size: 14px;
    }

    /* Status Badge */
    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-pending {
        background: #FEF3C7;
        color: #D97706;
    }

    .status-completed {
        background: #D1FAE5;
        color: #059669;
    }

    .status-failed {
        background: #FEE2E2;
        color: #DC2626;
    }

    .status-cancelled {
        background: #F3F4F6;
        color: #6B7280;
    }

    /* Details Link */
    .details-link {
        background: none;
        border: none;
        color: #3B82F6;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: underline;
        transition: all 0.3s;
    }

    .details-link:hover {
        color: #2563EB;
    }

    /* Pagination */
    .pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 24px;
        padding-top: 24px;
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

    .modal-transaction {
        max-width: 600px;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 1px solid #E5E7EB;
    }

    .modal-title {
        font-size: 18px;
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
        padding: 16px 20px;
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
        padding: 24px;
    }

    /* Tab Content */
    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .detail-item {
        margin-bottom: 20px;
    }

    .detail-item label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #6B7280;
        margin-bottom: 8px;
    }

    .detail-value {
        font-size: 14px;
        font-weight: 600;
        color: #1F2937;
    }

    .detail-value.amount {
        color: #059669;
        font-size: 16px;
    }

    /* User Info Detail */
    .user-info-detail {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: #F9FAFB;
        border-radius: 8px;
    }

    .user-info-detail .user-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
    }

    .user-text {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .user-text .user-name {
        font-size: 14px;
        font-weight: 600;
        color: #1F2937;
    }

    .user-text .user-email {
        font-size: 12px;
        color: #6B7280;
    }

    /* Account Type */
    .account-type {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px;
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

    /* Transaction Details Section */
    .transaction-details-section {
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px solid #E5E7EB;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding: 20px 24px;
        border-top: 1px solid #E5E7EB;
    }

    .btn-cancel {
        padding: 10px 24px;
        background: white;
        color: #DC2626;
        border: 1px solid #DC2626;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-cancel:hover {
        background: #FEE2E2;
    }

    .btn-confirm {
        padding: 10px 24px;
        background: #3B82F6;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-confirm:hover {
        background: #2563EB;
    }

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
    }

    @media (max-width: 1024px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .page-header {
            flex-direction: column;
            align-items: stretch;
        }

        .page-header .search-box {
            min-width: 100%;
        }
    }

    @media (max-width: 768px) {
        .transactions-container {
            padding: 16px;
        }

        .table-wrapper {
            overflow-x: scroll;
        }

        .modal-tabs {
            overflow-x: scroll;
        }

        .tab-btn {
            min-width: 120px;
        }
    }
</style>
