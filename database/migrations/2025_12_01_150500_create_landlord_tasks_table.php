<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landlord_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->string('title');
            $table->enum('type', [
                'tenant_request',
                'unit_maintenance',
                'tenant_complaint',
                'unit_cleaning',
            ]);
            $table->dateTime('scheduled_at')->nullable();
            $table->text('message')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'property_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landlord_tasks');
    }
};

