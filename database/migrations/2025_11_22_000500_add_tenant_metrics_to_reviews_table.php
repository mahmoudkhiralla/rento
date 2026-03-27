<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (! Schema::hasColumn('reviews', 'inquiry_response')) {
                $table->decimal('inquiry_response', 3, 1)->nullable()->after('timely_delivery');
            }
            if (! Schema::hasColumn('reviews', 'booking_acceptance_speed')) {
                $table->decimal('booking_acceptance_speed', 3, 1)->nullable()->after('inquiry_response');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (Schema::hasColumn('reviews', 'booking_acceptance_speed')) {
                $table->dropColumn('booking_acceptance_speed');
            }
            if (Schema::hasColumn('reviews', 'inquiry_response')) {
                $table->dropColumn('inquiry_response');
            }
        });
    }
};