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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->engine = 'InnoDB'; 
            $table->string('task_id')->unique();
            $table->string('name');
            $table->bigInteger('list_id');
            $table->string('status');
            $table->string('priority')->nullable();

            $table->string('empresa')->nullable();
            $table->string('departamento_mkt')->nullable();
            $table->string('planejamento')->nullable();

            $table->timestamp('date_created')->nullable();
            $table->timestamp('date_updated')->nullable();
            $table->timestamps();

            $table->foreign('list_id')->references('list_id')->on('lists')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
