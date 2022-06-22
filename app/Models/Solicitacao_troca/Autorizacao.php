<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Autorizacao extends Model
{
    protected $table = 'autorizacao';
    protected $primaryKey = 'id_solicitacao_troca';

    public $timestamps = false;
}
