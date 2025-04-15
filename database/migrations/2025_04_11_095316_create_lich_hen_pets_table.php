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
            $table->string('id_pet');
            $table->datetimes('ngay_gio_hen');
            $table->string('id_nv');
            $table->string('tinh_trang');
            $table->string('tien_coc');
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
