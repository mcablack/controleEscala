<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Relacionamento extends Model
{
    protected $table = 'relacionamento_troca';
    protected $primaryKey = 'id_relacionamento_troca';
   
    public $timestamps = false;
    
    public function getRelacionamentoFromSolicitacao($id_solicitacao){

        return $this->where('id_troca_1',$id_solicitacao)
                ->orWhere('id_troca_2',$id_solicitacao)
                ->orderBy('data_cadastro','desc')
                ->first();
      
    }
}
