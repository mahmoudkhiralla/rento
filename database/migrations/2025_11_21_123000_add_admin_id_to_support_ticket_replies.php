<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_ticket_replies', function (Blueprint $table) {
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('support_ticket_replies', function (Blueprint $table) {
            $table->dropConstrainedForeignId('admin_id');
        });
    }
};