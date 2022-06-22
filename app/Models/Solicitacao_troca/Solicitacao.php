<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solicitacao extends Model
{
    protected $table = 'solicitacao_troca';
    protected $primaryKey = 'id_solicitacao_troca';
   
    public $timestamps = false;
}
