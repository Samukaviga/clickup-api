<?php

namespace App\Console\Commands;

use App\Models\Folder;
use App\Models\TaskList;
use App\Models\Space;
use App\Models\Task;
use App\Models\Team;
use App\Models\TaskAssignee;
use App\Services\ClickUpService;
use Illuminate\Console\Command;

class SyncClickUpData extends Command
{
    protected $signature = 'clickup:sync';
    protected $description = 'Sincroniza os dados do ClickUp com o banco de dados';
    protected $clickUpService;

    public function __construct(ClickUpService $clickUpService)
    {
        parent::__construct();
        $this->clickUpService = $clickUpService;
    }

    public function handle()
    {
        $this->info("🔄 Iniciando sincronização do ClickUp...");

        // 1️⃣ Buscar e salvar os Workspaces (Teams)
        $teams = $this->clickUpService->getWorkspaces();
        
        foreach ($teams['teams'] as $team) {
            Team::updateOrCreate(
                ['team_id' => $team['id']],
                ['name' => $team['name']]
            );

            // 2️⃣ Buscar e salvar os Spaces do Team
            $spaces = $this->clickUpService->getSpaces($team['id']);
            foreach ($spaces['spaces'] as $space) {
                Space::updateOrCreate(
                    ['space_id' => $space['id']],
                    ['name' => $space['name'], 'team_id' => $team['id'], 'private' => $space['private']]
                );

                // 3️⃣ Buscar e salvar os Folders do Space
                $folders = $this->clickUpService->getFolders($space['id']);
                foreach ($folders['folders'] as $folder) {
                    Folder::updateOrCreate(
                        ['folder_id' => $folder['id']],
                        ['name' => $folder['name'], 'space_id' => $space['id']]
                    );

                    // 4️⃣ Buscar e salvar as Lists do Folder
                    $lists = $this->clickUpService->getLists($folder['id']);
                    foreach ($lists['lists'] as $list) {
                        TaskList::updateOrCreate(
                            ['list_id' => $list['id']],
                            ['name' => $list['name'], 'folder_id' => $folder['id']]
                        );

                        // 5️⃣ Buscar e salvar as Tasks da List
                        $tasks = $this->clickUpService->getTasks($list['id']);
                        foreach ($tasks['tasks'] as $task) {
                            $taskModel = Task::updateOrCreate(
                                ['task_id' => $task['id']],
                                [
                                    'name' => $task['name'],
                                    'list_id' => $list['id'],
                                    'status' => $task['status']['status'],
                                    'priority' => $task['priority'] ?? null,
                                    'date_created' => date('Y-m-d H:i:s', $task['date_created'] / 1000),
                                    'date_updated' => date('Y-m-d H:i:s', $task['date_updated'] / 1000),
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
            }
        }

        $this->info("✅ Sincronização concluída com sucesso!");
    }
}
