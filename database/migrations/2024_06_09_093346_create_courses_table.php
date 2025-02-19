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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('miniature')->nullable();
            //$table->unsignedBigInteger('video_id');
            $table->json('catArr');
            $table->boolean('isPrivate')->default(false);
            $table->string('password')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('miniature')->references('id')->on('images');
            //$table->foreign('video_id')->references('id')->on('videos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
