<!-- Modal: Refund Details -->
<div class="modal" id="refundDetailsModal">
    <div class="modal-overlay" onclick="closeRefundDetailsModal()"></div>
    <div class="modal-content modal-refund">
        <div class="modal-header">
            <h3 class="modal-title">طلب سحب رصيد</h3>
            <button class="close-btn" onclick="closeRefundDetailsModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="modal-body">
            <div class="details-grid">
                <div class="details-row details-header">
                    <div class="details-cell">اسم المستخدم</div>
                    <div class="details-cell">التحقق</div>
                    <div class="details-cell">نوع الحساب</div>
                    <div class="details-cell">المبلغ المطلوب</div>
                </div>
                <div class="details-row">
                    <div class="details-cell">
                        <div class="user-info-detail">
                            <img src="https://ui-avatars.com/api/?name=User&background=3B82F6&color=fff" alt="User" class="user-avatar" id="modalUserAvatar">
                            <div class="user-text">
                                <span class="user-name" id="modalUserName">اسم المستخدم</span>
                                <span class="user-email" id="modalUserEmail">jane.cooper@example.com</span>
                            </div>
                        </div>
                    </div>
                    <div class="details-cell"><i class="fas fa-check-circle" style="color:#3B82F6"></i></div>
                    <div class="details-cell" id="modalAccountType">مستأجر</div>
                    <div class="details-cell"><span class="detail-value amount" id="modalAmount">150 د.ل</span></div>
                </div>

                <div class="details-row details-header">
                    <div class="details-cell">نوع التحويل</div>
                    <div class="details-cell">تاريخ الطلب</div>
                    <div class="details-cell">الرصيد الحالي</div>
                    <div class="details-cell"></div>
                </div>
                <div class="details-row">
                    <div class="details-cell" id="modalTransferType">حساب بنكي</div>
                    <div class="details-cell" id="modalTransferDate">25 / 5 / 2025</div>
                    <div class="details-cell" id="modalCurrentBalance">150 د.ل</div>
                    <div class="details-cell"></div>
                </div>

                <div class="details-row details-header">
                    <div class="details-cell">اسم البنك/المحفظة</div>
                    <div class="details-cell">رقم الحساب/المحفظة</div>
                    <div class="details-cell">الحالة</div>
                    <div class="details-cell"></div>
                </div>
                <div class="details-row">
                    <div class="details-cell" id="modalBankName">مصرف الجمهورية</div>
                    <div class="details-cell" id="modalAccountNumber">9274639463-32084</div>
                    <div class="details-cell"><span class="status-badge status-pending" id="modalStatus">بإنتظار الموافقة</span></div>
                    <div class="details-cell"></div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-reject" onclick="rejectRefund()">رفض</button>
            <button type="button" class="btn-approve" onclick="approveRefund()">موافقة</button>
        </div>
    </div>
</div>
