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
        Schema::create('read_articles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->bigInteger('article_id')->unsigned()->nullable();
            // $table->foreignId('article_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('browser')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('location')->nullable();
            $table->string('mac_address')->nullable();
            $table->date('date')->nullable();
            $table->unsignedInteger('month')->nullable();
            $table->unsignedInteger('year')->nullable();
            $table->string('unique_reader_id')->nullable()->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('read_articles');
    }
};
