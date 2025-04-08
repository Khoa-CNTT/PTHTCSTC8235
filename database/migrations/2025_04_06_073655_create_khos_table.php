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
        Schema::create('khos', function (Blueprint $table) {
            $table->id();
            $table->string("ten_kho");
            $table->string('dia_chi');
            $table->integer('tinh_trang');
            $table->integer('so_luon_ton_kho');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('khos');
    }
};
