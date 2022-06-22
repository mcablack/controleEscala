<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CoordenadorController extends Controller
{
    public function listarFuncionarios(Request $request, $id = null){
        try {
            /** CÓDIGO QUE BUSCA O USUÁRIO LOGADO NA MAQUINA ou servidor
            * exec('wmic COMPUTERSYSTEM Get UserName',$user);
            *ou  dd( $_SERVER[]); a variavel server traz tudo relacionado ao servidor */

            /** Variavel que guarda o P do supervisor, será iniciada através do login do SO */
            $turnos = DB::table('vw_consulta_horario')->select()->get();
        
            //Se existir um $id, significa que a função ta sendo chamada por ajax na view de equipe do coordenador
            if($id){ 
                $p[0] = $id;  //$P[0] será o P do usuário verificado por meio do login do SO, deve ser passado com array para o banco
            }else{
                $p[0] = 'p788330';  //$P[0] será o P do usuário verificado por meio do login do SO, deve ser passado com array para o banco
            }       
            /** Parametros passados para a procedure, mês e ano atual, nesta ordem */
            $p[1] = date('m');
            $p[2] = date('Y');

            $funcionarios = DB::select('exec StoProc_Consulta_Escala_Coordenador ?, ?, ?', $p);
            $coordenador['nome'] = $funcionarios[0]->nome_coordenador;
           
            if(!empty($funcionarios)){
                return view('coordenador.funcionarios',['coordenador' => $coordenador,'supervisores' => $funcionarios , 'turnos'=>$turnos]);
            } else {
                /** Retorno para a tela inicial com mensagem de erro */
                $request->session()->flash('alert-danger', 'Usuário não possui supervisores');
                return redirect('/');
            }
        } catch (\Throwable $th) {
            
            $request->session()->flash('alert-danger', 'Erro de conexão com o servidor.');
            return redirect('/');
        }
    }

    public function listarFuncionariosCoord(Request $request){
        //dd(DB::table('vw_consulta_coordenador')->select()->get());
        try {
            $funcionario_coord= DB::table('vw_consulta_coordenador')->select()->get();
            // dd($funcionario_coord);
            //Veriricando se a variavel for diferente de vazia.
            if(!empty($funcionario_coord)){
                return view('coordenador.funcionariosCoord',['funcionario_coord'=>$funcionario_coord]);
            }else{
                /** Retorno para a tela inicial com mensagem de erro */
                $request->session()->flash('alert-danger', 'Usuário não possui supervisores');
                return redirect('/');
            }
        } catch (\Throwable $th) {
            $request->session()->flash('alert-danger', 'Erro de conexão com o servidor.');
            return redirect('/');
        }
    }


    public function solicitacoesTrocaCasada(){
       
        try{
            //Utilizando procedure de supervisor, devemos utilizar uma só para coordenador
            
            $parametro[0] = 'p730392';
            $parametro[1] = NULL;
            $solicitacoes = DB::select('exec StoProc_Consulta_Solicitacao_Troca_Status_Supervisor_Pendente ?,?', $parametro);
            
            return view('Coordenador.solicitacaoTrocaCasada', ['solicitacoes' => $solicitacoes]);
        }catch (\Throwable $th) {
            session()->put('AlertError', 'Erro de conexão com o servidor.');
            return redirect('/');
        }
    }

    /** INICIO AJAX */
    public function acaoSolicitacao(Request $request){
        try{

            //Utilizando procedure de supervisor, devemos utilizar uma só para coordenador
            
        //     $id_solicitacao = (int)$request->id_solicitacao;
        //     $tipo = (int)$request->tipo; // Tipo : 1 = aceite , 0 = recusa

        //     /** Parametros:  */
        //     $s[0] = (int)$id_solicitacao;
        //     $s[1] = 'p730392';
        //     $s[2] = (int)$tipo;
        //     /** Procedure de aceite ou recusa */
        //     DB::insert('exec StoProc_Autoriza_Solicitacao_Troca_Supervisor ?,?,?', $s);

        //    return '1';   
        return '0';
        }catch(\Throwable $th){
            return '0';
        }
    }

    public function getSolicitacaoRelacionamento($id_relacionamento,$id_solicitacao){
        try{
            // $parametro[0] = NULL;
            // $parametro[1] = $id_relacionamento;
            // $solicitacoes = DB::select('exec StoProc_Consulta_Solicitacao_Troca_Status_Supervisor ?,?', $parametro);
            
            // if(count($solicitacoes) > 1){
            //     foreach( $solicitacoes as $key => $value){
            //         if($value->id_solicitacao_troca != $id_solicitacao){
            //             return response()->json($value);
            //         }
            //     }
            // }else{
            //     return '0';
            // }
            return '0';
        }catch (\Throwable $th) {
            return '404';
        }

    }
    /** FIM AJAX */
}