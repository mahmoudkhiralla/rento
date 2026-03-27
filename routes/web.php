<?php

use App\Http\Controllers\Admin\ActivePlacesController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminsController;
use App\Http\Controllers\Admin\AreasController;
use App\Http\Controllers\Admin\BookingsController;
use App\Http\Controllers\Admin\CitiesController;
use App\Http\Controllers\Admin\CommissionsController;
use App\Http\Controllers\Admin\ComplaintsController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NotificationsController;
use App\Http\Controllers\Admin\PaymentsController;
use App\Http\Controllers\Admin\PropertiesController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SupportTicketsController;
use App\Http\Controllers\Admin\UsersController;
use App\Models\ActivePlace;
use App\Models\Booking;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    // صفحة سبلاش كبداية للتطبيق، ثم سيتم تحويل المستخدم تلقائيًا
    return view('splash');
});

// صفحة الداشبورد للأدمن فقط
// Main dashboard route (admin only)
Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardPageController::class, 'index'])
    ->middleware('auth:admin')
    ->name('dashboard');

// Dashboard routes group (admin only)
Route::middleware(['auth:admin'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardPageController::class, 'index'])->name('index');

    // العقارات
    // تحويل الصفحة إلى ديناميكية عبر الكنترولر مع الحفاظ على نفس الواجهة
    Route::get('/properties', [PropertiesController::class, 'dashboardIndex'])->name('properties.index');
    // صفحة معاينة العقار (ديناميكية)
    Route::get('/properties/{property}/preview', [PropertiesController::class, 'preview'])->name('properties.preview');
    // صفحة معاينة الطلب الجديدة (تصميم مستقل عن معاينة العقار)
    Route::get('/properties/{property}/request', [PropertiesController::class, 'requestPreview'])->name('properties.request');
    // إيقاف نشر العقار
    Route::post('/properties/{property}/deactivate', [PropertiesController::class, 'deactivate'])->name('properties.deactivate');
    // موافقة على طلب النشر
    Route::post('/properties/{property}/approve', [PropertiesController::class, 'approve'])->name('properties.approve');
    // رفض طلب النشر
    Route::post('/properties/{property}/reject', [PropertiesController::class, 'reject'])->name('properties.reject');
    Route::get('/properties/requests', [PropertiesController::class, 'requests'])->name('properties.requests');
    // تنظيف الطلبات القديمة (محمي للأدمن)
    Route::get('/properties/requests/cleanup', [PropertiesController::class, 'cleanupOldRequests'])->name('properties.requests.cleanup');
    // حذف كل طلبات النشر غير المعتمدة فورًا (محمي للأدمن)
    Route::get('/properties/requests/purge', [PropertiesController::class, 'purgeRequests'])->name('properties.requests.purge');
    Route::view('/properties/active', 'dashboard.properties.active')->name('properties.active');
    // صفحة المناطق والتصنيفات: ربطها بالكنترولر لإحضار الداتا والتعامل مع الإيقاف/التفعيل
    Route::get('/properties/areas', [PropertiesController::class, 'areas'])->name('properties.areas');
    // إدارة أنواع العقارات: إنشاء وتبديل حالة النوع
    Route::post('/property-types', [PropertiesController::class, 'storeType'])->name('property-types.store');
    Route::post('/property-types/{type}/toggle', [PropertiesController::class, 'toggleType'])->name('property-types.toggle');
    Route::post('/property-types/toggle-by-rental', [PropertiesController::class, 'toggleByRental'])->name('property-types.toggle-by-rental');
    // تحديث اسم نوع العقار
    Route::post('/property-types/{type}/update', [PropertiesController::class, 'updateType'])->name('property-types.update');
    Route::view('/properties/categories', 'dashboard.properties.categories')->name('properties.categories');

    // الطلبات
    Route::view('/orders/latest', 'dashboard.orders.latest')->name('orders.latest');
    Route::view('/orders/active', 'dashboard.orders.active')->name('orders.active');
    Route::view('/orders/stats', 'dashboard.orders.stats')->name('orders.stats');

    // API: عدد طلبات الحجز المعلقة (للبادج الديناميكي)
    Route::get('/orders/pending-count', function () {
        $count = Booking::where('status', 'pending')->count();

        return response()->json(['pending' => $count]);
    })->name('orders.pending-count');

    // API: عدد الشكاوى المفتوحة (للبادج الديناميكي في السايدبار)
    Route::get('/support/pending-count', function () {
        try {
            $query = \App\Models\SupportTicket::where('status', 'open');
            if (\Illuminate\Support\Facades\Schema::hasColumn('support_tickets', 'admin_read_at')) {
                $query->whereNull('admin_read_at');
            }
            $count = $query->count();
        } catch (\Throwable $e) {
            $count = 0;
        }

        return response()->json(['unread' => $count]);
    })->name('support.pending-count');

    // المستخدمون
    Route::view('/users/stats', 'dashboard.users.stats')->name('users.stats');
    Route::get('/users/list', function () {
        $query = request('q');
        $type = request('type');
        $type = $type === 'all' ? null : $type;
        $suspendedQuery = request('q_suspended');

        $users = User::when($query, function ($q) use ($query) {
            $q->where(function ($qq) use ($query) {
                $qq->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            });
        })
            ->when($type, function ($q) use ($type) {
                $q->where('user_type', $type);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(5)
            ->withQueryString();

        $userIds = collect($users->items())->pluck('id');
        $agg = \App\Models\Review::selectRaw('reviewed_user_id, AVG(rating) as avg_rating, COUNT(*) as total_reviews')
            ->whereIn('reviewed_user_id', $userIds)
            ->groupBy('reviewed_user_id')
            ->get()
            ->keyBy('reviewed_user_id');
        $ratingsMap = [];
        foreach ($userIds as $uid) {
            $row = $agg->get($uid);
            $ratingsMap[$uid] = [
                'avg' => round(($row->avg_rating ?? 0), 1),
                'count' => (int) ($row->total_reviews ?? 0),
            ];
        }

        $totalUsers = User::count();
        $newUsersMonth = User::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();
        $activeLandlords = User::where('user_type', 'landlord')->where('status', 'active')->count();
        $newUsersWeek = User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $newLandlordsWeek = User::where('user_type', 'landlord')
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        $suspendedUsers = User::where('status', 'suspended')
            ->when($suspendedQuery, function ($q) use ($suspendedQuery) {
                $q->where(function ($qq) use ($suspendedQuery) {
                    $qq->where('name', 'like', "%{$suspendedQuery}%")
                        ->orWhere('email', 'like', "%{$suspendedQuery}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(5)
            ->withQueryString();

        return view('dashboard.users.list', compact(
            'users',
            'totalUsers',
            'newUsersMonth',
            'activeLandlords',
            'newUsersWeek',
            'newLandlordsWeek',
            'suspendedUsers',
            'query',
            'type',
            'ratingsMap'
        ));
    })->name('users.list');

    // صفحة المستأجر: عرض باستخدام باراميتر ?id أو أول مستأجر
    Route::get('/users/show', function () {
        $id = request('id');
        $user = $id
            ? User::findOrFail($id)
            : User::where('user_type', 'tenant')->orderByDesc('created_at')->firstOrFail();

        // الحجوزات السابقة مع العقار (مستأجر)
        $previousBookings = Booking::with('property')
            ->where('user_id', $user->id)
            ->orderByDesc('start_date')
            ->take(3)
            ->get();

        $previousProperties = $previousBookings->map(function ($booking) {
            $p = $booking->property;

            return (object) [
                'image' => $p->image ?? null,
                'name' => $p->title ?? 'اسم العقار',
                'location' => ($p->city ?? 'اسم المدينة').(isset($p->area) ? ' - '.$p->area : ''),
                'price' => $p->price ?? null,
                'rating' => null,
            ];
        });

        // مراجعات المستأجر
        $rawReviews = Review::with(['reviewer', 'booking'])
            ->where('reviewed_user_id', $user->id)
            ->latest()
            ->take(20)
            ->get();

        $reviews = $rawReviews->map(function ($r) {
            $avatar = $r->reviewer?->avatar;
            $avatarSrc = null;
            if (! empty($avatar)) {
                $avatarSrc = \Illuminate\Support\Str::startsWith($avatar, ['http://','https://'])
                    ? $avatar
                    : (\Illuminate\Support\Str::startsWith($avatar, ['/storage/','storage/'])
                        ? asset(ltrim($avatar, '/'))
                        : \Illuminate\Support\Facades\Storage::url($avatar));
            }
            return (object) [
                'reviewer_name' => $r->reviewer?->name,
                'reviewer_avatar' => $r->reviewer?->avatar,
                'reviewer_avatar_src' => $avatarSrc,
                'start_date' => $r->start_date ?? optional($r->booking)->start_date,
                'end_date' => $r->end_date ?? optional($r->booking)->end_date,
                'rating' => $r->rating,
                'comment' => $r->comment,
                'created_at' => $r->created_at,
            ];
        });

        $totalReviews = $rawReviews->count();
        $avgRating = round($rawReviews->avg('rating') ?: 0, 1);
        $reviewsList = $rawReviews->take(5);
        $inquiryTickets = \App\Models\SupportTicket::where('user_id', $user->id)->get();
        $avgResponseHours = $inquiryTickets->filter(fn($t) => $t->last_replied_at)->avg(fn($t) => $t->created_at->diffInHours($t->last_replied_at));
        $mapHoursToRating = function ($h) {
            if ($h <= 2) return 4.8;
            if ($h <= 6) return 4.6;
            if ($h <= 12) return 4.4;
            if ($h <= 24) return 4.2;
            if ($h <= 48) return 4.0;
            return 3.8;
        };
        $inquiryResponseRating = is_null($avgResponseHours) ? null : $mapHoursToRating($avgResponseHours);
        $bookingAcceptanceRating = null;
        if (in_array(($user->user_type ?? ''), ['landlord', 'both'], true)) {
            $confirmedBookings = Booking::where('user_id', $user->id)->where('status', 'confirmed')->get();
            $avgAcceptHours = $confirmedBookings->avg(fn($b) => optional($b->created_at)->diffInHours($b->updated_at));
            $bookingAcceptanceRating = is_null($avgAcceptHours) ? null : $mapHoursToRating($avgAcceptHours);
        }
        $ratings = [
            'inquiry_response' => round($rawReviews->avg('inquiry_response') ?: 0, 1) ?: null,
            'booking_acceptance_speed' => round($rawReviews->avg('booking_acceptance_speed') ?: 0, 1) ?: null,
            'timely_delivery' => round($rawReviews->avg('timely_delivery') ?: 0, 1) ?: null,
        ];

        return view('dashboard.users.show', compact('user', 'previousProperties', 'reviews', 'totalReviews', 'ratings', 'avgRating'));
    })->name('users.show.page');

    // عرض مستخدم منفرد في لوحة التحكم (بيانات ديناميكية)
    Route::get('/users/{user}', function (User $user) {
        // الحجوزات السابقة مع العقار
        $previousBookings = Booking::with('property')
            ->where('user_id', $user->id)
            ->orderByDesc('start_date')
            ->take(3)
            ->get();

        // تحويل الحجوزات إلى شكل مناسب للواجهة
        $previousProperties = $previousBookings->map(function ($booking) {
            $p = $booking->property;

            return (object) [
                'image' => $p->image ?? null,
                'name' => $p->title ?? 'اسم العقار',
                'location' => $p->city ?? 'اسم المدينة - المنطقة',
                'price' => $p->price ?? null,
                'rating' => null,
            ];
        });

        // جلب المراجعات واحتساب الملخصات
        $rawReviews = Review::with(['reviewer', 'booking'])
            ->where('reviewed_user_id', $user->id)
            ->latest()
            ->take(20)
            ->get();

        $reviews = $rawReviews->map(function ($r) {
            $avatar = $r->reviewer?->avatar;
            $avatarSrc = null;
            if (! empty($avatar)) {
                $avatarSrc = \Illuminate\Support\Str::startsWith($avatar, ['http://','https://'])
                    ? $avatar
                    : (\Illuminate\Support\Str::startsWith($avatar, ['/storage/','storage/'])
                        ? asset(ltrim($avatar, '/'))
                        : \Illuminate\Support\Facades\Storage::url($avatar));
            }
            return (object) [
                'reviewer_name' => $r->reviewer?->name,
                'reviewer_avatar' => $r->reviewer?->avatar,
                'reviewer_avatar_src' => $avatarSrc,
                'start_date' => $r->start_date ?? optional($r->booking)->start_date,
                'end_date' => $r->end_date ?? optional($r->booking)->end_date,
                'rating' => $r->rating,
                'comment' => $r->comment,
                'created_at' => $r->created_at,
            ];
        });

        $totalReviews = $rawReviews->count();
        $avgRating = round($rawReviews->avg('rating') ?: 0, 1);
        $inquiryTickets = \App\Models\SupportTicket::where('user_id', $user->id)->get();
        $avgResponseHours = $inquiryTickets->filter(fn($t) => $t->last_replied_at)->avg(fn($t) => $t->created_at->diffInHours($t->last_replied_at));
        $mapHoursToRating = function ($h) {
            if ($h <= 2) return 4.8;
            if ($h <= 6) return 4.6;
            if ($h <= 12) return 4.4;
            if ($h <= 24) return 4.2;
            if ($h <= 48) return 4.0;
            return 3.8;
        };
        $inquiryResponseRating = is_null($avgResponseHours) ? null : $mapHoursToRating($avgResponseHours);
        $bookingAcceptanceRating = null;
        if (in_array(($user->user_type ?? ''), ['landlord', 'both'], true)) {
            $confirmedBookings = Booking::where('user_id', $user->id)->where('status', 'confirmed')->get();
            $avgAcceptHours = $confirmedBookings->avg(fn($b) => optional($b->created_at)->diffInHours($b->updated_at));
            $bookingAcceptanceRating = is_null($avgAcceptHours) ? null : $mapHoursToRating($avgAcceptHours);
        }
        $ratings = [
            'inquiry_response' => round($rawReviews->avg('inquiry_response') ?: 0, 1) ?: null,
            'booking_acceptance_speed' => round($rawReviews->avg('booking_acceptance_speed') ?: 0, 1) ?: null,
            'timely_delivery' => round($rawReviews->avg('timely_delivery') ?: 0, 1) ?: null,
        ];

        return view('dashboard.users.show', compact('user', 'previousProperties', 'reviews', 'totalReviews', 'ratings', 'avgRating'));
    })->name('users.show');

    // صفحة المؤجر: عرض معلومات المؤجر وقائمة عقاراته وإحصاءات مرتبطة
    Route::get('/landlords/{user}', function (User $user) {
        // ضمان أن الصفحة خاصة بالمؤجرين فقط
        abort_unless(in_array($user->user_type, ['landlord', 'both']), 404);

        // جلب عقارات المؤجر مع عدد الحجوزات
        $properties = \App\Models\Property::where('user_id', $user->id)
            ->withCount('bookings')
            ->latest()
            ->get();

        // عدد العقارات النشطة (المعتمدة)
        $activePropertiesCount = $properties->where('approved', true)->count();

        // الأماكن النشطة من جدول العقارات المنشورة حسب المستخدم
        $activePlaces = \App\Models\Property::where('user_id', $user->id)
            ->when(\Illuminate\Support\Facades\Schema::hasColumn('properties', 'status'), function ($q) {
                $q->where('status', 'published');
            }, function ($q) {
                $q->where('approved', true);
            })
            ->latest()
            ->get()
            ->map(function ($p) {
                $bookingType = null;
                $priceUnit = 'د.ل';
                if ($p->rental_type) {
                    if ($p->rental_type === 'يومي') {
                        $bookingType = 'إيجار يومي';
                        $priceUnit = 'د.ل / اليوم';
                    } elseif ($p->rental_type === 'شهري') {
                        $bookingType = 'إيجار شهري';
                        $priceUnit = 'د.ل / الشهر';
                    } else {
                        $bookingType = $p->rental_type;
                    }
                }
                return (object) [
                    'id' => $p->id,
                    'property_id' => $p->id,
                    'user_type' => 'landlord',
                    'name' => $p->title,
                    'city' => $p->city,
                    'area' => $p->address,
                    'available_from' => $p->created_at,
                    'booking_type' => $bookingType,
                    'price' => $p->price,
                    'price_unit' => $priceUnit,
                    'rating' => null,
                    'image' => $p->image,
                    'is_published' => true,
                ];
            });

        // إحصاءات المراجعات الخاصة بالمؤجر
        $rawReviews = Review::with(['reviewer', 'booking'])
            ->where('reviewed_user_id', $user->id)
            ->latest()
            ->get();
        // قائمة مختصرة لعرض آخر المراجعات في الواجهة (مع تجهيز الاسم والصورة)
        $reviewsList = $rawReviews->take(5)->map(function ($r) {
            $avatar = $r->reviewer?->avatar;
            $avatarSrc = null;
            if (! empty($avatar)) {
                $avatarSrc = \Illuminate\Support\Str::startsWith($avatar, ['http://','https://'])
                    ? $avatar
                    : (\Illuminate\Support\Str::startsWith($avatar, ['/storage/','storage/'])
                        ? asset(ltrim($avatar, '/'))
                        : \Illuminate\Support\Facades\Storage::url($avatar));
            }
            return (object) [
                'reviewer_name' => $r->reviewer?->name,
                'reviewer_avatar_src' => $avatarSrc,
                'rating' => $r->rating,
                'comment' => $r->comment,
                'created_at' => $r->created_at,
            ];
        });
        $totalReviews = $rawReviews->count();
        $avgRating = round($rawReviews->avg('rating') ?: 0, 1);
        $ratings = [
            'property_care' => round($rawReviews->avg('property_care') ?: 0, 1) ?: null,
            'cleanliness' => round($rawReviews->avg('cleanliness') ?: 0, 1) ?: null,
            'rules_compliance' => round($rawReviews->avg('rules_compliance') ?: 0, 1) ?: null,
            'timely_delivery' => round($rawReviews->avg('timely_delivery') ?: 0, 1) ?: null,
        ];

        // المحفظة والأرباح وفق القواعد المطلوبة:
        // - رصيد المحفظة = مجموع قيمة الحجز + الهدايا فقط
        // - مكافآت النقاط = الهدايا فقط (نقاط من أسباب الهدايا/العروض)
        $wallet = $user->wallet;

        // دخل الحجوزات: معاملات مرتبطة بحجز وبقيمة موجبة
        $bookingIncome = $wallet?->transactions()
            ->whereNotNull('booking_id')
            ->where('amount', '>', 0)
            ->sum('amount') ?? 0;

        // دخل الهدايا: معاملات نوعها gift أو معاملات ائتمان تحتوي على سبب "عرض ترويجي"
        $giftIncome = $wallet?->transactions()
            ->where('amount', '>', 0)
            ->where(function ($q) {
                $q->where('type', 'gift')
                    ->orWhere(function ($qq) {
                        $qq->where('type', 'credit')
                            ->where('meta', 'like', '%عرض ترويجي%');
                    });
            })
            ->sum('amount') ?? 0;

        // رصيد المحفظة = مجموع قيم كل معاملات المحفظة (سالب/موجب)
        $walletBalance = $wallet?->transactions()->sum('amount') ?? 0;

        // مكافآت النقاط: رصيد نقاط البرنامج الفعلي من المحفظة
        $pointsBalance = $wallet?->points_balance ?? 0;

        // أرباح الشهر الأخير وفق نفس القاعدة (حجوزات + هدايا خلال 30 يوم)
        $recentAddedProfit = (
            ($wallet?->transactions()
                ->whereNotNull('booking_id')
                ->where('amount', '>', 0)
                ->where('created_at', '>=', now()->subDays(30))
                ->sum('amount') ?? 0)
            +
            ($wallet?->transactions()
                ->where('amount', '>', 0)
                ->where('created_at', '>=', now()->subDays(30))
                ->where(function ($q) {
                    $q->where('type', 'gift')
                        ->orWhere(function ($qq) {
                            $qq->where('type', 'credit')
                                ->where('meta', 'like', '%عرض ترويجي%');
                        });
                })
                ->sum('amount') ?? 0)
        );

        // الطلبات المعلقة لحجوزات عقارات المؤجر
        $pendingBookings = Booking::whereHas('property', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
            ->where('status', 'pending')
            ->count();

        // أحدث معاملات المحفظة (كل الأنواع) مع تفاصيل الحجز إن وجدت
        $transactions = $wallet?->transactions()
            ->with(['booking.user', 'booking.property'])
            ->latest()
            ->take(10)
            ->get() ?? collect();
        // أحدث معاملات المكافآت (الهدايا كفلوس)
        $pointsTransactions = $wallet?->transactions()
            ->where('amount', '>', 0)
            ->where(function ($q) {
                $q->where('type', 'gift')
                    ->orWhere(function ($qq) {
                        $qq->where('type', 'credit')
                            ->where('meta', 'like', '%عرض ترويجي%');
                    });
            })
            ->latest()
            ->take(10)
            ->get() ?? collect();

        $reviews = $rawReviews;
        return view('dashboard.landlords.show', compact(
            'user',
            'properties',
            'activePlaces',
            'activePropertiesCount',
            'totalReviews',
            'reviewsList',
            'avgRating',
            'ratings',
            'walletBalance',
            'pointsBalance',
            'recentAddedProfit',
            'pendingBookings',
            'transactions',
            'pointsTransactions',
            'reviews'
        ));
    })->name('landlords.show');
    Route::view('/users/suspended', 'dashboard.users.suspended')->name('users.suspended');

    // الدعم والشكاوى
    Route::view('/support/stats', 'dashboard.support.stats')->name('support.stats');
    Route::get('/support/tickets', [SupportTicketsController::class, 'index'])->name('support.tickets');
    Route::get('/support/tickets/{id}', [SupportTicketsController::class, 'show'])->name('support.tickets.show');
    Route::post('/support/tickets/{id}/reply', [SupportTicketsController::class, 'reply'])->name('support.tickets.reply');
    Route::post('/support/tickets/{id}/close', [SupportTicketsController::class, 'close'])->name('support.tickets.close');
    Route::view('/support/reasons', 'dashboard.support.reasons')->name('support.reasons');

    // الدفع والتحصيل
    Route::get('/payments/stats', [\App\Http\Controllers\Admin\PenaltiesController::class, 'index'])->name('payments.stats');
    Route::get('/payments/penalties/{id}', [\App\Http\Controllers\Admin\PenaltiesController::class, 'show']);
    Route::get('/payments/penalties/{id}/preview', [\App\Http\Controllers\Admin\PenaltiesController::class, 'preview'])->name('payments.penalties.preview');
    Route::post('/payments/penalties/{id}/status', [\App\Http\Controllers\Admin\PenaltiesController::class, 'updateStatus']);
    Route::post('/payments/settings', [\App\Http\Controllers\Admin\PenaltiesController::class, 'updateSettings'])->name('payments.settings.update');
    Route::get('/payments/cards', [\App\Http\Controllers\Admin\PaymentCardsController::class, 'index'])->name('payments.cards');
    Route::post('/payments/cards', [\App\Http\Controllers\Admin\PaymentCardsController::class, 'store']);
    Route::get('/payments/cards/{id}', [\App\Http\Controllers\Admin\PaymentCardsController::class, 'show']);
    Route::post('/payments/cards/{id}/status', [\App\Http\Controllers\Admin\PaymentCardsController::class, 'updateStatus']);
    Route::get('/payments/cards/export', [\App\Http\Controllers\Admin\PaymentCardsController::class, 'export']);
    Route::get('/payments/refunds', [\App\Http\Controllers\Admin\RefundsController::class, 'index'])->name('payments.refunds');
    Route::get('/payments/refunds/{id}', [\App\Http\Controllers\Admin\RefundsController::class, 'show']);
    Route::post('/payments/refunds/{id}/status', [\App\Http\Controllers\Admin\RefundsController::class, 'updateStatus']);
    Route::view('/payments/wallets', 'dashboard.payments.wallets')->name('payments.wallets');
    Route::get('/payments/transactions', [\App\Http\Controllers\Admin\TransactionsController::class, 'index'])->name('payments.transactions');
    Route::get('/payments/transactions/{id}', [\App\Http\Controllers\Admin\TransactionsController::class, 'show']);
    Route::post('/payments/transactions/{id}/status', [\App\Http\Controllers\Admin\TransactionsController::class, 'updateStatus']);
    Route::get('/payments/commissions', [\App\Http\Controllers\Admin\CommissionsController::class, 'index'])->name('payments.commissions');
    Route::post('/payments/commissions', [\App\Http\Controllers\Admin\CommissionsController::class, 'update'])->name('payments.commissions.update');

    // العروض والإشعارات
    Route::view('/offers', 'dashboard.offers.offers')->name('offers.index');
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::view('/', 'dashboard.notifications.notifications')->name('index');
        Route::view('/create', 'dashboard.notifications.create')->name('create');
        Route::view('/stats', 'dashboard.notifications.notifications')->name('stats');
    });
    Route::view('/offers/notifications', 'dashboard.notifications.notifications')->name('offers.notifications');
    Route::view('/offers/stats', 'dashboard.notifications.notifications')->name('offers.stats');

    // التقارير
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportsController::class, 'index'])->name('reports');
});

// مسارات دخول وخروج الأدمن
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    });

    Route::post('/logout', [AdminAuthController::class, 'logout'])
        ->middleware('auth:admin')
        ->name('logout');
});

