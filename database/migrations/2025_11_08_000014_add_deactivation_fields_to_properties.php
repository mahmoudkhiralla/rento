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
        Schema::table('properties', function (Blueprint $table) {
            if (! Schema::hasColumn('properties', 'deactivation_reason')) {
                $table->string('deactivation_reason')->nullable()->after('approved');
            }
            if (! Schema::hasColumn('properties', 'deactivated_at')) {
                $table->timestamp('deactivated_at')->nullable()->after('deactivation_reason');
            }
            if (! Schema::hasColumn('properties', 'deactivated_by')) {
                $table->unsignedBigInteger('deactivated_by')->nullable()->after('deactivated_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            if (Schema::hasColumn('properties', 'deactivation_reason')) {
                $table->dropColumn('deactivation_reason');
            }
            if (Schema::hasColumn('properties', 'deactivated_at')) {
                $table->dropColumn('deactivated_at');
            }
            if (Schema::hasColumn('properties', 'deactivated_by')) {
                $table->dropColumn('deactivated_by');
            }
        });
    }
};
