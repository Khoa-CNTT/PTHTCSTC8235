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
        Schema::create('khach_hangs', function (Blueprint $table) {
            $table->id();
            $table->string('ho_va_ten');
            $table->string("email");
            $table->string("password");
            $table->string("so_dien_thoai");
            $table->date("ngay_sinh");
            $table->integer("is_active")->default(0);
            $table->integer("is_block")->default(0);
            $table->string("hash_active")->default(0);
            $table->string("hash_reset")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('khach_hangs');
    }
};
