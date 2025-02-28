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
        Schema::create('task_assignees', function (Blueprint $table) {
            $table->id();
            $table->string('task_id');
            $table->bigInteger('assignee_id'); // ID do usuário no ClickUp
            $table->string('assignee_name')->nullable(); // Nome do usuário (opcional)
            $table->timestamps();

            // Definir chave estrangeira
            $table->foreign('task_id')->references('task_id')->on('tasks')->cascadeOnDelete();
         });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_assignees');
    }
};
