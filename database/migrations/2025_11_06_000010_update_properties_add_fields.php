<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            if (! Schema::hasColumn('properties', 'address')) {
                $table->string('address')->nullable()->after('city');
            }
            if (! Schema::hasColumn('properties', 'rental_type')) {
                $table->string('rental_type')->nullable()->after('address');
            }
            if (! Schema::hasColumn('properties', 'capacity')) {
                $table->unsignedSmallInteger('capacity')->nullable()->after('rental_type');
            }
            if (! Schema::hasColumn('properties', 'bedrooms')) {
                $table->unsignedTinyInteger('bedrooms')->nullable()->after('capacity');
            }
            if (! Schema::hasColumn('properties', 'bathrooms')) {
                $table->unsignedTinyInteger('bathrooms')->nullable()->after('bedrooms');
            }
            if (! Schema::hasColumn('properties', 'image')) {
                $table->string('image')->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            // محاولة التراجع، قد تفشل إن كانت الأعمدة موجودة مسبقاً قبل هذه الهجرة
            try {
                $table->dropColumn(['address', 'rental_type', 'capacity', 'bedrooms', 'bathrooms', 'image']);
            } catch (\Throwable $e) {
            }
        });
    }
};
