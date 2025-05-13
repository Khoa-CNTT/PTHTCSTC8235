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
            $table->integer('id_lich');
            $table->integer('id_dv');
            $table->integer('id_kh');
            $table->integer('id_pet');
            $table->date('ngay');
            $table->string('gio');
            $table->integer('id_nv')->nullable();
            $table->integer('tinh_trang')->default(0);
            $table->integer('tien_coc')->nullable();
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
