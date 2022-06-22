<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DateTime;
use DateTimeInterface;
use DateTimeZone;

class OperadorController extends Controller
{
    
    public function teste(){
        try {
         
        return view ('operador.teste');
        }catch (\Throwable $th){
            return redirect('/');
        }
    }

    public function escala(Request $request,$id = null){
       
        // p641986 ; P642595
        try {
            /** CÓDIGO QUE BUSCA O USUÁRIO LOGADO NA MAQUINA ou servidor
            * exec('wmic COMPUTERSYSTEM Get UserName',$user);
            */
           
            
            /** Traz todas as escalas no banco, com um join na tabela horário que busca as entradas e saidas das escalas  */
            if($id){ //Se existir um $id, significa que a função ta sendo chamada por ajax na view de equipe do supervisor
                $p[0] = $id;  //$P[0] será o P do usuário verificado por meio do login do SO, deve ser passado com array para o banco
            }else{
                $p[0] = operador();  //$P[0] será o P do usuário verificado por meio do login do SO, deve ser passado com array para o banco
            }
            
            $events = DB::select('exec StoProc_Consulta_Escala_Operador ? ', $p);
            
            
            /** Se o operador possuir escala, montamos o calendário */
            if(!empty($events)){
                /** Dados do operador  */
                $operador['nome_operador'] = $events[0]->nome;
                $operador['skill'] = $events[0]->skill;
                $operador['nome_supervisor'] = $events[0]->nome_supervisor;
                /** Array que vai receber os eventos do usuário para montar o calendário   */
                $allEvents = [];
                /** Variavel que define o visual do evento no calendário , true: fita, false: fita + data e hora  */
                $allDay = true;
            
                // Adicionar os eventos em um array
               
                foreach ($events as $event) {
                    /** Decide se o evento vai informar a hora de entrada e saida ou se vai ser allDay */
                    if($event->id_status_usuario == 1 || $event->id_status_usuario == 4 || $event->id_status_usuario == 10){
                        $allDay = false;
                    }else{
                        $allDay = true;
                    }

                    /** Se o $allDay = false, significa que o evento é escala de trabalho/treinamento, então criamos 2 eventos.
                     * o primeiro evento não possui descrição, pois só vamos mostrar os horarios de entrada e saida
                     * o segundo evento possui a descrição, porem o allDay é setado como true, o que cria uma fita com a atividade do dia
                    */
                    if($allDay === false){
                        $allEvents[] = [
                            'title' => '',
                            'start' => $event->data_hora_inicial,
                            'end' => $event->data_hora_final,
                            'color' => $event->color,
                            'allDay' => $allDay
                        ];
                        $allEvents[] = [
                            'title' => $event->desc_status_usuario,
                            'start' => $event->data_hora_inicial,
                            'end' => $event->data_hora_final,
                            'color' => $event->color,
                            'allDay' => true
                        ];
                    }else{
                        /** Se o $allDay = true, significa que a escala não é de trabalho/treinamento, então criamos um evento.
                         * O evento possui a descrição do banco e o allDay continua como true, mostrando uma fita da atividade do dia
                        */
                        $allEvents[] = [
                            'title' => $event->desc_status_usuario,
                            'start' => $event->data_hora_inicial,
                            'end' => $event->data_hora_final,
                            'color' => $event->color, 
                            'allDay' => $allDay
                        ];
                    }    
                }
                /** Retorno para a tela de escala com calendário montado */
                //return response()->json();
                return view('operador.escala',array_merge(['events' => json_encode($allEvents),'operador' => $operador]));
            }else{
                /** Retorno para a tela inicial com mensagem de erro */
                $request->session()->flash('alert-danger', 'Operador não possui escala cadastrada no sistema');
                return redirect('/');
            }
        } catch (\Throwable $th) {
            /** Retorno para a tela inicial com mensagem de erro */
            $request->session()->flash('alert-danger', 'Erro de conexão com o servidor.');
            return redirect('/');
        }
    }

