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
        Schema::create('dich_vus', function (Blueprint $table) {
            $table->id();
            $table->  string('ten_dv');
            $table->  integer('id_loaidv');
            $table->  string('mo_ta');
            $table->  integer('gia');
            $table->  string('hinh_anh');
            $table->  float('can_nang_min')->nullable();
            $table->  float('can_nang_max')->nullable();
            $table->  integer('phan_loai_kg')->default(0);
            $table->  integer('tinh_trang')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dich_vus');
    }
};
