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
            $table->string('name')->nullable();
            $table->bigInteger('list_id')->nullable();
            $table->string('status')->nullable();
            $table->string('priority')->nullable();

            $table->string('empresa')->nullable();
            $table->string('departamento_mkt')->nullable();
            $table->string('departamento')->nullable();
            $table->string('planejamento')->nullable();
            
            $table->string('cad')->nullable();
            $table->string('cargo')->nullable();
            $table->string('comparecimento')->nullable();
            $table->string('fases_lead_time')->nullable();
            $table->string('mes')->nullable();
            $table->string('unidade')->nullable();
            $table->string('delegado_para')->nullable();

            $table->string('time_estimate')->nullable();

            $table->string('start_date')->nullable();
            $table->string('due_date')->nullable();

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
