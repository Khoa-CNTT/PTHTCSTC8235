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
        Schema::create('phieu_nhaps', function (Blueprint $table) {
            $table->id();
            $table->integer('id_kho');
            $table->integer('id_ncc');
            $table->date('ngay_nhap');
            $table->integer('tinh_trang')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phieu_nhaps');
    }
};
