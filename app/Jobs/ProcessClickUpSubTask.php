<?php

namespace App\Jobs;

use App\Models\SubTask;
use App\Models\TaskAssignee;
use App\Services\ClickUpService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessClickUpSubTask implements ShouldQueue
{
    use Queueable;
    protected $task;
    protected $clickUpService;

    public function __construct($task)
    {
        $this->task = $task;
    }

    public function handle(ClickUpService $clickUpService): void
    {
        $customFieldsSubTask = $this->processCustomFields($this->task['custom_fields']);

        $subtaskModel = SubTask::updateOrCreate(
            ['task_id' => $this->task['id'], 'parent' => $this->task['parent'] ?? null],
            [
                'name' => $this->task['name'],
                'list_id' => $this->task['list']['id'],
                'status' => $this->task['status']['status'],
                'priority' => $this->task['priority']['priority'] ?? null,
                'date_created' => date('Y-m-d H:i:s', $this->task['date_created'] / 1000),
                'date_updated' => date('Y-m-d H:i:s', $this->task['date_updated'] / 1000),
                'start_date' => isset($this->task['start_date']) ? date('Y-m-d H:i:s', $this->task['start_date'] / 1000) : null,
                'due_date' => isset($this->task['due_date']) ? date('Y-m-d H:i:s', $this->task['due_date'] / 1000) : null,
                'parent' => $this->task['parent'] ?? null,
                'time_estimate' => isset($this->task['time_estimate']) ? date('Y-m-d H:i:s', $this->task['time_estimate'] / 1000) : null,
                'empresa' => $customFieldsSubTask['empresa'] ?? null,
                'departamento_mkt' => $customFieldsSubTask['departamento_mkt'] ?? null,
                'departamento' => $customFieldsSubTask['departamento'] ?? null,
                'planejamento' => $customFieldsSubTask['planejamento'] ?? null,
                'cad' => $customFieldsSubTask['cad'] ?? null,
                'cargo' => $customFieldsSubTask['cargo'] ?? null,
                'comparecimento' => $customFieldsSubTask['comparecimento'] ?? null,
                'fases_lead_time' => $customFieldsSubTask['fases_lead_time'] ?? null,
                'mes' => $customFieldsSubTask['mes'] ?? null,
                'unidade' => $customFieldsSubTask['unidade'] ?? null,
            ]
        );

        // 6️⃣ Remover antigos assignees para evitar duplicação
        $subtaskModel->assignees()->delete();

        // 7️⃣ Inserir novos assignees
        foreach ($this->task['assignees'] as $assignee) {
            TaskAssignee::create([
                'task_id' => $subtaskModel->task_id,
                'assignee_id' => $assignee['id'],
                'assignee_name' => $assignee['username'],
            ]);
        }
    }


    /*
    private function processCustomFields($customFields)
    {
        $result = [
            'cad' => null,
            'cargo' => null,
            'comparecimento' => null,
            'fase_lead_time' => null,
            'mes' => null,
            'unidade' => null,
            'empresa' => null,
            'departamento_mkt' => null,
            'planejamento' => null,
            'delegado_para' => null,
        ];

        foreach ($customFields as $field) {
            $fieldName = strtolower(str_replace([' ', '.'], '_', $field['name']));

            if (isset($field['value'])) {
                if (isset($field['type_config']['options'])) {
                    foreach ($field['type_config']['options'] as $option) {
                        if ($option['orderindex'] == $field['value']) {
                            $result[$fieldName] = $option['name'];
                            break;
                        }
                    }
                } else {
                    $result[$fieldName] = $field['value'];
                }
            }
        }

        return $result;
    } */

    private function processCustomFields($customFields)
    {
        $result = [
            'cad' => null,
            'cargo' => null,
            'comparecimento' => null,
            'fases_lead_time' => null,
            'mes' => null,
            'unidade' => null,
            'empresa' => null,
            'departamento_mkt' => null,
            'departamento' => null,
            'planejamento' => null,
            'delegado_para' => null,
            'tipo_de_solicitacao' => null,
            'tipo' => null,
            'quantidade' => null,
        ];


        foreach ($customFields as $field) {

            if ($field['name'] === 'Delegado para' && isset($field['value'])) {
                foreach ($field['type_config']['options'] as $option) {

                    if ($option['orderindex'] == $field['value']) {
                        $result['delegado_para'] = $option['name'];
                        break;
                    }
                }
            }

            if ($field['name'] === 'Departamento' && isset($field['value'])) {
                foreach ($field['type_config']['options'] as $option) {

                    if ($option['orderindex'] == $field['value']) {
                        $result['departamento'] = $option['name'];
                        break;
                    }
                }
            }

            if ($field['name'] === 'Unidade' && isset($field['value'])) {
                foreach ($field['type_config']['options'] as $option) {

                    if ($option['orderindex'] == $field['value']) {
                        $result['unidade'] = $option['name'];
                        break;
                    }
                }
            }

            if ($field['name'] === 'Mês' && isset($field['value'])) {
                foreach ($field['type_config']['options'] as $option) {

                    if ($option['orderindex'] == $field['value']) {
                        $result['mes'] = $option['name'];
                        break;
                    }
                }
            }

            if ($field['name'] === 'Fases Lead Time' && isset($field['value'])) {
                foreach ($field['type_config']['options'] as $option) {

                    if ($option['orderindex'] == $field['value']) {
                        $result['fases_lead_time'] = $option['name'];
                        break;
                    }
                }
            }

            if ($field['name'] === 'Comparecimento' && isset($field['value'])) {
                foreach ($field['type_config']['options'] as $option) {

                    if ($option['orderindex'] == $field['value']) {
                        $result['comparecimento'] = $option['name'];
                        break;
                    }
                }
            }

            if ($field['name'] === 'Cargo' && isset($field['value'])) {
                foreach ($field['type_config']['options'] as $option) {

                    if ($option['orderindex'] == $field['value']) {
                        $result['cargo'] = $option['name'];
                        break;
                    }
                }
            }

            if ($field['name'] === 'CAD' && isset($field['value'])) {

                $result['cad'] = $field['value'] ?? null;
            }

            if ($field['name'] === 'Empresa' && isset($field['value'])) {
                foreach ($field['type_config']['options'] as $option) {

                    if ($option['orderindex'] == $field['value']) {
                        $result['empresa'] = $option['name'];
                        break;
                    }
                }
            }

            if ($field['name'] === 'Depto. MKT' && isset($field['value'])) {

                foreach ($field['type_config']['options'] as $option) {
                    if ($option['orderindex'] === $field['value']) {
                        $result['departamento_mkt'] = $option['name'];
                        break;
                    }
                }
            }

            if ($field['name'] === 'Planejamento' && isset($field['value'])) {
                foreach ($field['type_config']['options'] as $option) {
                    if ($option['orderindex'] === $field['value']) {
                        $result['planejamento'] = $option['name'];
                        break;
                    }
                }
            }

            # Compras
            if ($field['name'] === 'Tipo de Solicitação' && isset($field['value'])) {
                foreach ($field['type_config']['options'] as $option) {
                    if ($option['orderindex'] === $field['value']) {
                        $result['tipo_de_solicitacao'] = $option['name'];
                        break;
                    }
                }
            }

            if ($field['name'] === 'Tipo' && isset($field['value'])) {
                foreach ($field['type_config']['options'] as $option) {
                    if ($option['orderindex'] === $field['value']) {
                        $result['tipo'] = $option['name'];
                        break;
                    }
                }
            }

            if ($field['name'] === 'Quantidade' && isset($field['value'])) {
                
                     $result['quantidade'] = $field['value'] ?? null;
              
            }

           
        }

        return $result;
    }
}
