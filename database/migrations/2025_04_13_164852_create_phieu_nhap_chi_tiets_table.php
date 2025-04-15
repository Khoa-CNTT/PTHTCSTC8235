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
        Schema::create('phieu_nhap_chi_tiets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_phieu_nhap');
            $table->integer('id_thuoc');
            $table->integer('so_luong');
            $table->integer('gia_nhap');
            $table->date('han_su_dung');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phieu_nhap_chi_tiets');
    }
};
