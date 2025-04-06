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
        Schema::create('hoa_don_nhap_chi_tiets', function (Blueprint $table) {
            $table->id();
            $table->string('id_hdnhap');
            $table->string('id_ncc');
            $table->integer('so_luong_nhap');
            $table->integer('gia_nhap');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hoa_don_nhap_chi_tiets');
    }
};
