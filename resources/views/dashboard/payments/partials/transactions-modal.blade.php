<!-- Modal: Transaction Details -->
<div class="modal" id="transactionModal">
    <div class="modal-overlay" onclick="closeTransactionModal()"></div>
    <div class="modal-content modal-transaction">
        <div class="modal-header">
            <h3 class="modal-title">تفاصيل العملية المالية</h3>
            <button class="close-btn" onclick="closeTransactionModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="modal-body">
            <div class="details-grid">
                <div class="details-row">
                    <div class="details-cell">
                        <div class="detail-item">
                            <label>اسم المستخدم</label>
                            <div class="user-info-detail">
                                <img src="https://ui-avatars.com/api/?name=User&background=3B82F6&color=fff" alt="User" class="user-avatar" id="transModalUserAvatar">
                                <div class="user-text">
                                    <span class="user-name" id="transModalUserName">اسم المستخدم</span>
                                    <span class="user-email" id="transModalUserEmail">jane.cooper@example.com</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="details-cell">
                        <div class="detail-item">
                            <label>التحقق</label>
                            <div id="transModalVerification"><i class="fas fa-check-circle" style="color:#3B82F6"></i></div>
                        </div>
                    </div>
                    <div class="details-cell">
                        <div class="detail-item">
                            <label>نوع الحساب</label>
                            <div id="transModalAccountType">مستأجر</div>
                        </div>
                    </div>
                    <div class="details-cell">
                        <div class="detail-item">
                            <label>المبلغ</label>
                            <span class="detail-value amount" id="transModalAmount">150 د.ل</span>
                        </div>
                    </div>
                </div>

                <div class="details-row">
                    <div class="details-cell">
                        <div class="detail-item">
                            <label>نوع العملية</label>
                            <div id="transModalType">إيداع رصيد المستخدم</div>
                        </div>
                    </div>
                    <div class="details-cell">
                        <div class="detail-item">
                            <label>تاريخ العملية</label>
                            <div id="transModalDate">25 / 5 / 2025</div>
                        </div>
                    </div>
                    <div class="details-cell">
                        <div class="detail-item">
                            <label>الحالة</label>
                            <span class="status-badge status-completed" id="transModalStatus">تم الاكتمال</span>
                        </div>
                    </div>
                    <div class="details-cell">
                        <div class="detail-item">
                            <label>الرصيد الحالي</label>
                            <div id="transModalBalance">0 د.ل</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeTransactionModal()">إغلاق</button>
        </div>
    </div>
</div>
