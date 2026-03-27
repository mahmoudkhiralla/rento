<style>
    /* Main Container */
    .payment-cards-container {
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
    }

    .page-title {
        font-size: 24px;
        font-weight: 700;
        color: #1F2937;
        margin: 0;
    }

    .btn-primary {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: #3B82F6;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-primary:hover {
        background: #2563EB;
    }

    .btn-secondary {
        padding: 10px 20px;
        background: white;
        color: #374151;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-secondary:hover {
        background: #F9FAFB;
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
        margin-bottom: 20px;
        gap: 20px;
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

    .search-box input::placeholder {
        color: #9CA3AF;
    }

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
        text-align: center;
        font-size: 13px;
        font-weight: 700;
        color: #374151;
        white-space: nowrap;
    }

    .data-table td {
        padding: 16px;
        text-align: center;
        font-size: 13px;
        color: #4B5563;
        border-bottom: 1px solid #F3F4F6;
    }

    .data-table tbody tr:hover {
        background: #F9FAFB;
    }

    /* Card Link */
    .card-link {
        color: #3B82F6;
        text-decoration: underline;
        background: none;
        border: none;
        cursor: pointer;
        font-size: 13px;
    }

    .card-link:hover {
        color: #2563EB;
    }

    /* Status Badge */
    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-pending {
        background: #DBEAFE;
        color: #1E40AF;
    }

    .status-active {
        background: #D1FAE5;
        color: #059669;
    }

    .status-expired {
        background: #F3F4F6;
        color: #6B7280;
    }

    .status-cancelled {
        background: #FEF3C7;
        color: #D97706;
    }

    /* Additional statuses to match table design */
    .status-issued {
        background: #FFF3CD; /* أصفر لحالة "مصدر" */
        color: #856404;
        border: 1px solid #FFE8A1;
    }
    .status-cancelled-red {
        background: #F3D6D6;
        color: #7E2E2E;
    }

    /* User Info */
    .user-info {
        display: flex;
        align-items: center;
        gap: 12px;
        justify-content: center;
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
    }

    .user-details {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .user-name {
        font-size: 13px;
        font-weight: 600;
        color: #1F2937;
    }

    .user-role {
        font-size: 12px;
        color: #6B7280;
    }

    .user-email {
        font-size: 12px;
        color: #6B7280;
    }

    /* Icon Buttons */
    .icon-btn,
    .notes-btn,
    .action-btn {
        background: none;
        border: none;
        color: #6B7280;
        cursor: pointer;
        padding: 4px 8px;
        transition: all 0.3s;
    }

    .icon-btn:hover,
    .notes-btn:hover,
    .action-btn:hover {
        color: #1F2937;
    }

    /* Pagination */
    .pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
        padding-top: 20px;
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

    /* ===== Modal Overlay ===== */
    .modal {
        display: none; /* استخدم display:flex عند الفتح */
        position: fixed;
        inset: 0;
        justify-content: center;
        align-items: center;
        z-index: 2001; /* أعلى من السايدبار */
        font-family: "Tajawal", sans-serif;
    }

    .modal-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
        backdrop-filter: blur(3px);
        -webkit-backdrop-filter: blur(3px);
        z-index: 0;
    }

    /* ===== Modal Card ===== */
    .modal-content {
        position: relative;
        width: 600px;
        background: #FFFFFF;
        border-radius: 14px;
        box-shadow: 0px 10px 30px rgba(0,0,0,0.08);
        overflow: hidden;
        animation: fadeIn .25s ease-out;
        z-index: 1;
        direction: rtl;
    }

    /* ===== Header ===== */
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 24px;
        border-bottom: 1px solid #E7ECF2;
        background: #FFFFFF;
    }

    .modal-title {
        font-size: 18px;
        color: #0E1C2F;
        font-weight: 700;
    }

    .close-btn {
        background: none;
        border: none;
        color: #6B7280;
        font-size: 18px;
        cursor: pointer;
    }

    /* ===== Body Styling ===== */
    .modal-body {
        padding: 0;
    }

    .details-row {
        display: grid;
        gap: 0;
        padding: 14px 22px;
    }

    /* إلغاء الخلفيات المتناوبة لضمان التحكم الكامل */
    .details-row:nth-child(even) {
        background: transparent;
    }

    /* Table-like block: header blue, data white */
    .details-row.headers {
        background: #EAF3FE; /* لبني للهيدر */
        border-bottom: 1px solid #E7ECF2;
    }
    .details-row.headers .detail-item label {
        font-weight: 600;
        color: #667085;
        font-size: 12.5px;
    }
    .details-row.data {
        background: #FFFFFF; /* بيانات بيضاء */
    }

    .detail-item label {
        font-size: 13px;
        color: #667085;
        display: block;
        margin-bottom: 5px;
    }

    .detail-value {
        font-size: 15px;
        font-weight: 600;
        color: #0E1C2F;
    }

    /* ===== Status Badge ===== */
    .status-badge {
        padding: 3px 10px;
        font-size: 13px;
        border-radius: 6px;
        font-weight: 600;
    }

    /* ===== Avatar + User Info ===== */
    .user-info-detail {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
    }

    .user-name {
        font-size: 14px;
        color: #0E1C2F;
        font-weight: 600;
    }

    .user-email {
        font-size: 12px;
        color: #6B7280;
    }

    /* ===== Check Icon ===== */
    .check-icon {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        width: 22px;
        height: 22px;
        background: #2563EB;
        border-radius: 50%;
        color: #fff;
        font-size: 12px;
    }

    /* ===== Footer Buttons ===== */
    .modal-footer {
        display: flex;
        justify-content: space-between; /* إيقاف يمين، إغلاق شمال */
        gap: 12px;
        padding: 16px 22px;
        border-top: 1px solid #E7ECF2;
    }

    .btn-primary,
    .btn-secondary {
        padding: 8px 22px;
        border: none;
        border-radius: 7px;
        font-size: 14px;
        cursor: pointer;
        font-weight: 600;
    }

    .btn-primary {
        background: #0A66C2;
        color: white;
    }

    .btn-secondary {
        background: #F3F4F6;
        color: #374151;
    }

    .btn-secondary:disabled {
        opacity: .4;
        cursor: not-allowed;
    }

    /* Animation */
    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.98);} 
        to { opacity: 1; transform: scale(1);} 
    }

    /* Form Elements */
    .form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
        margin-bottom: 16px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-group label {
        font-size: 13px;
        font-weight: 600;
        color: #374151;
    }

    .form-control {
        padding: 10px 12px;
        border: 1px solid #E5E7EB;
        border-radius: 6px;
        font-size: 13px;
        color: #374151;
        transition: all 0.3s;
    }

    .form-control:focus {
        outline: none;
        border-color: #3B82F6;
    }

    .form-control:disabled {
        background: #F9FAFB;
        cursor: not-allowed;
    }

    /* Input with Dropdown */
    .input-with-dropdown {
        position: relative;
    }

    .dropdown-trigger {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 12px;
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: 6px;
        font-size: 13px;
        color: #6B7280;
        cursor: pointer;
        transition: all 0.3s;
    }

    .dropdown-trigger:hover {
        border-color: #3B82F6;
    }

    /* Divider */
    .divider {
        height: 1px;
        background: #E5E7EB;
        margin: 24px 0;
    }

    /* Export Section */
    .export-section {
        margin-top: 20px;
    }

    .section-subtitle {
        font-size: 15px;
        font-weight: 700;
        color: #1F2937;
        margin: 0 0 8px 0;
    }

    .section-description {
        font-size: 13px;
        color: #6B7280;
        margin: 0 0 16px 0;
    }

    .export-options {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .export-option {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px;
        background: #F9FAFB;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
    }

    .export-label {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
    }

    .export-label input[type="radio"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .export-text {
        font-size: 13px;
        font-weight: 600;
        color: #374151;
    }

    .export-format {
        font-size: 12px;
        color: #6B7280;
    }

    /* Info Box */
    .info-box {
        display: flex;
        gap: 12px;
        padding: 16px;
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
        border-radius: 8px;
        margin-top: 20px;
    }

    .info-box i {
        color: #3B82F6;
        font-size: 20px;
    }

    .info-content {
        flex: 1;
    }

    .info-content strong {
        font-size: 13px;
        color: #1F2937;
        display: block;
        margin-bottom: 8px;
    }

    .info-content ul {
        margin: 0;
        padding-right: 20px;
        font-size: 12px;
        color: #4B5563;
        line-height: 1.6;
    }

    /* Card Details Grid */
    .card-details-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
        margin-bottom: 16px;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .detail-item.full-width {
        grid-column: 1 / -1;
    }

    .detail-item label {
        font-size: 13px;
        color: #667085;
        display: block;
        margin-bottom: 5px;
    }

    .detail-value {
        font-size: 15px;
        font-weight: 600;
        color: #0E1C2F;
    }

    .details-row {
        display: grid;
        gap: 0;
        padding: 14px 22px;
    }

    .details-row:nth-child(even) {
        background: transparent; /* إزالة أي تلوين لبني متناوب */
    }

    .detail-value.ellipsis {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .check-icon {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        width: 22px;
        height: 22px;
        background: #2563EB;
        border-radius: 50%;
        color: #fff;
        font-size: 12px;
    }

    .copy-btn {
        background: none;
        border: none;
        color: #3B82F6;
        cursor: pointer;
        padding: 4px;
        transition: all 0.3s;
    }

    .copy-btn:hover {
        color: #2563EB;
    }

    .user-info-detail {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
    }

    .user-name {
        font-size: 14px;
        color: #0E1C2F;
        font-weight: 600;
    }

    .user-email {
        font-size: 12px;
        color: #6B7280;
    }

    .user-checkbox {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .user-checkbox input[type="checkbox"] {
        width: 18px;
        height: 18px;
    }

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
        .payment-cards-container {
            padding: 16px;
        }

        .page-header {
            flex-direction: column;
            align-items: stretch;
            gap: 16px;
        }

        .table-wrapper {
            overflow-x: scroll;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .card-details-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