    public function trocaCasada(Request $request){
        try {

            $p[0] = operador();
            /** Substituir forma como obtemos os dados do operador, atualmente utilizamos a sua primeira escala vinda da procedure, mas iremos buscar em uma procedure futura todos os dados */

            $escala = DB::select('exec StoProc_Consulta_Escala_Operador ? ', $p);
            $turnos = DB::table('vw_consulta_horario')->select()->get();

            /** Se o operador possuir escala, abrimos solicitação de troca, se não, retornamos um erro */
            if(!empty($escala)){
                $operador['nome_operador'] = $escala[0]->nome;
                $operador['matricula'] = $escala[0]->login;
                $operador['skill'] = $escala[0]->skill;
                $operador['nome_supervisor'] = $escala[0]->nome_supervisor;
                return view('operador.trocaCasada',array_merge(['operador'=> $operador,'turnos'=> $turnos]));
            }else{
                /** Retorno para a tela inicial com mensagem de erro */
                return redirect('/')->with('Alert', 'Operador não possui escala cadastrada no sistema');;
            }  
        } catch (\Throwable $th) {
            /** Retorno para a tela inicial com mensagem de erro */
            $request->session()->flash('alert-danger', 'Erro de conexão com o servidor.');
            return redirect('/');
        }
    }


    public function solicitarTrocaCasada(Request $request){
        try {
          
            $data_solicitacao = Carbon::parse($request->data_solicitada)->format('Ymd'); //Formatação da data para padrão da procedure (Ymd)
            $new_solicitacao = [];
            
            $new_solicitacao[0] = operador(); //Usuario que cadastra solicitacao
            $new_solicitacao[1] = operador(); //P do operador
            $new_solicitacao[2] = (int)1 ; //tipo da troca
            $new_solicitacao[3] = $data_solicitacao; //data da troca
            $new_solicitacao[4] = (int)$request->id_escala; //p da escala do operador 
            $new_solicitacao[5] = (int)$request->turno_solicitado; //turno selecionado 
            $new_solicitacao[6] = null; //id da solicitacao que for aceita - vazio quando não for aceite
            
            //Cadastrar_Solicitacao_Troca_e_Aceite
            if(DB::insert('exec StoProc_Cadastra_Solicitacao_Troca_e_Aceite ?,?,?,?,?,?,?', $new_solicitacao) ){
                return redirect('/operador/minhassolicitacoes')->with('Alert', 'Solicitação de troca criada com sucesso!');
            }else{
                $request->session()->put('AlertError', 'Erro de conexão com o servidor.');
                return redirect('/operador/trocacasada');
            }
        } catch (\Throwable $th) {
            /** Retorno para a tela inicial com mensagem de erro e rollback na transação do banco */
            DB::rollback();
            $request->session()->put('AlertError','Erro de conexão com o servidor.');
            return redirect('/');
        }
    }

    public function solicitacoesDeTroca(){
        /** Status para as solicitações:
             * 1. Aguardando aceitação
             * 2. Aguardando aprovação do supervisor
             * 3. Aprovada
             * 4. Reprovada
             * 5. Expirada
        */
        try {
            $parametro[0] = operador();
            $solicitacoes = DB::select('exec StoProc_Consulta_Solicitacao_Troca_Status_Operador ?',$parametro);
            return view ('operador.solicitacoesDeTroca',array_merge(['solicitacoes' => $solicitacoes,'login' => $parametro[0] ]));
        } catch (\Throwable $th) {
            // retorno
        }
    }


    /** INICIO AJAX */

