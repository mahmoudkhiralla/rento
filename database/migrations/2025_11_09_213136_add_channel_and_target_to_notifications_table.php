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
            $table->string('channel')->nullable()->after('type')->comment('sms, push, email');
            $table->string('target_users')->nullable()->after('channel')->comment('all, tenants, landlords, specific');
            $table->timestamp('sent_at')->nullable()->after('read_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['channel', 'target_users', 'sent_at']);
        });
    }
};
