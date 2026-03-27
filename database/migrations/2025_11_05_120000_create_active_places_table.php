<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('active_places', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('user_type', ['landlord', 'tenant']);
            $table->string('name');
            $table->string('city');
            $table->string('area')->nullable();
            $table->date('available_from')->nullable();
            $table->string('booking_type')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('price_unit');
            $table->decimal('rating', 3, 1)->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('active_places');
    }
};
