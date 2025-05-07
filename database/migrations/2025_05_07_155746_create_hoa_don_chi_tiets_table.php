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
        Schema::create('hoa_don_chi_tiets', function (Blueprint $table) {
            $table->id();
            $table->integer('id_hoadon');
            $table->integer('id_ct_don_thuoc');
            $table->integer('id_nv');
            $table->integer('id_dvct');
            $table->date('ngay_tt');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hoa_don_chi_tiets');
    }
};
