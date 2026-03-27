<script>
    (function() {
        const form = document.getElementById('commissionsForm');
        let saveTimer;
        function scheduleSave() {
            clearTimeout(saveTimer);
            saveTimer = setTimeout(saveNow, 500);
        }
        function saveNow() {
            if (!form) return;
            const formData = new FormData(form);
            try {
                formData.delete('points_enabled');
                formData.append('points_enabled', (pointsToggle && pointsToggle.checked) ? '1' : '0');
            } catch (e) {}

            const pptHidden = document.getElementById('pointsPerTransactionHidden');
            const ppd = document.querySelector('input[name="points_per_dinar"]');
            const mpc = document.querySelector('input[name="min_points_conversion"]');
            const method = document.querySelector('input[name="commission_calculation_method"]:checked');
            const percHidden = document.getElementById('commissionPercentageHidden');
            const fixedHidden = document.getElementById('commissionFixedHidden');

            if (pptHidden) formData.set('points_per_transaction', (pptHidden.value || '0'));
            if (ppd) formData.set('points_per_dinar', (ppd.value || '100'));
            if (mpc) formData.set('min_points_conversion', (mpc.value || '0'));

            if (method) formData.set('commission_calculation_method', method.value);
            if (percHidden) formData.set('commission_percentage', (percHidden.value || '0'));
            if (fixedHidden) formData.set('commission_fixed_value', (fixedHidden.value || '0'));

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(d => { if (d && d.success) showSuccessMessage(d.message || 'تم تحديث الإعدادات بنجاح'); });
        }

        const pointsToggle = document.getElementById('pointsEnabledToggle');
        const pointsFields = document.querySelectorAll('input[name="points_per_dinar"], input[name="min_points_conversion"]');
        pointsToggle?.addEventListener('change', function() {
            const disabled = !this.checked;
            pointsFields.forEach(field => {
                field.disabled = disabled;
                if (field.closest('.form-group')) field.closest('.form-group').style.opacity = disabled ? '0.5' : '1';
            });
            applyPointsDisabledState(disabled);
            saveNow();
        });
        pointsFields.forEach(field => { field.addEventListener('input', scheduleSave); field.addEventListener('blur', scheduleSave); });

        const methodRadios = document.querySelectorAll('input[name="commission_calculation_method"]');
        const percentHidden = document.getElementById('commissionPercentageHidden');
        const fixedHidden = document.getElementById('commissionFixedHidden');
        const percentInput = document.getElementById('commissionValueInput');

        function formatPercentage() {
            const raw = percentHidden.value || percentInput?.dataset?.percentage || '0';
            if (percentInput) percentInput.value = '% ' + raw;
        }
        function onMethodChange() {
            const selected = document.querySelector('input[name="commission_calculation_method"]:checked')?.value || 'percentage';
            if (selected === 'percentage') formatPercentage();
            scheduleSave();
        }
        methodRadios.forEach(r => r.addEventListener('change', onMethodChange));

        percentInput?.addEventListener('input', function() {
            const numeric = (this.value || '').replace(/[^0-9.]/g, '');
            percentHidden.value = numeric || '0';
            if (!this.value.startsWith('%')) this.value = numeric ? ('% ' + numeric) : '';
            scheduleSave();
        });

        const rows = document.querySelectorAll('.row-grid');
        let fixedPill = null;
        if (rows.length > 1) fixedPill = rows[1].querySelector('.row-left .value-pill');
        if (fixedPill) {
            fixedPill.addEventListener('click', () => {
                const parent = fixedPill.parentElement;
                const input = document.createElement('input');
                input.type = 'text';
                input.className = 'form-input compact-input';
                input.id = 'fixedInlineInput';
                input.value = fixedHidden.value || '';
                input.placeholder = 'أدخل القيمة';
                parent.replaceChild(input, fixedPill);
                input.focus();
                input.addEventListener('input', () => {
                    const numeric = (input.value || '').replace(/[^0-9.]/g, '');
                    fixedHidden.value = numeric;
                    scheduleSave();
                });
                input.addEventListener('blur', scheduleSave);
                input.addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); input.blur(); } });
            });
        }

        // Points program: inline edit for "عدد النقاط عند" pill
        const pointsCard = document.querySelector('.points-card');
        const pointsRows = pointsCard ? pointsCard.querySelectorAll('.row-grid') : [];
        const pointsPill = pointsRows.length > 1 ? pointsRows[1].querySelector('.row-left .value-pill') : null;
        const pointsHidden = document.getElementById('pointsPerTransactionHidden');
        if (pointsPill && pointsHidden) {
            pointsPill.addEventListener('click', () => {
                const parent = pointsPill.parentElement;
                const input = document.createElement('input');
                input.type = 'text';
                input.className = 'form-input compact-input';
                input.id = 'pointsInlineInput';
                input.value = pointsHidden.value || '';
                input.placeholder = 'أدخل القيمة';
                parent.replaceChild(input, pointsPill);
                input.focus();
                if (pointsToggle && !pointsToggle.checked) { input.disabled = true; }
                input.addEventListener('input', () => {
                    const numeric = (input.value || '').replace(/[^0-9.]/g, '');
                    pointsHidden.value = numeric;
                    scheduleSave();
                });
                input.addEventListener('blur', () => {
                    const numericVal = parseFloat(pointsHidden.value || '0') || 0;
                    if (numericVal <= 0) {
                        const pill = document.createElement('div');
                        pill.className = 'value-pill';
                        pill.textContent = 'أدخل القيمة';
                        input.parentElement.replaceChild(pill, input);
                        pill.addEventListener('click', () => {
                            const parent2 = pill.parentElement;
                            const input2 = document.createElement('input');
                            input2.type = 'text';
                            input2.className = 'form-input compact-input';
                            input2.id = 'pointsInlineInput';
                            input2.value = pointsHidden.value || '';
                            input2.placeholder = 'أدخل القيمة';
                            parent2.replaceChild(input2, pill);
                            input2.focus();
                            input2.addEventListener('input', () => {
                                const numeric2 = (input2.value || '').replace(/[^0-9.]/g, '');
                                pointsHidden.value = numeric2;
                                scheduleSave();
                            });
                            input2.addEventListener('blur', () => {
                                const num2 = parseFloat(pointsHidden.value || '0') || 0;
                                if (num2 <= 0) parent2.replaceChild(pill, input2);
                            });
                            input2.addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); input2.blur(); } });
                        });
                    }
                    scheduleSave();
                });
                input.addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); input.blur(); } });
            });
        }

        // Disable points fields when program is off
        function applyPointsDisabledState(disabled) {
            const inline = document.getElementById('pointsInlineInput');
            pointsFields.forEach(field => { field.disabled = disabled; });
            if (inline) inline.disabled = disabled;
        }
        
        if (pointsToggle) applyPointsDisabledState(!pointsToggle.checked);

        document.addEventListener('DOMContentLoaded', function() {
            if (pointsToggle) {
                applyPointsDisabledState(!pointsToggle.checked);
            }
            formatPercentage();
            const fixedVal = (fixedHidden?.value || '').trim();
            if (fixedVal && parseFloat(fixedVal) > 0) {
                const parent = fixedPill?.parentElement;
                if (parent && !document.getElementById('fixedInlineInput')) {
                    const input = document.createElement('input');
                    input.type = 'text';
                    input.className = 'form-input compact-input';
                    input.id = 'fixedInlineInput';
                    input.value = fixedVal;
                    input.placeholder = 'أدخل القيمة';
                    parent.replaceChild(input, fixedPill);
                    input.addEventListener('input', () => {
                        const numeric = (input.value || '').replace(/[^0-9.]/g, '');
                        fixedHidden.value = numeric;
                        scheduleSave();
                    });
                    input.addEventListener('blur', scheduleSave);
                    input.addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); input.blur(); } });
                }
            }
            const ptsVal = (document.getElementById('pointsPerTransactionHidden')?.value || '').trim();
            const pointsCard = document.querySelector('.points-card');
            const pointsRows = pointsCard ? pointsCard.querySelectorAll('.row-grid') : [];
            const pointsPill = pointsRows.length > 1 ? pointsRows[1].querySelector('.row-left .value-pill') : null;
            const pointsHidden = document.getElementById('pointsPerTransactionHidden');
            if (ptsVal && parseFloat(ptsVal) > 0 && pointsPill && pointsHidden && !document.getElementById('pointsInlineInput')) {
                const parent = pointsPill.parentElement;
                const input = document.createElement('input');
                input.type = 'text';
                input.className = 'form-input compact-input';
                input.id = 'pointsInlineInput';
                input.value = ptsVal;
                input.placeholder = 'أدخل القيمة';
                parent.replaceChild(input, pointsPill);
                if (pointsToggle && !pointsToggle.checked) { input.disabled = true; }
                input.addEventListener('input', () => {
                    const numeric = (input.value || '').replace(/[^0-9.]/g, '');
                    pointsHidden.value = numeric;
                    scheduleSave();
                });
                input.addEventListener('blur', scheduleSave);
                input.addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); input.blur(); } });
            }
        });

        function showSuccessMessage(message) {
            let messageEl = document.querySelector('.success-message');
            if (!messageEl) {
                messageEl = document.createElement('div');
                messageEl.className = 'success-message';
                messageEl.innerHTML = '<i class="fas fa-check-circle"></i><span class="message-text">' + message + '</span>';
                document.body.appendChild(messageEl);
            } else {
                messageEl.querySelector('.message-text').textContent = message;
            }
            setTimeout(() => { messageEl.classList.add('show'); }, 100);
            setTimeout(() => { messageEl.classList.remove('show'); }, 3100);
        }
    })();
</script>
