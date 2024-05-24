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
        Schema::create('selected_articles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('article_id'); // Ensure this is an unsignedBigInteger
            $table->date('date');
            $table->timestamps();

            // Manually specify the foreign key constraint
            $table->foreign('article_id')->references('id')->on('articles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('selected_articles');
    }
};
