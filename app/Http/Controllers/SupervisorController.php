<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Supervisor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Solicitacao_troca\Solicitacao;
use App\Models\Solicitacao_troca\Relacionamento;
use App\Models\Solicitacao_troca\Autorizacao;
use App\Models\Solicitacao_troca\Escala;

class SupervisorController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
    */
    


    public function listarEquipe(Request $request,$id = null)
    {   
        try {
            /************************** ETAPA1: MONTAGEM DA TABELA DE EQUIPE ******************************/

            /* CÓDIGO QUE BUSCA O USUÁRIO LOGADO NA MAQUINA ou servidor
            * exec('wmic COMPUTERSYSTEM Get UserName',$user); */
            
            // Se existir um $id, significa que a função ta sendo chamada por ajax na view de equipe do coordenador
            if($id){ 
                $p[0] = $id;        
            } else {
                $p[0] = supervisor();  
            }
            
            /* Parametros passados para a procedure, mês e ano atual, nesta ordem */
            $p[1] = date('m');
            $p[2] = date('Y');

            /* Busca das escalas de operadores relacionados ao supervisor logado */
            $escala_equipe = DB::select('exec StoProc_Consulta_Escala_Supervisor ?, ?, ?', $p);
            $turnos = DB::table('vw_consulta_horario')->select()->get();

            if(!empty($escala_equipe)){
                /* Matriz para separar os operadores da equipe, a procedure que vem com 60 registros, para um mês com 30 dias, possui no minimo 2 operadores diferentes */
                $days = Carbon::parse(Carbon::now()->format('Y-m-d'))->daysInMonth; //$days define quantos registros de escala o usuário deve possuir
               

                $operador = $this->montarOperadores($escala_equipe);
              
                /************************** ETAPA2: CONSOLIDADE DE OPERADORES TRABALHANDO E FOLGANDO ******************************/
                /** Contagem de escalados para consolidado */
                $currentDate = date('Y-m-d'); 
                $contadorEscalado = 0;
                $contadorFolga = 0;
                
                foreach($escala_equipe as $escala) {
                    if($escala->data == $currentDate){
                        $status = $escala->codigo_status;
                        
                        if($status == 'F'){
                            $contadorFolga++;
                        }
                        if($status == 'T' || $status == 'ET' ){
                            $contadorEscalado++;
                        }
                    }
                }

                return view('supervisor.equipe', ['operador' => $operador,'dias'=>$days, 'operadoresTrabalhando' => $contadorEscalado, 'operadoresFolgando' => $contadorFolga,'turnos'=>$turnos]);
            }else{
                /** Retorno para a tela inicial com mensagem de erro */
                $request->session()->put('AlertError', 'Supervisor não possui operadores');
                return redirect('/');
            }
        } catch (\Throwable $th) {
            $request->session()->put('AlertError', 'Erro de conexão com o servidor.');
            return redirect('/');
        }
    }

    public function montarOperadores($escala_equipe){
        $operador = array();
        $contador = 0;

        foreach($escala_equipe as $indice => $valor){
            $matricula_atual = $escala_equipe[$indice]->login;
            $matricula_anterior = '';

            // Se não estiver no primeiro ponteiro do foreach, a matricula anterior pode ser diferente do atual
            if($indice != 0){
                $matricula_anterior = $escala_equipe[$indice-1]->login;
            }
            // Se a matricula atual for o mesmo do anterior, incrementamos o contador
            if($matricula_atual == $matricula_anterior){
                $contador++;
            }else{
                // Se 'virarmos', estamos em um outro usuário e zeramos o contador
                $contador = 0;                
            }
            
            $operador[$matricula_atual][$contador] = $valor; // Jogamos no ponteiro do Usuário, todas as suas escalas cadastradas no banco
       
        }
        return $operador;

    }
  
    public function listarTrocas(){
       
        try{

            $parametro[0] = supervisor();
            $parametro[1] = NULL;
            $parametro[2] = NULL;
            $parametro[3] = NULL;
            $parametro[4] = 1;
            $solicitacoes = DB::select('exec StoProc_Consulta_Solicitacao_Troca_Status_Pendente_Supervisor_Coordenador_e_Trafego ?,?,?,?,?', $parametro);
            
            return view('Supervisor.trocaPendente', ['solicitacoes' => $solicitacoes]);
        }catch (\Throwable $th) {
            session()->put('AlertError', 'Erro de conexão com o servidor.');
            return redirect('/');
        }
    }

    public function trocaEspecial(){
        try{

        $p[0] = supervisor();
        
        $operadores = DB::select('exec StoProc_Consulta_Usuario_Por_Supervisor ?', $p) ; //Adicionar procedure para buscar operadores do supervisor
        
        $turnos = DB::table('vw_consulta_horario')->select()->get();
        $skills = DB::table('vw_consulta_skill')->select()->get(); //Adicionar procedure que busca todas as skills existentes
        $status = DB::table('vw_consulta_status_usuario')->select()->get(); ; //Adicionar procedure que busca todos os status de escala existentes
    
        return view('Supervisor.trocaEspecial', ['turnos' => $turnos,'skills' => $skills,'status' => $status,'operadores' => $operadores]);

        }catch(\Throwable $th){
            session()->put('AlertError', 'Erro de conexão com o servidor.');
            return redirect('/');
        }
    }

    public function listarSolicitacoesTodas(){
        
        try{
            $solicitacoesTodas = [];
            $turnos = DB::table('vw_consulta_horario')->select()->get();
            
            $parametros[0] = supervisor() ;
            $parametros[1] = null;
            $parametros[2] = null;
            $parametros[3] = null;
            $busca = DB::select('exec StoProc_Consulta_Solicitacao_Troca_Status_Supervisor_Coordenador_e_Trafego ?,?,?,?',$parametros);
            
            // dd($busca);
           
            
            /** POG TEMPORARIA - Apresentação 18/12 */
            // foreach($busca as $key => $solicitacao){
            //     if($solicitacao->id_sup == supervisor() ){
            //         $solicitacoesTodas[$solicitacao->id_solicitacao_troca] = $solicitacao;
            //         //Salva no array somente o ultimo registro de uma solicitação
            //     }
            // }
            
            return view('Supervisor.solicitacoesTodas',['solicitacoesTodas'=>$busca],[ 'turnos'=>$turnos]);
        }catch (\Throwable $th) {
            session()->put('AlertError', 'Erro de conexão com o servidor.');
            return redirect('/');
        }  
    }

    /** INICIO AJAX */
    public function acaoSolicitacao(Request $request){
        try{   
            /** Parametros:  */
            $parametros[0] = (int)$request->id_solicitacao; // Id da solicitação
            $parametros[1] = supervisor();            // Matrícula do supervisor
            $parametros[2] = (int)$request->tipo;          // Tipo de ação : 1 = aceite , 0 = recusa

           
           DB::insert('StoProc_Autoriza_Desautoriza_Relacionamento_Troca_Supervisor ?,?,?', $parametros);
            
            // $solicitacao = Solicitacao::find($id_solicitacao);

            // $escalaSolicitacao = Escala::find($solicitacao->id_escala_atual);
          
            // $relacionamento = new Relacionamento();
            // $relacionamento = $relacionamento->getRelacionamentoFromSolicitacao($id_solicitacao);

            // if($relacionamento->id_troca_1 == $id_solicitacao){

            //     $aceite = Solicitacao::find($relacionamento->id_troca_2);
            //     $autorizacaoAceite = Autorizacao::find($relacionamento->id_troca_2);

            // }else{

            //     $aceite = Solicitacao::find($relacionamento->id_troca_1);
            //     $autorizacaoAceite = Autorizacao::find($relacionamento->id_troca_1);

            // }

            // $escalaAceite = Escala::find($aceite->id_escala_atual);

            // $autorizacao = Autorizacao::find($id_solicitacao);
            
            // switch ($tipo) {

            //     case 0:
            //     $autorizacao->status_sup_aprov = $tipo;
            //     $autorizacao->id_status_autorizacao = 4;
            //     $autorizacao->save();

            //     $autorizacaoAceite->status_sup_aprov = $tipo;
            //     $autorizacaoAceite->id_status_autorizacao = 4;
            //     $autorizacaoAceite->save();

               
            //     $new_solicitacao[0] = $aceite->id_usuario_cad_solicitacao; //Usuario que cadastra solicitacao
            //     $new_solicitacao[1] = $aceite->id_solicitante_troca; //P do operador
            //     $new_solicitacao[2] = (int)1 ; //tipo da troca
            //     $new_solicitacao[3] = $aceite->data_troca; //data da troca
            //     $new_solicitacao[4] = $aceite->id_escala_atual; //p da escala do operador 
            //     $new_solicitacao[5] = $escalaSolicitacao->id_horario; //turno selecionado 
            //     //$new_solicitacao[6] = ''; //id da solicitacao que for aceita - vazio quando não for aceite
                
            //     //Cadastrar_Solicitacao_Troca_e_Aceite
            //     DB::insert('exec StoProc_Cadastra_Solicitacao_Troca_e_Aceite ?,?,?,?,?,?', $new_solicitacao);

            //         break;

            //     case 1:
            //                 $autorizacao->status_sup_aprov = $tipo;
            //                 $autorizacao->save();

            //             if($autorizacaoAceite->status_sup_aprov == 1){
            //                 //Atualiza o status da solicitação e faz a troca da escala
            //                 $dataSolicitante = $escalaSolicitacao->data;
            //                 $dataAceite = $escalaAceite->data;

            //                 $turnoSolicitante = $escalaSolicitacao->id_horario;
            //                 $turnoAceite = $escalaAceite->id_horario;

            //                 $escalaSolicitacao->data = $dataAceite;
            //                 $escalaSolicitacao->id_horario = $turnoAceite;
            //                 $escalaSolicitacao->id_status_usuario = 10;
            //                 $escalaSolicitacao->save();

            //                 $escalaAceite->data = $dataSolicitante;
            //                 $escalaAceite->id_horario = $turnoSolicitante;
            //                 $escalaAceite->id_status_usuario = 10;
            //                 $escalaAceite->save();

            //                 $autorizacao->id_status_autorizacao = 3;
            //                 $autorizacao->save();
            //                 $autorizacaoAceite->id_status_autorizacao = 3;
            //                 $autorizacaoAceite->save();
            //             }
            //         break;
            //     default:
            //         # code...
            //         break;
            // }
            /** Procedure de aceite ou recusa */
            //DB::insert('exec StoProc_Autoriza_Solicitacao_Troca_Supervisor ?,?,?', $s);

            return '1';   
        }catch(\Throwable $th){
            return '0';
        }
    }

    public function getSolicitacaoRelacionamento($id_relacionamento,$id_solicitacao){
        try{
            $parametro[0] = NULL;
            $parametro[1] = $id_relacionamento;
            //Busca as duas solicitações presentes no relacionamento
            $solicitacoes = DB::select('exec StoProc_Consulta_Solicitacao_Troca_Status_Supervisor_Pendente ?,?', $parametro);
            //Dentro das solicitações buscadas, retorna aquela a qual o id_solicitação seja diferente do passado por parametro da função
            if(count($solicitacoes) > 1){
                foreach( $solicitacoes as $key => $value){
                    if($value->id_solicitacao_troca != $id_solicitacao){
                        return response()->json($value);
                    }
                }
            }else{
                //Caso ocorra algum erro ou a busca só busque uma solicitação, quer dizer que houve algum erro no relacionamento e retorna 0, erro tratado na view
                return '0';
            }
        }catch (\Throwable $th) {
            return '404';
        }
    }
    /** FIM AJAX */
}

