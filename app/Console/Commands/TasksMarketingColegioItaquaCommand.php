<?php

namespace App\Console\Commands;

use App\Jobs\ProcessClickUpSubTask;
use App\Jobs\ProcessClickUpTasks;
use App\Models\Task;
use App\Models\TaskAssignee;
use App\Models\TaskList;
use App\Services\ClickUpService;
use Illuminate\Console\Command;

class TasksMarketingColegioItaquaCommand extends Command
{

    protected $signature = 'app:tasks-marketing-colegio-itaqua-command';


    protected $description = 'Tasks of List Marketing Colegio Itaqua';


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

            $folder = $this->clickUpService->getTasks("901108288683", $page);

            if (!empty($folder['tasks'])) {
                TaskList::updateOrCreate(
                    ['list_id' => "901108288683"],
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
}
