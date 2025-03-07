<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\TaskAssignee;
use App\Models\TaskList;
use App\Services\ClickUpService;
use Illuminate\Console\Command;

class TasksMarketingLiceuCommand extends Command
{

    protected $signature = 'app:created-tasks-marketing-liceu-command';


    protected $description = 'Tasks of List Marketing Liceu';

    protected $clickUpService;

    public function __construct(ClickUpService $clickUpService)
    {
        parent::__construct();
        $this->clickUpService = $clickUpService;
    }


    public function handle()
    {
        $folder = $this->clickUpService->getTasks("901108220827");


        TaskList::updateOrCreate(
            ['list_id' => "901108220827"],
            ['name' => "Marketing Liceu"]
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
                    'list_id' => "901108220827",
                    'status' => $task['status']['status'],
                    'priority' => $task['priority']['priority'] ?? null,
                    'date_created' => date('Y-m-d H:i:s', $task['date_created'] / 1000),
                    'date_updated' => date('Y-m-d H:i:s', $task['date_updated'] / 1000),
                    'empresa' => $empresaSelecionada ?? null,
                    'departamento_mkt' => $departamentoSelecionado ?? null,
                    'planejamento' => $planejamentoSelecionado ?? null,
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
    
    }
}
