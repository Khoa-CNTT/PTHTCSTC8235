<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Bảng lưu trữ tương tác
        Schema::create('chatbot_interactions', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('response');
            $table->json('context')->nullable();
            $table->float('success_rate')->default(1);
            $table->integer('usage_count')->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('user_id')->nullable();
            $table->timestamps();
        });

        // Bảng lưu trữ phản hồi từ khách hàng
        Schema::create('chatbot_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interaction_id')->constrained('chatbot_interactions');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->boolean('is_helpful');
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        // Bảng lưu trữ từ khóa và mẫu câu
        Schema::create('chatbot_patterns', function (Blueprint $table) {
            $table->id();
            $table->string('pattern');
            $table->string('intent');
            $table->json('entities')->nullable();
            $table->float('confidence')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chatbot_feedbacks');
        Schema::dropIfExists('chatbot_interactions');
        Schema::dropIfExists('chatbot_patterns');
    }
}; 