<div class="modal" id="penaltyModal">
    <div class="modal-overlay" onclick="closePenaltyModal()"></div>
    <div class="modal-content modal-penalty">
        <div class="modal-header">
            <h3 class="modal-title">تفاصيل الغرامة أو التعويض</h3>
            <button class="close-btn" onclick="closePenaltyModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="table-card cols-4">
                <div class="table-header">
                    <div class="table-cell">اسم المستخدم</div>
                    <div class="table-cell">التحقق</div>
                    <div class="table-cell">نوع الحساب</div>
                    <div class="table-cell">مبلغ الغرامة أو التعويض</div>
                </div>
                <div class="table-row">
                    <div class="table-cell">
                        <div class="detail-item">
                            <div class="user-info-detail">
                                <img id="prevUserAvatar" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==" alt="User" class="user-avatar">
                                <div class="user-text">
                                    <span id="prevUserName" class="user-name"></span>
                                    <span id="prevUserEmail" class="user-email"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-cell">
                        <div class="detail-item">
                            <span id="prevVerified" class="verified-icon"></span>
                        </div>
                    </div>
                    <div class="table-cell">
                        <div class="detail-item">
                            <span id="prevAccountType"></span>
                        </div>
                    </div>
                    <div class="table-cell">
                        <div class="detail-item">
                            <span id="prevAmount"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-card cols-3">
                <div class="table-header">
                    <div class="table-cell">النوع</div>
                    <div class="table-cell">تاريخ العملية</div>
                    <div class="table-cell">الرصيد الحالي</div>
                </div>
                <div class="table-row">
                    <div class="table-cell"><span id="prevType"></span></div>
                    <div class="table-cell"><span id="prevDate"></span></div>
                    <div class="table-cell"><span id="prevBalance"></span></div>
                </div>
            </div>

            <div class="table-card cols-2">
                <div class="table-header">
                    <div class="table-cell">السبب</div>
                    <div class="table-cell">الحالة</div>
                </div>
                <div class="table-row">
                    <div class="table-cell"><div id="prevReason" class="detail-value"></div></div>
                    <div class="table-cell"><span id="prevStatus" class="status-badge"></span></div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closePenaltyModal()">إغلاق</button>
        </div>
    </div>
</div>
