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
        Schema::create('thuoc_khos', function (Blueprint $table) {
            $table->id();
            $table->integer('id_kho');
            $table->integer('id_thuoc');
            $table->decimal('gia_nhap', 15, 2);
            $table->integer('so_luong_ton_kho');
            $table->date('han_su_dung');
            $table->decimal('gia_ban',15,2);
            $table->integer('id_phieu_nhap_CT')->nullable();
            $table->integer('tinh_trang')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thuoc_khos');
    }
};
