<?php

namespace App\Console\Commands;

use App\Jobs\ProcessClickUpSubTask;
use App\Jobs\ProcessClickUpTasks;
use App\Models\Task;
use App\Models\TaskAssignee;
use App\Models\TaskList;
use App\Services\ClickUpService;
use Illuminate\Console\Command;

class TaskRhLeadTimeCommand extends Command
{

    protected $signature = 'app:task-rh-lead-time';


    protected $description = 'Command description';

    protected $clickUpService;

    public function __construct(ClickUpService $clickUpService)
    {
        parent::__construct();
        $this->clickUpService = $clickUpService;
    }



    public function handle()
    {

        $page = 0;

        do {

            // Buscar as tarefas do ClickUp
            $folder = $this->clickUpService->getTasks("901109379346", $page);


            if (!empty($folder['tasks'])) {
                // Atualizar ou criar a lista de tarefas
                TaskList::updateOrCreate(
                    ['list_id' => "901109379346"],
                    ['name' => $folder['tasks'][0]['list']['name']],
                );
            }


            // Enviar cada tarefa para a fila
            foreach ($folder['tasks'] as $task) {
                ProcessClickUpTasks::dispatch($task);

                $result = $this->clickUpService->getTask($task['id']);

                if (isset($result['subtasks'])) {
                    foreach ($result['subtasks'] as $subtask) {

                        $result2 = $this->clickUpService->getTask($subtask['id']);

                        ProcessClickUpSubTask::dispatch($result2);
                    }
                }
            }

            $page++;
        } while (!empty($folder['tasks']));
    }
}
