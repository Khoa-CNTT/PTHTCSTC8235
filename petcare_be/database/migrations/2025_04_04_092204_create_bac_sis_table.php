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
        Schema::create('bac_sis', function (Blueprint $table) {
            $table->id();
            $table->string('ten_bs');
            $table->integer('gioi_tinh');
            $table->string('email');
            $table->integer('password');
            $table->string('mo_ta');
            $table->integer('hinh_anh');
            $table->string('tinh_trang');
            $table->integer('id_chucvu');
            $table->string('chuyen_khoa');
            $table->integer('tien_kham');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bac_sis');
    }
};
