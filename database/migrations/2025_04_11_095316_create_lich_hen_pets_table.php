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
        Schema::create('lich_hen_pets', function (Blueprint $table) {
            $table->id();
            $table->string('id_lich');
            $table->string('id_kh');
            $table->string('id_dv');
            $table->string('id_pet');
            $table->date('ngay');
            $table->string('gio');
            $table->string('id_nv')->nullable();
            $table->string('tinh_trang')->default(0);
            $table->string('tien_coc')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lich_hen_pets');
    }
};
