<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Storage;


class PlanejamentoController extends Controller
{
    public function listarColaboradores(Request $request, $id = null){
        
            /************************** ETAPA1: MONTAGEM DA TABELA DE EQUIPE ******************************/

            /* CÓDIGO QUE BUSCA O USUÁRIO LOGADO NA MAQUINA ou servidor
            * exec('wmic COMPUTERSYSTEM Get UserName',$user); */
            
            // Se existir um $id, significa que a função ta sendo chamada por ajax na view de equipe do coordenador
            if($id){ 
                $p[0] = $id;  // $P[0] será o P do usuário verificado por meio do login do SO, deve ser passado com array para o banco
            }else{
                $p[0] = 'P730392';  // $P[0] será o P do usuário verificado por meio do login do SO, deve ser passado com array para o banco
            }

            /* Parametros passados para a procedure, mês e ano atual, nesta ordem */
            $p[1] = date('m');
            $p[2] = date('Y');

            /* Busca das escalas de operadores relacionados ao supervisor logado */
            $escala_equipe = DB::select('exec StoProc_Consulta_Escala_Trafego ?, ?, ?', $p);
            // dd($escala_equipe);
            $turnos = DB::table('vw_consulta_horario')->select()->get();
         
            if(!empty($escala_equipe)){
                /* Matriz para separar os operadores da equipe, a procedure que vem com 60 registros, para um mês com 30 dias, possui no minimo 2 operadores diferentes */
                $days = Carbon::parse(Carbon::now()->format('Y-m-d'))->daysInMonth; //$days define quantos registros de escala o usuário deve possuir
                $operador = array();
                $contador = 0;
                // escala_equipe é a query completa, no foreach separamos ela e usamos o seu $indice ou o seu $valor, valor é a linha do registro na tabela de escala
                foreach($escala_equipe as $indice => $valor){
                    $matricula_atual = $escala_equipe[$indice]->login;
                    $matricula_anterior = '';

                    // Se não estiver no primeiro ponteiro do foreach, a matricula anterior pode ser diferente do atual
                    if($indice != 0){
                        $matricula_anterior = $escala_equipe[$indice-1]->login;
                    }
                    // Se a matricula atual for o mesmo do anterior, incrementamos o contador
                    if($matricula_atual != $matricula_anterior ){
                        $contador = 0;
                    }else{
                        // Se 'virarmos', estamos em um outro usuário e zeramos o contador
                        $contador++;                
                    }
                    
                    $operador[$matricula_atual][$contador] = $valor; // Jogamos no ponteiro do Usuário, todas as suas escalas cadastradas no banco
                }
                
                // $property = DB::select('SELECT * FROM properties WHERE name = ?', [$name]);
                return view('planejamento.colaboradores', ['operador' => $operador,'dias'=>$days, 'turnos'=>$turnos]);
            }else{
                /** Retorno para a tela inicial com mensagem de erro */
                $request->session()->flash('alert-danger', 'Supervisor não possui operadores');
                return redirect('/');
            }
        
    }

    public function listarSolicitacoes(){
        $turnos = DB::table('vw_consulta_horario')->select()->get();
        $solicitacoesTodas = DB::select('exec StoProc_Consulta_Solicitacao_Troca');
        //Lista todas AS SOLICITAÇÕES, inclusive as que já foram 'finalizadas' e retornaram pro sistema, porém o status segue errado, como duplicata
        return view('planejamento.trocasTodas', ['turnos'=>$turnos],['solicitacoesTodas'=>$solicitacoesTodas]);
    }

    public function importacaoEscala(){     
     
        return view('planejamento.importacao');
    }

    public function importacaoEscalaAction(Request $request){

        try {
            // Valida se algum arquivo foi enviado
            if($request->hasFile('escala')){
                // Pegar o nome do arquivo com a extensão
                $filenameWithExt = $request->file('escala')->getClientOriginalName();

                // Pegar apenas o nome do arquivo
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

                // Pegar apenas a extensão
                $extension = $request->file('escala')->getClientOriginalExtension();

                // Nome ao salvar o arquivo
                $fileNameToStore= $filename.'_'.time().'.'.$extension;
                $fileNameToStoreCopy = $filename.'.'.$extension;
                
                $path = $request->file('escala')->storeAs('public/uploads', $fileNameToStore);
                $path = $request->file('escala')->storeAs('public', $fileNameToStoreCopy);
                
                /** SSIS_StoProc_Import_Escala ; StoProc_Inserir_Escala_Importada */
                DB::select('exec SSIS_StoProc_Import_Escala');
                // DB::insert('SSIS_StoProc_Import_Escala');



                //return redirect()->back();
            } else {
                return 'Por favor, envie um arquivo!';
            } 
        } catch (\Throwable $th) {
            return $th;
            $request->session()->flash('alert-danger', 'Erro de conexão com o servidor.');
            return redirect('/');
        }
    }

