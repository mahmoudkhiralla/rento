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
        if (! Schema::hasTable('suspended_users')) {
            Schema::create('suspended_users', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
                $table->string('reason')->nullable();
                $table->string('duration')->nullable(); // week | two_weeks | month | review | permanent
                $table->timestamp('ends_at')->nullable();
                $table->timestamp('released_at')->nullable();
                $table->string('status')->default('suspended'); // suspended | released
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('suspended_users')) {
            Schema::dropIfExists('suspended_users');
        }
    }
};
