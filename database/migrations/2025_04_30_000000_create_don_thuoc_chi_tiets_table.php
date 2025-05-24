<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('don_thuoc_chi_tiets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_thuoc');
            $table->unsignedBigInteger('id_don_thuoc');
            $table->integer('so_luong');
            $table->string('lieu_luong')->nullable();
            $table->integer('tinh_trang')->default(0);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('don_thuoc_chi_tiets');
    }
};