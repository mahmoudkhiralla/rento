<?php

use App\Http\Controllers\Api\ActivePlaceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingCancellationController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ComplaintController;
use App\Http\Controllers\Api\ExternalUsersController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\LandlordTaskController;
use App\Http\Controllers\Api\RefundController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\SupportTicketController;
use Illuminate\Support\Facades\Route;

// Auth endpoints
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('referral-link', [AuthController::class, 'referralLink'])->middleware('auth:sanctum');
    Route::post('password/forgot', [AuthController::class, 'forgotPassword']);
    Route::post('password/verify-code', [AuthController::class, 'verifyResetCode']);
    Route::post('password/reset', [AuthController::class, 'resetPassword']);
});

// Home
Route::get('home', [HomeController::class, 'index']);

// Properties
Route::get('properties', [PropertyController::class, 'index']);
Route::get('properties/published', [PropertyController::class, 'published']);
Route::get('properties/{id}', [PropertyController::class, 'show'])->whereNumber('id');
Route::get('property-types', [PropertyController::class, 'types']);
Route::get('amenities', [PropertyController::class, 'amenities']);
// Active places (public)
Route::get('active-places', [ActivePlaceController::class, 'index']);
Route::get('active-places/{id}', [ActivePlaceController::class, 'show']);

// Bookings (protected for create)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('bookings', [BookingController::class, 'index']);
    Route::post('bookings', [BookingController::class, 'store']);

    // Landlord bookings (properties owned by current user) — define before dynamic route
    Route::get('bookings/landlord', [BookingController::class, 'landlordIndex']);

    // Show single booking (tenant, landlord, admin)
    Route::get('bookings/{booking}', [BookingController::class, 'show']);

    // Update booking status (landlord/admin)
    Route::patch('bookings/{booking}/status', [BookingController::class, 'updateStatus']);

    // Cancel booking (by renter or by owner)
    Route::post('bookings/{booking}/cancel/renter', [BookingCancellationController::class, 'cancelByRenter']);
    Route::post('bookings/{booking}/cancel/owner', [BookingCancellationController::class, 'cancelByOwner']);

    // Canceled bookings list (search & filters)
    Route::get('bookings/canceled', [BookingCancellationController::class, 'listCanceled'])->name('api.bookings.canceled');

    // Properties (create by authenticated user)
    Route::post('properties', [PropertyController::class, 'store']);
    // Update & delete property (landlord/admin)
    Route::put('properties/{id}', [PropertyController::class, 'update'])->whereNumber('id');
    Route::patch('properties/{id}', [PropertyController::class, 'update'])->whereNumber('id');
    Route::delete('properties/{id}', [PropertyController::class, 'destroy'])->whereNumber('id');
    // Properties of current landlord
    Route::get('properties/mine', [PropertyController::class, 'mine']);
    // Properties summary (id & title) for current landlord
    Route::get('properties/mine/summary', [PropertyController::class, 'mineSummary']);

    // Active places (create by landlord)
    Route::post('active-places', [ActivePlaceController::class, 'store']);

    // User profile and wallet
    Route::get('users/{id}', [UserController::class, 'show']);
    Route::get('wallet', [WalletController::class, 'show']);
    // Wallet transactions (create new transaction)
    Route::post('wallet/transactions', [WalletController::class, 'addTransaction']);
    // Points: referral award and convert
    Route::post('points/referral', [WalletController::class, 'awardReferralPoints']);
    Route::post('wallet/points/convert', [WalletController::class, 'convertPoints']);

    // Refund requests
    Route::post('refunds', [RefundController::class, 'store']);
    Route::prefix('payments')->group(function () {
        Route::get('refunds', [RefundController::class, 'index']);
        Route::get('refunds/{id}', [RefundController::class, 'show']);
        Route::post('refunds', [RefundController::class, 'store']);
        Route::patch('refunds/{id}/status', [RefundController::class, 'updateStatus']);

        Route::get('penalties', [\App\Http\Controllers\Admin\PenaltiesController::class, 'apiIndex']);
        Route::post('penalties', [\App\Http\Controllers\Admin\PenaltiesController::class, 'apiStore']);
        Route::patch('penalties/{id}', [\App\Http\Controllers\Admin\PenaltiesController::class, 'apiUpdate']);
        Route::delete('penalties/{id}', [\App\Http\Controllers\Admin\PenaltiesController::class, 'apiDestroy']);
        Route::delete('penalties', [\App\Http\Controllers\Admin\PenaltiesController::class, 'apiClear']);
        Route::get('settings/cancel-penalty', [\App\Http\Controllers\Admin\PenaltiesController::class, 'apiCancelPenaltySettings']);
        Route::get('settings/compensation', [\App\Http\Controllers\Admin\PenaltiesController::class, 'apiCompensationSettings']);
        Route::post('compensations/apply', [\App\Http\Controllers\Admin\PenaltiesController::class, 'apiApplyCompensation']);
        Route::get('penalties/cancellation/preview', [\App\Http\Controllers\Admin\PenaltiesController::class, 'apiPreviewCancellationPenalty']);
    });

    // Reviews (tenant reviewed by landlord)
    Route::post('reviews', [ReviewController::class, 'store']);

    // Reviews (landlord reviewed by tenant)
    Route::post('reviews/landlord', [ReviewController::class, 'storeLandlordReview']);

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index']);

    // Complaints
    Route::post('complaints', [ComplaintController::class, 'store']);

    // Favorites (tenant-only)
    Route::get('favorites', [FavoriteController::class, 'index']);
    Route::post('favorites', [FavoriteController::class, 'store']);

    // Landlord tasks/reminders (landlord-only)
    Route::get('landlord/tasks', [LandlordTaskController::class, 'index']);
    Route::post('landlord/tasks', [LandlordTaskController::class, 'store']);
    Route::delete('landlord/tasks/{id}', [LandlordTaskController::class, 'destroy'])->whereNumber('id');

    // Support tickets
    Route::get('support/tickets', [SupportTicketController::class, 'index']);
    Route::get('support/tickets/{id}', [SupportTicketController::class, 'show']);
    Route::post('support/tickets', [SupportTicketController::class, 'store']);
    Route::post('support/tickets/{id}/reply', [SupportTicketController::class, 'reply']);
    Route::post('support/tickets/{id}/reply/system', [SupportTicketController::class, 'replySystem']);
});

// External integration routes (secured by API key middleware)
Route::prefix('external')->middleware('external.api')->group(function () {
    Route::get('users', [ExternalUsersController::class, 'index']);
    Route::get('users/{user}', [ExternalUsersController::class, 'show']);
    Route::post('users', [ExternalUsersController::class, 'store']);
    Route::put('users/{user}', [ExternalUsersController::class, 'update']);
});

// payments routes secured above under auth:sanctum
