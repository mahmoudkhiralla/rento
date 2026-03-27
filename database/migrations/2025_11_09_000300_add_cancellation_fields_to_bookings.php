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
        Schema::table('bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('bookings', 'canceled_at')) {
                $table->timestamp('canceled_at')->nullable()->after('updated_at');
            }
            if (! Schema::hasColumn('bookings', 'canceled_by')) {
                // قيم متوقعة: renter, owner (أو admin لاحقاً)
                $table->string('canceled_by', 20)->nullable()->after('canceled_at');
            }
            if (! Schema::hasColumn('bookings', 'cancel_reason')) {
                $table->string('cancel_reason')->nullable()->after('canceled_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'cancel_reason')) {
                $table->dropColumn('cancel_reason');
            }
            if (Schema::hasColumn('bookings', 'canceled_by')) {
                $table->dropColumn('canceled_by');
            }
            if (Schema::hasColumn('bookings', 'canceled_at')) {
                $table->dropColumn('canceled_at');
            }
        });
    }
};
