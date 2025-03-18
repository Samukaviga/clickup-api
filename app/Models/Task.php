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
        'start_date',
        'due_date',
        'date_created',
        'date_updated',
        'cad',
        'cargo',
        'comparecimento',
        'fases_lead_time',
        'mes',
        'unidade',
        'parent',
        'time_estimate',
        'delegado_para',
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
