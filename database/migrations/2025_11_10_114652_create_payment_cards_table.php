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
        Schema::create('payment_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('card_number')->unique();
            $table->string('card_type')->default('standard');
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('balance', 10, 2)->default(0);
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->enum('status', ['pending', 'active', 'expired', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_cards');
    }
};
