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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->string('id_pet');
            $table->string('id_kh');
            $table->string('ten_pet');
            $table->string('chung_loai');
            $table->string('gioi_tinh');
            $table->string('tuoi');
            $table->string('hinh_anh');
            $table->string('can_nang');
            $table->string('tinh_trang');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
