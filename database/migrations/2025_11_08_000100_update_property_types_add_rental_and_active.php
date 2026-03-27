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
        Schema::table('property_types', function (Blueprint $table) {
            if (! Schema::hasColumn('property_types', 'rental_type')) {
                $table->string('rental_type', 20)->default('شهري')->after('name');
            }
            if (! Schema::hasColumn('property_types', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('rental_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_types', function (Blueprint $table) {
            if (Schema::hasColumn('property_types', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('property_types', 'rental_type')) {
                $table->dropColumn('rental_type');
            }
        });
    }
};
