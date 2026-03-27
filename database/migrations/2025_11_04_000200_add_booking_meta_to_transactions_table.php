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
        Schema::table('transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('transactions', 'booking_id')) {
                $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete()->after('wallet_id');
            }
            if (! Schema::hasColumn('transactions', 'meta')) {
                $table->json('meta')->nullable()->after('type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'booking_id')) {
                $table->dropConstrainedForeignId('booking_id');
            }
            if (Schema::hasColumn('transactions', 'meta')) {
                $table->dropColumn('meta');
            }
        });
    }
};
