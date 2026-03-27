<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reviewed_user_id'); // المستخدم الذي تُكتب عنه المراجعة
            $table->unsignedBigInteger('reviewer_user_id'); // المستخدم الذي كتب المراجعة
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->unsignedTinyInteger('rating')->default(5); // 1-5
            // فئات التقييم
            $table->decimal('property_care', 2, 1)->nullable();
            $table->decimal('cleanliness', 2, 1)->nullable();
            $table->decimal('rules_compliance', 2, 1)->nullable();
            $table->decimal('timely_delivery', 2, 1)->nullable();
            // نص المراجعة
            $table->text('comment')->nullable();
            // فترة الإقامة (اختياري، يمكن قراءته من الحجز)
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();

            $table->foreign('reviewed_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reviewer_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
