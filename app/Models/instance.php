<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instance extends Model
{
    protected $table = 'instances';
    protected $fillable = [
        'date',
        'instance',
        'client',
        'ligne',
        'responsable',
        'etat',
        'delai',
        'date_cloture',
        'observation',
    ];

}
