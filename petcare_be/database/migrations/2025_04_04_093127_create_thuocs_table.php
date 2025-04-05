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
        Schema::create('thuocs', function (Blueprint $table) {
            $table->id();
            $table->string('ten_thuoc');
            $table->string('don_vi');
            $table->integer('don_gia');
            $table->string('mo_ta');
            $table->date('han_su_dung');
            $table->integer('tinh_trang');
            $table->string('id_kho');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thuocs');
    }
};
