<!-- Modal: Issue Payment Card -->
<div class="modal" id="issueCardModal">
    <div class="modal-overlay" onclick="closeIssueCardModal()"></div>
    <div class="modal-content modal-large no-scroll">
        <div class="modal-header">
            <h3 class="modal-title">إصدار بطاقات دفع</h3>
            <button class="close-btn" onclick="closeIssueCardModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="issueCardForm" onsubmit="submitIssueCard(event)">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>عدد البطاقات</label>
                        <input type="number" name="card_count" class="form-control" value="1" min="1" placeholder="اكتب عدد البطاقات">
                    </div>
                    <div class="form-group">
                        <label>قيمة البطاقة (د.ل)</label>
                        <input type="number" name="amount" class="form-control" min="0" placeholder="اكتب قيمة البطاقة">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label>القيم المقترحة</label>
                        <select id="suggestedValue" class="form-control">
                            <option value="">اختر قيمة سريعة</option>
                            <option value="25">25 د.ل</option>
                            <option value="50">50 د.ل</option>
                            <option value="100">100 د.ل</option>
                            <option value="200">200 د.ل</option>
                            <option value="500">500 د.ل</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label>صيغة ملف التصدير</label>
                        <select name="export_format" class="form-control">
                            <option value="csv">CSV</option>
                            <option value="excel">Excel Sheet</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                </div>


            </div>

            <div class="modal-footer">
                <button type="submit" class="btn-primary">إصدار</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Card Details -->
<div class="modal" id="cardDetailsModal">
    <div class="modal-overlay" onclick="closeCardDetailsModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">تفاصيل بطاقة الدفع</h3>
            <button class="close-btn" onclick="closeCardDetailsModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <!-- Row 1: Headers (Number, Amount, Status) -->
            <div class="details-row headers" style="grid-template-columns: repeat(3, 1fr);">
                <div class="detail-item"><label>رقم البطاقة</label></div>
                <div class="detail-item"><label>القيمة (د.ل)</label></div>
                <div class="detail-item"><label>الحالة</label></div>
            </div>
            <!-- Row 1: Data -->
            <div class="details-row data" style="grid-template-columns: repeat(3, 1fr);">
                <div class="detail-item">
                    <div class="detail-value ellipsis">
                        <span id="modalCardNumber">1234 - 5678 - 901 - 234</span>
                        <button class="copy-btn" onclick="copyCardNumber()">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                <div class="detail-item">
                    <span class="detail-value" id="modalAmount">25</span>
                </div>
                <div class="detail-item">
                    <span class="status-badge status-pending" id="modalStatus">مشحون</span>
                </div>
            </div>

            <!-- Row 2: Headers (Issue and Use Dates) -->
            <div class="details-row headers" style="grid-template-columns: repeat(2, 1fr);">
                <div class="detail-item"><label>تاريخ الإصدار</label></div>
                <div class="detail-item"><label>تاريخ الاستخدام</label></div>
            </div>
            <!-- Row 2: Data -->
            <div class="details-row data" style="grid-template-columns: repeat(2, 1fr);">
                <div class="detail-item">
                    <span class="detail-value" id="modalIssueDate">25 / 5 / 2025</span>
                </div>
                <div class="detail-item">
                    <span class="detail-value" id="modalExpiryDate">25 / 6 / 2025</span>
                </div>
            </div>

            <!-- Headers row: User, Renter, Balance, Rewards -->
            <div class="details-row headers" style="grid-template-columns: repeat(4, 1fr);">
                <div class="detail-item">
                    <label>اسم المستخدم</label>
                </div>
                <div class="detail-item">
                    <label>مؤجر</label>
                </div>
                <div class="detail-item">
                    <label>الرصيد</label>
                </div>
                <div class="detail-item">
                    <label>مكافآت</label>
                </div>
            </div>

            <!-- Data row: values under headers -->
            <div class="details-row data" style="grid-template-columns: repeat(4, 1fr);">
                <div class="detail-item">
                    <div class="user-info-detail">
                        <img src="https://ui-avatars.com/api/?name=User&background=3B82F6&color=fff"
                             alt="User" class="user-avatar" id="modalUserAvatar">
                        <div class="user-text">
                            <span class="user-name" id="modalUserName">اسم المستخدم</span>
                            <span class="user-email" id="modalUserEmail">jane.cooper@example.com</span>
                        </div>
                    </div>
                </div>
                <div class="detail-item">
                    <span class="check-icon"><i class="fas fa-check"></i></span>
                </div>
                <div class="detail-item">
                    <span class="detail-value" id="modalBalance">150 د.ل</span>
                </div>
                <div class="detail-item">
                    <span class="detail-value" id="modalRewards">150 د.ل</span>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-secondary" id="stopCardBtn" onclick="confirmCardDetails()" disabled>إيقاف</button>
            <button type="button" class="btn-primary" onclick="closeCardDetailsModal()">إغلاق</button>
        </div>
    </div>
</div>