    /** Para aceitar uma troca, precisamos passar como parametros: P_USUARIO,ID_SOLICITACAO_TROCA e ID_ESCALA_ACEITACAO */
    public function aceitarTrocaCasada(Request $request){

       
        try {

            //Parametros p/ procedure de aceitar troca
            //P do usuario, que será automático no ambiente caixa 
            //Parametro para confirmar a escala e buscar id a partir da data
            
            $parametro[0] = operador();
            $escala = DB::select('exec StoProc_Consulta_Escala_Operador ? ', [$parametro[0]]);
            $data = $request->data;
            $valideEscala = false; //variavel booleana para verificar se o id da escala na data informada é o mesmo passado por request
            if($data){
                foreach($escala as $key => $value){
                    if($value->data == $data && $value->id_escala == $request->id_escala && $value->id_horario == $request->turno_solicitado ){
                        $valideEscala=true;
            
                    }
                }
            }
          
          
            $data_solicitacao = Carbon::parse($request->data)->format('Ymd'); //Formatação da data para padrão da procedure (Ymd)
            //Se a escala do usuário no dia informado, for a mesma passada por request, seguimos para o aceite da solicitação
            if($valideEscala){

                $new_solicitacao[0] = operador(); //Usuario que cadastra solicitacao
                $new_solicitacao[1] = operador(); //P do operador
                $new_solicitacao[2] = (int)1 ; //tipo da troca
                $new_solicitacao[3] = $data_solicitacao; //data da troca
                $new_solicitacao[4] = (int)$request->id_escala; //id da escala do operador 
                $new_solicitacao[5] = (int)$request->turno_troca; //turno selecionado 
                $new_solicitacao[6] = (int)$request->id_solicitacao; //id da solicitacao que for aceita - vazio quando não for aceite
                
                
                    if($request->ajax()){
                        if(DB::insert('exec StoProc_Cadastra_Solicitacao_Troca_e_Aceite ?,?,?,?,?,?,?', $new_solicitacao)){
                            $request->session()->put('Alert','Solicitação de troca aceita com sucesso!');
                        }else{
                            $request->session()->put('AlertError','Erro de conexão com o servidor');
                        }
                        return '1'; //retorno passado para a função ajax, que redireciona para a pagina minhas solicitaçãoes com a mensagem passada no flash, necessita de qualquer retorno para poder prosseguir
                    }
            //Se não, redirecionamos para a pagina de minhas solicitações com a mensagem de erro
            }else{
               return '0';
            }
        } catch (\Throwable $th) {
            /** Retorno para a tela inicial com mensagem de erro */
            $request->session()->put('AlertError','Erro de conexão com o servidor');
            return redirect('/');
        }
    }

    /** Função acessada via ajax na view de trocaCasada, quando o usuário seleciona uma data de solicitação */
    public function escalaTroca(Request $request,$data_solicitacao=null,$p_operador = null)
    {
        try {
            $p[0] = isset($p_operador) ? $p_operador : operador();
            $escala = DB::select('exec StoProc_Consulta_Escala_Operador ? ', $p);
            $verify = false;
            $escala_troca = [];
            if($data_solicitacao){
                foreach($escala as $key => $value){
                    if($value->data == $data_solicitacao ){
                        $verify = true;
                        $escala_troca = $value;
                    }
                }
            }
            if($verify === true){
                return response()->json($escala_troca);
            }else{
                return '0';
            }
        } catch (\Throwable $th) {
            /** Retorno para a tela inicial com mensagem de erro */
            $request->session()->flash('alert-danger', 'Erro de conexão com o servidor.');
            return redirect('/');
        }
        
    }

    public function filtrarSolicitacoes(Request $request){
        if($request->ajax()){
            $parametros[0] = Carbon::parse($request->data)->format('Ymd');
            $parametros[1] = (int)$request->id_horario;
            $parametros[2] = (int)$request->skill;
            $solicitacoes = DB::select('exec StoProc_Consulta_Solicitacao_Troca_por_Skill_e_Horario ?,?,?', $parametros);
            // $solicitacoes = DB::table('solicitacao_troca')->join('escala','escala.id_escala','=','solicitacao_troca.id_escala_atual')->where('solicitacao_troca.data_troca',$parametros[0])->where('solicitacao_troca.id_horario_troca',$parametros[1])->where('escala.id_skill',$parametros[2])->get();
            if($solicitacoes){
                return response()->json($solicitacoes);
            }else{
                return '0';
            }
        }
    }



     /** FIM AJAX */

}


