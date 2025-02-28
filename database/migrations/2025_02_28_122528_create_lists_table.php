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
        Schema::create('lists', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('list_id')->unique();
            $table->string('name');
            $table->bigInteger('space_id')->nullable();
            $table->bigInteger('folder_id')->nullable();
            $table->timestamps();

            $table->foreign('space_id')->references('space_id')->on('spaces')->cascadeOnDelete();
            $table->foreign('folder_id')->references('folder_id')->on('folders')->cascadeOnDelete();
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lists');
    }
};
