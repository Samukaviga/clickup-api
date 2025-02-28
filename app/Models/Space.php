<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Space extends Model
{

    protected $table = 'spaces';

    protected $fillable = [
        'space_id',
        'name',
        'team_id'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'team_id');
    }

    public function folders()
    {
        return $this->hasMany(Folder::class, 'space_id', 'space_id');
    }
}
