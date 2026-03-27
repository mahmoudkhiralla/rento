@extends('dashboard.layouts.app')

@section('title', 'إعدادات العمولة')

@section('content')

    <div class="commissions-container">
        <form id="commissionsForm" action="{{ route('dashboard.payments.commissions.update') }}" method="POST">
            @csrf

            <div class="settings-card">
                <div class="card-header"><h3 class="card-title">إعدادات العمولة</h3></div>
                <div class="card-body">
                    <div class="group-title">حساب العمولة</div>
                    <div class="row-grid">
                        <div class="row-right">
                            <div class="radio-option">
                                <label class="radio-label">
                                    <input type="radio" name="commission_calculation_method" value="percentage" class="radio-input"
                                           {{ old('commission_calculation_method', $settings['commission_calculation_method']) == 'percentage' ? 'checked' : '' }}>
                                    <span class="radio-text">نسبة ثابتة</span>
                                </label>
                                <p class="option-description">استخدام نسبة ثابتة من القيمة الإجمالية للإيجار لحساب عمولة التطبيق</p>
                            </div>
                        </div>
                        <div class="row-left">
                            <input type="text" id="commissionValueInput" class="form-input compact-input"
                                   value="{{ old('commission_calculation_method', $settings['commission_calculation_method']) === 'percentage' ? ('% ' . (old('commission_percentage', $settings['commission_percentage']))) : (old('commission_fixed_value', $settings['commission_fixed_value'])) }}"
                                   data-percentage="{{ old('commission_percentage', $settings['commission_percentage']) }}"
                                   data-fixed="{{ old('commission_fixed_value', $settings['commission_fixed_value']) }}"
                                   placeholder="% 25">
                            <input type="hidden" name="commission_percentage" id="commissionPercentageHidden" value="{{ old('commission_percentage', $settings['commission_percentage']) }}">
                            <input type="hidden" name="commission_fixed_value" id="commissionFixedHidden" value="{{ old('commission_fixed_value', $settings['commission_fixed_value']) }}">
                        </div>
                    </div>
                    <div class="row-grid">
                        <div class="row-right">
                            <div class="radio-option">
                                <label class="radio-label">
                                    <input type="radio" name="commission_calculation_method" value="fixed" class="radio-input"
                                           {{ old('commission_calculation_method', $settings['commission_calculation_method']) == 'fixed' ? 'checked' : '' }}>
                                    <span class="radio-text">قيمة ثابتة</span>
                                </label>
                                <p class="option-description">استخدام مبلغ ثابت من القيمة الإجمالية للإيجار لحساب عمولة التطبيق</p>
                            </div>
                        </div>
                        <div class="row-left">
                            <div class="value-pill">أدخل القيمة</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="settings-card points-card">
                <div class="card-header"><h3 class="card-title">برنامج النقاط</h3></div>
                <div class="card-body">
                    <div class="row-grid">
                        <div class="row-right">
                            <div class="toggle-inline">
                                <input type="hidden" name="points_enabled" value="0">
                                <label class="toggle-inline" for="pointsEnabledToggle">
                                    <span class="toggle-text">تشغيل برنامج النقاط</span>
                                    <input type="checkbox" id="pointsEnabledToggle" name="points_enabled"  class="toggle-input" {{ old('points_enabled', $settings['points_enabled']) ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>
                        <div class="row-left"></div>
                    </div>

                    <div class="row-grid">
                        <div class="row-right">
                            <div class="form-section"><label class="section-label">عدد النقاط عند</label><p class="section-description">أدخل عدد النقاط التي يحصل عليها العميل عند استخدام عميل جديد الرابط الخاص به</p></div>
                        </div>
                        <div class="row-left">
                            <div class="value-pill">أدخل القيمة</div>
                            <input type="hidden" name="points_per_transaction" id="pointsPerTransactionHidden" value="{{ old('points_per_transaction', $settings['points_per_transaction']) }}">
                        </div>
                    </div>

                    <div class="row-grid">
                        <div class="row-right">
                            <div class="form-section"><label class="section-label">قيمة تحويل النقاط</label><p class="section-description">عدد النقاط لكل دينار ليبي</p></div>
                        </div>
                        <div class="row-left">
                            <input type="number" name="points_per_dinar" class="form-input compact-input" value="{{ old('points_per_dinar', $settings['points_per_dinar']) }}" placeholder="100 نقطة" min="1" step="1">
                        </div>
                    </div>

                    <div class="row-grid">
                        <div class="row-right">
                            <div class="form-section"><label class="section-label">الحد الأدنى لتحويل النقاط</label><p class="section-description">أدخل الحد الأدنى اللازم لتحويل النقاط إلى محفظة العميل</p></div>
                        </div>
                        <div class="row-left">
                            <input type="number" name="min_points_conversion" class="form-input compact-input" value="{{ old('min_points_conversion', $settings['min_points_conversion']) }}" placeholder="5 دينار" min="0" step="0.01">
                        </div>
                    </div>
                </div>
            </div>


        </form>

        <style>
            .commissions-container{padding:24px;background:#F3F4F6}
            .settings-card{background:#fff;border:1px solid #E5E7EB;border-radius:12px;margin-bottom:24px;box-shadow:0 4px 12px rgba(0,0,0,.06)}
            .card-header{padding:16px 20px;border-bottom:1px solid #F3F4F6}
            .card-title{font-size:20px;font-weight:700;color:#1F2937;margin:0}
            .card-body{padding:24px}
            .card-grid{display:grid;grid-template-columns:1fr 300px;gap:16px;align-items:start}
            .card-left{grid-column:2;display:flex;flex-direction:column;gap:10px}
            .card-right{grid-column:1}
            .row-grid{display:grid;grid-template-columns:1fr 300px;gap:12px;align-items:stretch;padding:10px 0;border-bottom:1px solid #F3F4F6}
            .row-grid:last-child{border-bottom:none}
            .row-left{grid-column:2;display:flex;flex-direction:column;gap:12px;align-items:flex-end;}
            .row-right{grid-column:1}
            .value-pill{display:flex;align-items:center;justify-content:center;padding:0;background:#D1D5DB;color:#374151;border-radius:10px;font-size:13px;width:108px;height:40px;opacity:1;transform:rotate(0deg)}
            .form-input{padding:6px 8px;border:1px solid #D1D5DB;border-radius:6px;font-size:12px;color:#1A1A1A;background:#F9FAFB;width:100px;height:40px;box-sizing:border-box;opacity:1;transform:rotate(0deg)}
            .row-left .form-input{margin-bottom:auto}
            .row-left .value-pill{margin-top:auto}
            .input-group{display:flex;align-items:center;gap:8px;max-width:240px}
            .group-title{font-size:16px;font-weight:700;color:#1F2937;margin-bottom:6px}
            .radio-option{padding:0}
            .radio-label{display:inline-flex;align-items:center;gap:8px;cursor:pointer}
            .radio-input{width:16px;height:16px}
            .radio-text{font-weight:700;color:#1F2937}
            .option-description{font-size:12px;color:#6B7280;margin:6px 0 0}
            .toggle-option{padding:8px 0 16px;border-bottom:1px solid #F3F4F6;margin-bottom:8px}
            .toggle-label{display:flex;align-items:center;justify-content:flex-end;gap:12px}
            .toggle-wrapper{display:inline-flex;align-items:center}
            .toggle-inline{display:flex;align-items:center;gap:12px;justify-content:flex-start;direction:rtl;width:100%}
            .toggle-text{font-weight:700;color:#1F2937}
            .toggle-input{position:absolute;opacity:0;width:0;height:0}
            .toggle-slider{position:relative;width:48px;height:26px;background:#D1D5DB;border-radius:26px;transition:all .3s}
            .toggle-input:checked + .toggle-slider{background:#3B82F6}
            .toggle-slider::before{content:'';position:absolute;width:20px;height:20px;background:#fff;border-radius:50%;top:3px;right:3px;transition:all .3s;box-shadow:0 2px 4px rgba(0,0,0,.2)}
            .toggle-input:checked + .toggle-slider::before{transform:translateX(-22px)}
            .section-label{font-weight:700;color:#1F2937;display:block}
            .section-description{font-size:12px;color:#6B7280;margin:4px 0 0}
            .card-actions{display:flex;justify-content:flex-end}
            .btn-submit{padding:10px 20px;border-radius:8px;background:#9CA3AF;color:#fff;border:none}
            @media (max-width:1024px){.card-grid{grid-template-columns:1fr}}
        </style>
    </div>

    @include('dashboard.payments.partials.commissions-scripts')

@endsection
