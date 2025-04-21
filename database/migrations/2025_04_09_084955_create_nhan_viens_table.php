<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\password;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nhan_viens', function (Blueprint $table) {
            $table->id();
            $table->string('ten_nv');
            $table->integer('gioi_tinh');
            $table->string('email');
            $table->integer('tien_kham')->nullable();
            $table->string('password');
            $table->string('mo_ta');
            $table->string('hinh_anh');
            $table->integer('tinh_trang')->default(0);
            $table->integer('id_chucvu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nhan_viens');
    }
};
