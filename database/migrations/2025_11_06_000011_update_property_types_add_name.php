<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('property_types', function (Blueprint $table) {
            if (! Schema::hasColumn('property_types', 'name')) {
                $table->string('name')->nullable()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('property_types', function (Blueprint $table) {
            try {
                $table->dropColumn('name');
            } catch (\Throwable $e) {
            }
        });
    }
};
