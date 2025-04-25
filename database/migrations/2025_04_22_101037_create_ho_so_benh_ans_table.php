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
        Schema::create('ho_so_benh_ans', function (Blueprint $table) {
            $table->id();
            $table->integer("id_nv");
            $table->date("ngay_kham");
            $table->string("chuan_doan")->nullable();
            $table->integer("id_pet");
            $table->integer('tinh_trang')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ho_so_benh_ans');
    }
};
