<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskList extends Model
{

    protected $table = 'lists';

    protected $fillable = [
        'list_id',
        'name',
        'folder_id',
        'space_id',
    ];

    public function folder()
    {
        return $this->belongsTo(Folder::class, 'folder_id', 'folder_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'list_id', 'list_id');
    }
}
