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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('avatar')->nullable();

            // User type: tenant, landlord
            $table->enum('user_type', ['tenant', 'landlord', 'both'])->default('tenant');

            // Verification fields
            $table->boolean('id_verified')->default(false);
            $table->boolean('face_verified')->default(false);
            $table->boolean('is_influencer')->default(false);
            $table->boolean('needs_renewal')->default(false);

            // Status: active, suspended, banned
            $table->enum('status', ['active', 'suspended', 'banned'])->default('active');

            // Additional info
            $table->string('job')->nullable();
            $table->string('city')->nullable();
            $table->boolean('has_pet')->default(false);

            // Rating
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('reviews_count')->default(0);

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
