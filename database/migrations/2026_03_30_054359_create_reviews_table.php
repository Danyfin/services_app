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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('rating')->unsigned()->comment('Оценка от 1 до 5');
            $table->text('comment')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('Автор отзыва (заказчик)');
            $table->foreignId('executor_id')->constrained('users')->onDelete('cascade')->comment('Исполнитель, которому оставлен отзыв');
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique('order_id');
            $table->index('executor_id');
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