// لوحة التحكم للمشرفين فقط باستخدام حارس الأدمن
Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin profile page
    Route::view('/profile', 'admin.auth.profile')->name('profile');
    Route::view('/profile/edit', 'admin.auth.profile-edit')->name('profile.edit');
    Route::view('/password/change', 'admin.auth.password-change')->name('password.change');
    Route::post('/profile/edit', [AdminAuthController::class, 'updateProfile'])->name('profile.update');
    Route::post('/password/change', [AdminAuthController::class, 'updatePassword'])->name('password.update');

    Route::resource('users', UsersController::class);
    Route::resource('properties', PropertiesController::class);
    Route::resource('bookings', BookingsController::class)->only(['index', 'show', 'update']);
    Route::resource('complaints', ComplaintsController::class)->only(['index', 'show', 'update']);
    Route::resource('notifications', NotificationsController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::resource('cities', CitiesController::class)->except(['show']);
    Route::resource('areas', AreasController::class)->except(['show']);

    Route::get('payments', [PaymentsController::class, 'index'])->name('payments.index');
    Route::get('payments/commissions', [CommissionsController::class, 'index'])->name('payments.commissions');
    Route::post('payments/commissions', [CommissionsController::class, 'update'])->name('payments.commissions.update');
    Route::get('reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');

    // إدارة المشرفين
    Route::get('admins', [AdminsController::class, 'index'])->name('admins.index');
    Route::post('admins', [AdminsController::class, 'store'])->name('admins.store');
    Route::get('admins/create', [AdminsController::class, 'create'])->name('admins.create');
    Route::get('admins/{admin}', [AdminsController::class, 'show'])->name('admins.show');
    Route::get('admins/{admin}/edit', [AdminsController::class, 'edit'])->name('admins.edit');
    Route::put('admins/{admin}', [AdminsController::class, 'update'])->name('admins.update');
    Route::delete('admins/{admin}', [AdminsController::class, 'destroy'])->name('admins.destroy');
    // تعليق/تفعيل المستخدم (داخل مجموعة الأدمن)
    Route::post('users/{user}/suspend', [UsersController::class, 'suspend'])->name('users.suspend');
    Route::post('users/{user}/activate', [UsersController::class, 'activate'])->name('users.activate');
    Route::post('users/activate-bulk', [UsersController::class, 'activateBulk'])->name('users.activate.bulk');

    // إدارة الأماكن النشطة: إيقاف النشر وحذف
    Route::post('active-places/{place}/stop', [ActivePlacesController::class, 'stop'])->name('active-places.stop');
    Route::post('active-places/{place}/publish', [ActivePlacesController::class, 'publish'])->name('active-places.publish');
    Route::delete('active-places/{place}', [ActivePlacesController::class, 'destroy'])->name('active-places.destroy');
});
