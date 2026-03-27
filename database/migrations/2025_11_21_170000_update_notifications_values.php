<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getSchemaBuilder()->hasTable('notifications')) {
            DB::table('notifications')->where('channel', 'push')->update(['channel' => 'app']);
            DB::table('notifications')->where('type', 'info')->update(['type' => 'booking_confirm']);
            DB::table('notifications')->where('type', 'announcement')->update(['type' => 'alert']);
        }
    }

    public function down(): void
    {
        if (DB::getSchemaBuilder()->hasTable('notifications')) {
            DB::table('notifications')->where('channel', 'app')->update(['channel' => 'push']);
            DB::table('notifications')->where('type', 'booking_confirm')->update(['type' => 'info']);
            DB::table('notifications')->where('type', 'alert')->update(['type' => 'announcement']);
        }
    }
};