<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class projectB2B extends Model
{
    protected $table = 'project_b2_b_s';
    protected $fillable = [
        'date',
        'project',
        'client',
        'Action',
        'responsable',
        'etat',
        'delai',
        'date_cloture',
        'observation',
    ];

}
