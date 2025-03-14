<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignee extends Model
{


    protected $table = 'assignees';
    protected $fillable = [
        'assignee_id',
        'username',
        'email',
        'profile_picture',
    ];
}
