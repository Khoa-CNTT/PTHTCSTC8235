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
        Schema::create('bang_gias', function (Blueprint $table) {
            $table->id();
            $table->string('id_dv');
            $table->integer('can_nang_min');
            $table->integer('can_nang_max');
            $table->integer('gia_tien');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bang_gias');
    }
};