    // Função para retornar os dados para o modal, fazendo parte do fluxo de edição
    public function editarColaboradores($matricula){
        $data = [];

        $data['skills'] = DB::select('SELECT * from skill');
        $data['user']   = DB::select('SELECT * FROM empregado as e 
                                      INNER JOIN escala as esc 
                                      ON e.login = esc.id_usuario
                                      INNER JOIN horario as h
                                      ON esc.id_horario = h.id_horario
                                      WHERE login = ?', [$matricula]);
        return $data;
    }

    // Editar o usuário
    public function updateColaboradores(Request $request){
        try {
            // Verificar campos preenchidos
            if(empty($request->data_solicitada)){
                return redirect('/trafego/colaboradores')->with('AlertError', 'Por favor, informe a data de troca.');
            } elseif(empty($request->status)){
                return redirect('/trafego/colaboradores')->with('AlertError', 'Por favor, informe o status de troca.');
            } else {
                // Pegar o id da escala
                $escala = DB::select('SELECT id_escala FROM escala WHERE data = ? and id_usuario = ?', [$request->data_solicitada, $request->matriculaHidden]);
            }

            // Atribui os dados status, skill e data em um array para update
            $data[] = $request->data_solicitada;
            $data[] = $request->status;
            $data[] = $request->skillModal;
            $data[] = $escala[0]->id_escala;

            $updateUser = DB::update('UPDATE escala SET data = ?, id_status_usuario = ?, id_skill = ? WHERE id_escala = ?', $data);

            // Verificar se deu o update com sucesso e retornar para o usuário
            if(!empty($updateUser)){
                return redirect('/trafego/colaboradores')->with('Alert', 'Alteração registrada com sucesso!');
            } else {
                return redirect('/trafego/colaboradores')->with('AlertError', 'Problema ao atualizar o usuário.');
            }
        } catch (\Throwable $th) {
            $request->session()->flash('alert-danger', 'Erro de conexão com o servidor.');
            return redirect('/');
        }
    }

    public function listarTrocas(){
        try{
        
        //Utilizando procedures do supervisor, modificar p/ procedures do trafego
        
        $parametro[0] = 'p730392';
        $parametro[1] = NULL;
        $solicitacoes = DB::select('exec StoProc_Consulta_Solicitacao_Troca_Status_Supervisor_Pendente ?,?', $parametro);
        //  dd($solicitacoes);
        return view('Planejamento.trocaPendente',['solicitacoes' => $solicitacoes]);
        }catch (\Throwable $th) {
            session()->put('AlertError', 'Erro de conexão com o servidor.');
            return redirect('/');
        } 
    }



    /** INICIO AJAX */

    public function acaoSolicitacao(Request $request){
        try{
            $id_solicitacao = (int)$request->id_solicitacao;
            $tipo = (int)$request->tipo; // Tipo : 1 = aceite , 0 = recusa

            /** Parametros:  */
            $s[0] = (int)$id_solicitacao;
            $s[1] = 'p730392';
            $s[2] = (int)$tipo;
            /** Procedure de aceite ou recusa */
            DB::insert('exec StoProc_Autoriza_Solicitacao_Troca_Supervisor ?,?,?', $s);

           return '1';   
        }catch(\Throwable $th){
            return '0';
        }
    }

    public function getSolicitacaoRelacionamento($id_relacionamento,$id_solicitacao){
        try{
            $parametro[0] = NULL;
            $parametro[1] = $id_relacionamento;
            $solicitacoes = DB::select('exec StoProc_Consulta_Solicitacao_Troca_Status_Supervisor ?,?', $parametro);
            
            if(count($solicitacoes) > 1){
                foreach( $solicitacoes as $key => $value){
                    if($value->id_solicitacao_troca != $id_solicitacao){
                            return response()->json($value);
                    }
                }
            }else{
                return '0';
            }
        }catch (\Throwable $th) {
            return '404';
        }

    }



}
