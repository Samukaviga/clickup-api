<?php

namespace App\Console\Commands;

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
        
            $folder = $this->clickUpService->getTasks("901109379346");
    
    
            TaskList::updateOrCreate(
                ['list_id' => "901109379346"],
                ['name' => "RH - Lead Time"]
            );
    
    
            foreach ($folder['tasks'] as $task) { 
    
    
                $cadSelecionado = null;
                $cargoSelecionado = null;
                $comparecimentoSelecionado = null;
                $faseLeadTimeSelecionado = null;
                $mesSelecionado = null;
                $unidadeSelecionado = null;
                
                $empresaSelecionada = null;
                $departamentoSelecionado = null;
                $planejamentoSelecionado = null;
    
                foreach ($task['custom_fields'] as $field) { 
    
                    if ($field['name'] === 'Unidade' && isset($field['value'])) {
                        foreach ($field['type_config']['options'] as $option) {         
    
                            if ($option['orderindex'] == $field['value']) {
                                $unidadeSelecionado = $option['name'];
                                break;
                            }
                        }
                    }
    
                    if ($field['name'] === 'Mês' && isset($field['value'])) {
                        foreach ($field['type_config']['options'] as $option) {         
    
                            if ($option['orderindex'] == $field['value']) {
                                $mesSelecionado = $option['name'];
                                break;
                            }
                        }
                    }
    
                    if ($field['name'] === 'Fases Lead Time' && isset($field['value'])) {
                        foreach ($field['type_config']['options'] as $option) {         
    
                            if ($option['orderindex'] == $field['value']) {
                                $faseLeadTimeSelecionado = $option['name'];
                                break;
                            }
                        }
                    }
    
                    if ($field['name'] === 'Comparecimento' && isset($field['value'])) {
                        foreach ($field['type_config']['options'] as $option) {         
    
                            if ($option['orderindex'] == $field['value']) {
                                $comparecimentoSelecionado = $option['name'];
                                break;
                            }
                        }
                    }
    
                    if ($field['name'] === 'Cargo' && isset($field['value'])) {
                        foreach ($field['type_config']['options'] as $option) {         
    
                            if ($option['orderindex'] == $field['value']) {
                                $cargoSelecionado = $option['name'];
                                break;
                            }
                        }
                    }
    
                    if ($field['name'] === 'CAD' && isset($field['value'])) {
    
    
                        $valueArray = str_split($field['value'], 3); // divide a cada 3 caracteres
                        $valueFormatado = implode(',', $valueArray);
                        
                        $cadSelecionado = $valueFormatado ?? null;
    
                    }
    
                    if ($field['name'] === 'Empresa' && isset($field['value'])) {
                        foreach ($field['type_config']['options'] as $option) {         
    
                            if ($option['orderindex'] == $field['value']) {
                                $empresaSelecionada = $option['name'];
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
    
    
                $taskModel = Task::updateOrCreate(
                    ['task_id' => $task['id']],
                    [
                        'name' => $task['name'],
                        'list_id' => "901109379346",
                        'status' => $task['status']['status'],
                        'priority' => $task['priority']['priority'] ?? null,
                        'date_created' => date('Y-m-d H:i:s', $task['date_created'] / 1000),
                        'date_updated' => date('Y-m-d H:i:s', $task['date_updated'] / 1000),
                        'start_date' => isset($task['start_date']) ? date('Y-m-d H:i:s', $task['start_date'] / 1000) : null,
                        'due_date' => isset($task['due_date']) ? date('Y-m-d H:i:s', $task['due_date'] / 1000) : null,
                        'empresa' => $empresaSelecionada ?? null,
                        'departamento_mkt' => $departamentoSelecionado ?? null,
                        'planejamento' => $planejamentoSelecionado ?? null,
                        'cad' => $cadSelecionado ?? null,
                        'cargo' => $cargoSelecionado ?? null,
                        'comparecimento' => $comparecimentoSelecionado ?? null,
                        'fases_lead_time' => $faseLeadTimeSelecionado ?? null,
                        'mes' => $mesSelecionado ?? null,
                        'unidade' => $unidadeSelecionado ?? null,
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
