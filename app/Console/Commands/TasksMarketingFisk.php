<?php

namespace App\Console\Commands;

use App\Jobs\ProcessClickUpSubTask;
use App\Jobs\ProcessClickUpTasks;
use App\Models\Task;
use App\Models\TaskAssignee;
use App\Models\TaskList;
use App\Services\ClickUpService;
use Illuminate\Console\Command;

class TasksMarketingFisk extends Command
{
    protected $signature = 'app:tasks-marketing-fisk';


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

            $folder = $this->clickUpService->getTasks("901108303682", $page);

            if (!empty($folder['tasks'])) {
                TaskList::updateOrCreate(
                    ['list_id' => "901108303682"],
                    ['name' => $folder['tasks'][0]['list']['name']],
                );
            }

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

    /*
    public function handle()
    {
        $folder = $this->clickUpService->getTasks("901108303682");


        TaskList::updateOrCreate(
            ['list_id' => "901108303682"],
            ['name' => "Marketing Fisk"]
        );


        foreach ($folder['tasks'] as $task) { 

           

            $empresaSelecionada = null;
            $departamentoSelecionado = null;
            $planejamentoSelecionado = null;

            foreach ($task['custom_fields'] as $field) { 

             

                if ($field['name'] === 'Empresa' && isset($field['value'])) {
                    foreach ($field['type_config']['options'] as $option) {
                        
                        //dd($option['orderindex']);
                        //dd($field);
                        //dd($option['id']);

                        if ($option['orderindex'] == $field['value']) {
                            $empresaSelecionada = $option['name'];
                            //dd($empresaSelecionada);
                            break;
                        }
                    }
                }
                
                if ($field['name'] === 'Depto. MKT' && isset($field['value'])) {
                    
                    
                    foreach ($field['type_config']['options'] as $option) {
                        if ($option['orderindex'] === $field['value']) {
                            $departamentoSelecionado = $option['name'];
                           break;
                        }
                    }
                }

                if ($field['name'] === 'Planejamento' && isset($field['value'])) {
                    foreach ($field['type_config']['options'] as $option) {
                        if ($option['orderindex'] === $field['value']) {
                            $planejamentoSelecionado = $option['name'];
                            break;
                        }
                    }
                }
            }

            //dd($empresaSelecionada);

            //dd($task['priority']['priority']);

            $taskModel = Task::updateOrCreate(
                ['task_id' => $task['id']],
                [
                    'name' => $task['name'],
                    'list_id' => "901108303682",
                    'status' => $task['status']['status'],
                    'priority' => $task['priority']['priority'] ?? null,
                    'date_created' => date('Y-m-d H:i:s', $task['date_created'] / 1000),
                    'date_updated' => date('Y-m-d H:i:s', $task['date_updated'] / 1000),
                    'empresa' => $empresaSelecionada ?? null,
                    'departamento_mkt' => $departamentoSelecionado ?? null,
                    'planejamento' => $planejamentoSelecionado ?? null,
                    'start_date' => isset($task['start_date']) ? date('Y-m-d H:i:s', $task['start_date'] / 1000) : null,
                    'due_date' => isset($task['due_date']) ? date('Y-m-d H:i:s', $task['due_date'] / 1000) : null,
                ]
            );

            // 6️⃣ Remover antigos assignees para evitar duplicação
            $taskModel->assignees()->delete();

            // 7️⃣ Inserir novos assignees
            foreach ($task['assignees'] as $assignee) {
                TaskAssignee::create([
                    'task_id' => $taskModel->task_id,
                    'assignee_id' => $assignee['id'],
                    'assignee_name' => $assignee['username'],
                ]);
            }
        }
    
    }*/
}
