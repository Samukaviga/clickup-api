<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{

    protected $table = 'folders';

    protected $fillable = [
        'folder_id',
        'name',
        'space_id'
    ];

    public function space()
    {
        return $this->belongsTo(Space::class, 'space_id', 'space_id');
    }

    public function lists()
    {
        return $this->hasMany(TaskList::class, 'folder_id', 'folder_id');
    }
}
