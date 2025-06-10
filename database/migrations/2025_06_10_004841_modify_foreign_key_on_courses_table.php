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
    Schema::table('courses', function (Blueprint $table) {
        $table->dropForeign(['miniature']);

        $table->foreign('miniature')
              ->references('id')
              ->on('images')
              ->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('courses', function (Blueprint $table) {
        $table->dropForeign(['miniature']);
        $table->foreign('miniature')
              ->references('id')
              ->on('images');
    });
}

};
