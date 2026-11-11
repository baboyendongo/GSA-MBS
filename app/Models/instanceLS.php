<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class instanceLS extends Model
{
    protected $table = 'instance_l_s';

    protected $fillable = [
        'date',
        'evenement',
        'operation',
        'ligne',
        'codeclient',
        'clients',
        'ville',
        'phase',
        'responsable',
        'delai',
        'date_cloture',
        'observation',
    ];
}
