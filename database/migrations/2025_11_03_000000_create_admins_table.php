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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            // كلمة المرور تُحفظ بشكل مشفر فقط، ولا يتم تخزين تأكيد كلمة المرور في قاعدة البيانات
            $table->string('password');
            $table->string('phone', 20)->nullable();
            $table->string('image')->nullable(); // مسار الصورة أو اسم الملف
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
