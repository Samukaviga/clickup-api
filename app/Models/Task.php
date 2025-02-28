<?php

namespace App\Models;

use App\Models\TaskAssignee;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{

    protected $table = 'tasks';

    protected $fillable = [
        'task_id',
        'name',
        'list_id',
        'status',
        'priority',
        'empresa',
        'departamento_mkt',
        'planejamento',
        'date_created',
        'date_updated',
    ];

    protected $casts = [
        'date_created' => 'datetime',
        'date_updated' => 'datetime',
    ];

    public function list()
    {
        return $this->belongsTo(TaskList::class, 'list_id', 'list_id');
    }

    public function assignees()
    {
        return $this->hasMany(TaskAssignee::class, 'task_id', 'task_id');
    }
}
