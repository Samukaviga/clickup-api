<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessClickUpSubTask;
use App\Jobs\ProcessClickUpTasks;
use App\Models\Assignee;
use App\Models\Folder;
use App\Models\Space;
use App\Models\SubTask;
use App\Models\Task;
use App\Models\TaskAssignee;
use App\Models\TaskList;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Services\ClickUpService;

class ClickUpController extends Controller
{
    protected $clickUpService;

    public function __construct(ClickUpService $clickUpService)
    {
        $this->clickUpService = $clickUpService;
    }

    public function getWorkspaces()
    {
        $workspaces = $this->clickUpService->getWorkspaces();
        //return response()->json($workspaces);

        dd($workspaces);
    }

    public function getSpaces()
    {
        $spaces = $this->clickUpService->getSpaces("9011827606");

        dd($spaces);
    }

    public function getFolders()
    {

        # space Recursos Humanos: 90113577562

        $folder = $this->clickUpService->getFolders("90113577562"); //Space de tecnologia da informacao

        dd($folder);
    }

    public function getLists()
    {

        #folder : 90115587486

        $folder = $this->clickUpService->getLists("90115587486"); //Folder (PASTA) de tecnologia da informacao

        dd($folder);
    }

    public function getTasks()
    {

        # Lista Marketing Liceu: 901108220827
        # Lista Colegio Itaqua: 901108288683  
        # Lista Estação Futura: 901108291057
        # Lista Fisk: 901108303682

        # Lista RH - lead time: 901109379346
        # Estratégico e Tático: 901109890304

        $folder = $this->clickUpService->getTasks("901109379346");


        dd($folder);
    }

    public function getTask()
    {

        # recepcionisa = 868cnag2w
        $folder = $this->clickUpService->getTask('868d3bppn');

        dd($folder);
    }

    public function createMembers()
    {

        $workspaces = $this->clickUpService->getWorkspaces();

        #dd($workspaces['teams'][0]['members']);
        $members = $workspaces['teams'][0]['members'];

        foreach ($members as $member) {

            $assignee_id = $member['user']['id'];
            $username = $member['user']['username'];
            $email = $member['user']['email'];
            $profile_picture = $member['user']['profilePicture'];


            Assignee::updateOrCreate(
                ['assignee_id' => $assignee_id],
                [
                    'username' => $username ?? null,
                    'email' => $email ?? null,
                    'profile_picture' => $profile_picture ?? null,
                ]
            );
        }
    }

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

            if ($field['name'] === 'Delegado para' && isset($field['value'])) {
                foreach ($field['type_config']['options'] as $option) {

                    if ($option['orderindex'] == $field['value']) {
                        $result['delegado_para'] = $option['name'];
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
                        $result['fase_lead_time'] = $option['name'];
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
        }

        return $result;
    }


    public function teste()
    {
        // Buscar as tarefas do ClickUp
        $folder = $this->clickUpService->getTasks("901109379346");

        // Atualizar ou criar a lista de tarefas
        TaskList::updateOrCreate(
            ['list_id' => "901109379346"],
            ['name' => "RH - Lead Time"]
        );

        // Enviar cada tarefa para a fila
        foreach ($folder['tasks'] as $task) {
            # ProcessClickUpTasks::dispatch($task);
            $result = $this->clickUpService->getTask($task['id']);



            $custom = $this->processCustomFields($result['custom_fields']);

            dd($custom);

            foreach ($result['subtasks'] as $subtask) {

                $result2 = $this->clickUpService->getTask($subtask['id']);

                ProcessClickUpSubTask::dispatch($result2);

                dd("deu certo");
            }
        }

        return response()->json(['message' => 'Tarefas enviadas para processamento!']);
    }

    public function teste1()
    {

        set_time_limit(300); // Aumenta o tempo de execução para evitar erros


        $folder = $this->clickUpService->getTasks("901109379346");


        TaskList::updateOrCreate(
            ['list_id' => "901109379346"],
            ['name' => "RH - Lead Time"]
        );


        foreach ($folder['tasks'] as $task) {


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
                    'empresa' => $customFieldsTask['empresa'] ?? null,
                    'departamento_mkt' => $customFieldsTask['departamento_mkt'] ?? null,
                    'planejamento' => $customFieldsTask['planejamento'] ?? null,
                    'cad' => $customFieldsTask['cad'] ?? null,
                    'cargo' => $customFieldsTask['cargo'] ?? null,
                    'comparecimento' => $customFieldsTask['comparecimento'] ?? null,
                    'fases_lead_time' => $customFieldsTask['fases_lead_time'] ?? null,
                    'mes' => $customFieldsTask['mes'] ?? null,
                    'unidade' => $customFieldsTask['unidade'] ?? null,
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

            # SUBTASKS

            $subtask = $this->clickUpService->getTask($task['id']);

            foreach ($subtask['subtasks'] as $item) {

                $result = $this->clickUpService->getTask($item['id']);

                $customFieldsSubTask = $this->processCustomFields($result['custom_fields']);

                $subtaskModel = SubTask::updateOrCreate(
                    ['task_id' => $result['id'], 'parent' => $result['parent'] ?? null],
                    [
                        'name' => $result['name'],
                        'list_id' => $result['list']['id'],
                        'status' => $result['status']['status'],
                        'priority' => $result['priority']['priority'] ?? null,
                        'date_created' => date('Y-m-d H:i:s', $result['date_created'] / 1000),
                        'date_updated' => date('Y-m-d H:i:s', $result['date_updated'] / 1000),
                        'start_date' => isset($result['start_date']) ? date('Y-m-d H:i:s', $result['start_date'] / 1000) : null,
                        'due_date' => isset($result['due_date']) ? date('Y-m-d H:i:s', $result['due_date'] / 1000) : null,
                        'parent' => $result['parent'] ?? null,
                        'time_estimate' => $result['time_estimate'] ?? null,
                        'empresa' => $customFieldsSubTask['empresa'] ?? null,
                        'departamento_mkt' => $customFieldsSubTask['departamento_mkt'] ?? null,
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
                $taskModel->assignees()->delete();

                // 7️⃣ Inserir novos assignees
                foreach ($task['assignees'] as $assignee) {
                    TaskAssignee::create([
                        'task_id' => $subtaskModel->task_id,
                        'assignee_id' => $assignee['id'],
                        'assignee_name' => $assignee['username'],
                    ]);
                }
            }
        }
    }




    /*
    public function createTask()
    {
        $listId = "SUA_LIST_ID";
        $data = [
            "name" => "Nova Tarefa",
            "description" => "Criada via API Laravel",
            "status" => "to do",
            "priority" => 2,
        ];

        $task = $this->clickUpService->createTask($listId, $data);
        return response()->json($task);
    } */
}
