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
        Schema::create('luongs', function (Blueprint $table) {
            $table->id();
            $table->string('ten_kho');
            $table->string('dia_chi');
            $table->integer('trang_thai');
            $table->integer('so_luong_ton_khokho');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('luongs');
    }
};
