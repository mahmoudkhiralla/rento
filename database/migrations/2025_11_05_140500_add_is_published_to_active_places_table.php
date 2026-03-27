<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('active_places', function (Blueprint $table) {
            if (! Schema::hasColumn('active_places', 'is_published')) {
                $table->boolean('is_published')->default(true)->after('image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('active_places', function (Blueprint $table) {
            if (Schema::hasColumn('active_places', 'is_published')) {
                $table->dropColumn('is_published');
            }
        });
    }
};
