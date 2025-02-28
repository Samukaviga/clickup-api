<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{

    protected $table = 'teams';

    protected $fillable = [
        'team_id',
        'name'
    ];

    public function spaces()
    {
        return $this->hasMany(Space::class, 'team_id', 'team_id');
    }
}
