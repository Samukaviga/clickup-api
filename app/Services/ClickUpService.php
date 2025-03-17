<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ClickUpService
{
    protected $apiUrl = "https://api.clickup.com/api/v2";
    protected $token;

    public function __construct()
    {
        $this->token = env('CLICKUP_API_TOKEN'); // Adicione isso no .env
    }

    // Buscar Workspaces
    public function getWorkspaces()
    {
        $response = Http::withHeaders([
            'Authorization' => $this->token,
        ])->get("$this->apiUrl/team");

        return $response->json();
    }

    //buscar spaces
    public function getSpaces($teamId)
    {
        $response = Http::withHeaders([
            'Authorization' => env('CLICKUP_API_TOKEN'),
        ])->get("https://api.clickup.com/api/v2/team/$teamId/space");

        return $response->json();
    }

    //bucar folders (PASTAS)
    public function getFolders($spaceId)
    {
        $response = Http::withHeaders([
            'Authorization' => env('CLICKUP_API_TOKEN'),
        ])->get("https://api.clickup.com/api/v2/space/$spaceId/folder");

        return $response->json();
    }


    // buscar lists (LISTAS)
    public function getLists($folderId)
    {
        $response = Http::withHeaders([
            'Authorization' => env('CLICKUP_API_TOKEN'),
        ])->get("https://api.clickup.com/api/v2/folder/$folderId/list");

        return $response->json();
    }


    public function getTask($task)
    {
        $response = Http::withHeaders([
            'Authorization' => env('CLICKUP_API_TOKEN'),
        ])->get("https://api.clickup.com/api/v2/task/$task?include_subtasks=true", [
            'include_closed' => true, // ✅ Incluir tarefas concluídas
        ], ['custom_fields' => true], ['include_closed' => true], ['subtasks' => true]);

        return $response->json();
    }

    public function getSubtasks($taskId)
    {
        $response = Http::withHeaders([
            'Authorization' => env('CLICKUP_API_TOKEN'),
        ])->get("https://api.clickup.com/api/v2/task/$taskId/subtask");

        return $response->json();
    }


    // buscar tasks (TAREFAS)
    public function getTasks($listId)
    {
        $response = Http::withHeaders([
            'Authorization' => env('CLICKUP_API_TOKEN'),
        ])->get("https://api.clickup.com/api/v2/list/$listId/task?subtasks=true", [
            'include_closed' => true, // ✅ Incluir tarefas concluídas
        ], ['custom_fields' => true], ['include_closed' => true]);

        return $response->json();
    }

    /*
    public function getTasks($listId)
    {
        $response = Http::withHeaders([
            'Authorization' => env('CLICKUP_API_TOKEN'),
        ])->get("https://api.clickup.com/api/v2/list/$listId/task", [
            'include_subtasks' => true, // Se quiser pegar subtarefas 
        ],
        ['custom_fields' => true],
        ['include_closed' => true]);
    
        return $response->json();
    } */


    // Buscar Tasks de uma List e salvar no banco
    public function syncTasks($listId)
    {
        $response = Http::withHeaders([
            'Authorization' => $this->token,
        ])->get("$this->apiUrl/list/$listId/task");

        $tasks = $response->json()['tasks'] ?? [];

        foreach ($tasks as $task) {
            Task::updateOrCreate(
                ['task_id' => $task['id']], // Identificador único
                [
                    'name' => $task['name'],
                    'status' => $task['status']['status'],
                    'priority' => $task['priority'] ?? null,
                    'assignees' => json_encode($task['assignees'])
                ]
            );
        }

        return "Tasks sincronizadas!";
    }
}
