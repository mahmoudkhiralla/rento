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
        Schema::table('notifications', function (Blueprint $table) {
            if (! Schema::hasColumn('notifications', 'user_id')) {
                $table->unsignedBigInteger('user_id')->index();
            }
            if (! Schema::hasColumn('notifications', 'title')) {
                $table->string('title')->nullable();
            }
            if (! Schema::hasColumn('notifications', 'message')) {
                $table->text('message')->nullable();
            }
            if (! Schema::hasColumn('notifications', 'type')) {
                $table->string('type')->nullable()->index();
            }
            if (! Schema::hasColumn('notifications', 'meta')) {
                $table->json('meta')->nullable();
            }
            if (! Schema::hasColumn('notifications', 'read_at')) {
                $table->timestamp('read_at')->nullable()->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'read_at')) {
                $table->dropColumn('read_at');
            }
            if (Schema::hasColumn('notifications', 'meta')) {
                $table->dropColumn('meta');
            }
            if (Schema::hasColumn('notifications', 'type')) {
                $table->dropColumn('type');
            }
            if (Schema::hasColumn('notifications', 'message')) {
                $table->dropColumn('message');
            }
            if (Schema::hasColumn('notifications', 'title')) {
                $table->dropColumn('title');
            }
            if (Schema::hasColumn('notifications', 'user_id')) {
                $table->dropIndex(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};
