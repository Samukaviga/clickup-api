<?php

namespace App\Http\Controllers;

use App\Models\Assignee;
use App\Models\Folder;
use App\Models\Space;
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

        $folder = $this->clickUpService->getTasks("901109379346");

       # $dd($folder['tasks'][1]['dependencies']);

        $tasksId = $folder['tasks'][1]['dependencies'][0]['task_id'];
        
       # dd($tasksId);
       
       # 868cnag4b

        $result = $this->clickUpService->getTask("868cnag4b");

        dd($result);

    }

    public function createMembers()
    {

        $workspaces = $this->clickUpService->getWorkspaces();

        #dd($workspaces['teams'][0]['members']);
        $members = $workspaces['teams'][0]['members'];

        foreach($members as $member){

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

    public function teste()
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
