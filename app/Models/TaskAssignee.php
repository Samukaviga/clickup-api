<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; 

class TaskAssignee extends Model
{

    use HasFactory;

    protected $table = 'task_assignees';

    protected $fillable = [
        'task_id',
        'assignee_id',
        'assignee_name',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id', 'task_id');
    }
}
