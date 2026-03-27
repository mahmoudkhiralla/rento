@extends('dashboard.layouts.app')

@section('title', 'طلبات سحب الرصيد')

@section('content')
    <div class="refunds-container">
        <div class="page-header">
            <h2 class="page-title">طلبات سحب رصيد من المحفظة</h2>
        </div>

        

        <!-- Refunds Table Section -->
        <div class="table-section">
            <div class="table-header">
                <h3 class="section-title">طلبات جديدة</h3>
                <div class="search-box table-search">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="ابحث عن اسم مستخدم أو بريد إلكتروني" class="js-refunds-search" value="{{ request('search') }}">
                </div>
            </div>

            <div class="table-wrapper">
                <table class="refunds-table">
                    <thead>
                        <tr>
                            <th>اسم المستخدم</th>
                            <th>تاريخ الطلب</th>
                            <th>الرصيد الكلي</th>
                            <th>المبلغ</th>
                            <th>اسم البنك/المحفظة</th>
                            <th>رقم الحساب/المحفظة</th>
                            <th class="col-actions" style="width:110px">الإجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($refunds as $refund)
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        @php
                                            $user = $refund->user;
                                            $avatar = $user->avatar ?? null;
                                            $avatarUrl = $avatar ? (\Illuminate\Support\Str::startsWith($avatar, ['http://', 'https://']) ? $avatar : asset('storage/' . $avatar)) : ('https://ui-avatars.com/api/?name=' . urlencode($user->name ?? 'User') . '&background=3B82F6&color=fff');
                                        @endphp
                                        <img src="{{ $avatarUrl }}" class="avatar" alt="">
                                        <div class="user-texts">
                                            <div class="name">{{ $refund->user->name ?? 'اسم المستخدم' }}</div>
                                            <div class="email">{{ $refund->user->email ?? 'email@example.com' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ optional($refund->created_at)->format('Y/m/d') }}</td>
                                <td>{{ number_format(optional($refund->user->wallet)->balance ?? ($refund->amount ?? 0)) }} د.ل</td>
                                <td>{{ number_format($refund->amount ?? 0) }} د.ل</td>
                                <td>{{ $refund->bank_name ?? ($refund->account_type ?? '-') }}</td>
                                <td>{{ $refund->account_number ?? '-' }}</td>
                                <td class="action-cell">
                                    <button class="icon-btn reject" onclick="rejectRefundDirect({{ $refund->id }})"><i class="fas fa-times"></i></button>
                                    <button class="icon-btn approve" onclick="approveRefundDirect({{ $refund->id }})"><i class="fas fa-check"></i></button>
                                    <button class="icon-btn view" onclick="openRefundDetailsModal({{ $refund->id }})"><i class="fas fa-eye"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align:center">لا توجد طلبات حالياً</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($refunds->lastPage() > 1)
            <div class="pagination-wrapper">
                <span class="pagination-info">عرض {{ $refunds->count() ? $refunds->firstItem() : 0 }} إلى {{ $refunds->count() ? $refunds->lastItem() : 0 }} من {{ $refunds->total() }}</span>
                <div class="pagination">
                    @php
                        $current = $refunds->currentPage();
                        $last = $refunds->lastPage();
                        $makeUrl = function($page) {
                            return request()->fullUrlWithQuery(['page' => $page]);
                        };
                    @endphp
                    <a class="page-btn" href="{{ $current > 1 ? $makeUrl($current - 1) : '#' }}"><i class="fas fa-chevron-right"></i></a>
                    @for ($p = 1; $p <= $last; $p++)
                        <a class="page-btn {{ $p == $current ? 'active' : '' }}" href="{{ $makeUrl($p) }}">{{ $p }}</a>
                    @endfor
                    <a class="page-btn" href="{{ $current < $last ? $makeUrl($current + 1) : '#' }}"><i class="fas fa-chevron-left"></i></a>
                </div>
            </div>
            @endif
        </div>

        <div class="table-section">
            <div class="table-header">
                <h3 class="section-title">طلبات سابقة</h3>
                <div class="search-box table-search">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="ابحث عن اسم مستخدم أو بريد إلكتروني" class="js-refunds-search" value="{{ request('search') }}">
                </div>
            </div>
            <div class="table-wrapper">
                <table class="refunds-table">
                    <thead>
                        <tr>
                            <th>اسم المستخدم</th>
                            <th>تاريخ الطلب</th>
                            <th>الرصيد الكلي</th>
                            <th>المبلغ</th>
                            <th>الحالة</th>
                            <th>اسم البنك/المحفظة</th>
                            <th>رقم الحساب/المحفظة</th>
                            <th class="col-actions" style="width:110px">معاينة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($refundsPrev as $refund)
                            @php
                                $statusMap = [
                                    'pending' => ['text' => 'قيد المعاملة', 'class' => 'status-pending'],
                                    'approved' => ['text' => 'تم التحويل', 'class' => 'status-approved'],
                                    'rejected' => ['text' => 'مرفوض', 'class' => 'status-rejected'],
                                ];
                                $status = $statusMap[$refund->status ?? 'pending'] ?? $statusMap['pending'];
                            @endphp
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        @php
                                            $user = $refund->user;
                                            $avatar = $user->avatar ?? null;
                                            $avatarUrl = $avatar ? (\Illuminate\Support\Str::startsWith($avatar, ['http://', 'https://']) ? $avatar : asset('storage/' . $avatar)) : ('https://ui-avatars.com/api/?name=' . urlencode($user->name ?? 'User') . '&background=3B82F6&color=fff');
                                        @endphp
                                        <img src="{{ $avatarUrl }}" class="avatar" alt="">
                                        <div class="user-texts">
                                            <div class="name">{{ $refund->user->name ?? 'اسم المستخدم' }}</div>
                                            <div class="email">{{ $refund->user->email ?? 'email@example.com' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ optional($refund->created_at)->format('Y/m/d') }}</td>
                                <td>{{ number_format(optional($refund->user->wallet)->balance ?? ($refund->amount ?? 0)) }} د.ل</td>
                                <td>{{ number_format($refund->amount ?? 0) }} د.ل</td>
                                <td><span class="status-badge {{ $status['class'] }}">{{ $status['text'] }}</span></td>
                                <td>{{ $refund->bank_name ?? ($refund->account_type ?? '-') }}</td>
                                <td>{{ $refund->account_number ?? '-' }}</td>
                                <td class="preview-cell"><a href="javascript:void(0)" class="preview-link" onclick="openRefundDetailsModal({{ $refund->id }})">معاينة</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align:center">لا توجد طلبات سابقة حالياً</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($refundsPrev->lastPage() > 1)
            <div class="pagination-wrapper">
                <span class="pagination-info">عرض {{ $refundsPrev->count() ? $refundsPrev->firstItem() : 0 }} إلى {{ $refundsPrev->count() ? $refundsPrev->lastItem() : 0 }} من {{ $refundsPrev->total() }}</span>
                <div class="pagination">
                    @php
                        $currentPrev = $refundsPrev->currentPage();
                        $lastPrev = $refundsPrev->lastPage();
                        $makePrevUrl = function($page) {
                            return request()->fullUrlWithQuery(['prev_page' => $page]);
                        };
                    @endphp
                    <a class="page-btn" href="{{ $currentPrev > 1 ? $makePrevUrl($currentPrev - 1) : '#' }}"><i class="fas fa-chevron-right"></i></a>
                    @for ($p = 1; $p <= $lastPrev; $p++)
                        <a class="page-btn {{ $p == $currentPrev ? 'active' : '' }}" href="{{ $makePrevUrl($p) }}">{{ $p }}</a>
                    @endfor
                    <a class="page-btn" href="{{ $currentPrev < $lastPrev ? $makePrevUrl($currentPrev + 1) : '#' }}"><i class="fas fa-chevron-left"></i></a>
                </div>
            </div>
            @endif
        </div>
    </div>

    @include('dashboard.payments.partials.refunds-modal')
    @include('dashboard.payments.partials.refunds-styles')
    @include('dashboard.payments.partials.refunds-scripts')
@endsection
