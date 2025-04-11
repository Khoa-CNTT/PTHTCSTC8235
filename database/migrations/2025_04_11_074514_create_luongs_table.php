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
        Schema::create('luongs', function (Blueprint $table) {
            
            $table->id();
            $table->string('id_luong');
            $table->string('id_nv');
            $table->integer('tien_luong');
            $table->integer('ngay_thanh_toan');
            $table->integer('tinh_trang')->default(0);
            $table->integer('tien_thuong');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('luongs');
    }
};
