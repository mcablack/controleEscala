@extends('adminlte::page')
@extends('common/navbar.nav_right')

@section('content_header') 
  <h1>Solicitação especial</h1>
@endsection

@section('content')
  {{-- Início do card 1 --}}
  <div class="card card-default">
    <div class="card-header">
      <h3 class="card-title">Formulário de solicitação especial</h3>
    </div>
    <div class="card-body">
      <div class="alert alert-info" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h6 class="alert-heading">Solicitação especial</h6>
        <hr>
        <p class="mb-0">Realize uma solicitação emergêncial para algum operador em específico e acompanhe o pedido na tabela abaixo!</p>
      </div>

      <form action="<?= url('/operador/solicitar/troca/casada') ?>" method="post" id="solicitarTrocaCasada">
        @csrf
        <div class="row">
          <div class="col-md-8">
            <label for="p_operador">Operador</label>
            <select name="p_operador" id="p_operador" class="form-control">
              <option value="" selected disabled >Selecione</option>
              <?php 
                if(!empty($operadores)){
                  foreach($operadores as $key => $value){
                    echo "<option value='".$value->id_usuario."'>".$value->id_usuario." - ".$value->nome_usuario."</option>";
                  }     
                }else{
                    echo "<option value=''>Supervisor não possui operadores cadastrados</option>";
                } 
              ?>
            </select>
          </div>

          <div class="col-md-4">
            <label for="data_solicitada">Data troca</label>
            <!--ajustarData impede do usuário burlar o campo de dala e escalaSolicitante busca os dados da escala do usuario referente ao dia selecionado -->
            <input type="date" onchange="ajustarData();escalaSolicitante();" onkeyup="" onblur="" class="form-control datepicker" name="data_solicitada" id="data_solicitada"  value=""/>
          </div>
        </div>
        
        {{-- <h6>Dados da escala - Ronaldinho Gaúcho Clóvis Abigail</h6> --}}
        {{-- <hr> --}}
        <div class="row my-2">
          <div class="col-md-4">
            <label for="data_escala">Data escala</label>
            <input type="text" class="form-control" id="data_escala"  readonly/>
            <input type="hidden" id="id_escala" name="id_escala">
          </div>
          <div class="col-md-4">
            <label for="turno_escala">Turno escala</label>
            <input type="text" class="form-control" id="turno_escala" readonly/>
            <input type="hidden" id="id_horario" name="id_horario">
          </div>
          
          <div class="col-md-4">
              <label for="skill_escala">Skill escala</label>
              <input type="text" class="form-control" id="skill_escala" name="" <?php if(isset($operador)){ ?> value="<?php echo $operador['skill']; ?>" <?php } ?>  readonly/>
          </div>
        </div> <!-- /.row -->
        {{-- <hr> --}}

        <div class="row my-2 mb-0">
          <div class="col-md-4">
            <label for="turno_troca">Turno troca</label>
            <select name='turno_troca' id='turno_troca'  class="form-control" >
              <option value="" selected disabled>Selecione</option>
              <?php 
                if(isset($turnos)){
                  foreach($turnos as $key=>$value){ ?>
                  <option value="<?= $value->id_horario ?>"><?= $value->horario_inicial.' - '.$value->horario_final ?></option>
              <?php } 
                } 
              ?>
            </select>
          </div>

          <div class="col-md-4">
            <label for="skill_troca">Skill troca</label>
            <select name="skill_troca" id="skill_troca" class="form-control selectpicker">
              <option value="" selected disabled>Selecione</option>
              <?php 
                if(!empty($skills)){
                  foreach($skills as $key => $value){
                    echo "<option value='".$value->id_skill."'>".$value->desc_skill."</option>";
                  }     
                }
              ?>
            </select>
          </div>

          <div class="col-md-4">
            <label for="status_troca">Status</label>
            <select name="status_troca" id="status_troca" class="form-control selectpicker">
              <option value="" selected disabled>Selecione</option>
              <?php 
                if(!empty($status)){
                  foreach($status as $key => $value){
                    echo "<option value='".$value->id_status_usuario."'>".$value->desc_status_usuario."</option>";
                  }     
                }
              ?>
            </select>
          </div>

          <div class="col-md-12 my-2">
            <label for="motivo">Motivo</label>
            <textarea id="motivo" class="form-control"></textarea>
          </div>

          <div class="col-md-2 my-2">
            <button type="button" class="btn btn-primary btn-block" onclick="validate()"  id="validar"> <i class="fas fa-plus-circle"></i> Solicitar troca</button>
          </div>
        </div> <!-- /.row -->
      </form>
    </div>
  </div> <!-- /.card-body --> 
  {{-- Fim do card 1 --}}

  {{-- <!-- início do card 2 --> --}}
  <div class="card card-default">
    <div class="card-header">
      <h3 class="card-title">Tabela de solicitações</h3>
    </div>
    <div class="card-body">
      <table class="table table-striped table-hover table-bordered" id="myTable" width="100%" table-layout:fixed>
        <thead>
          <tr>
            <th>Data</th>
            <th>Turno</th>
            <th>Skill</th>
            <th style="text-align: center;">Ações</th>
          </tr>
        </thead>

        <tbody id="myTable">
          <tr>
            <td>A</td>
            <td>B</td>
            <td>C</td>
            <td>D</td>
          </tr>
          <tr>
            <td>B</td>
            <td>E</td>
            <td>F</td>
            <td>G</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  {{-- <!-- fim do card 2 --> --}}

  {{-- Versão --}}
  <div style="color: #8c8b8b; display: flex; justify-content: flex-end;">Versão: 1.0.0</div>
@endsection

@section('footer')
  <div class="footer">
    Copyright © BS Tecnologia e Serviços - <?php echo date('Y'); ?>
  </div>
@endsection

@section('css')
  <link rel="stylesheet" href="{{asset('assets/calendario1/css/dataTables.min.css')}}">

  <style>
    /* Centralizar elementos td da table que possuam a classe tdCenter*/
    .tdCenter{
      text-align: center; vertical-align: middle;
    }
  </style>
@endsection

@section('js')
  <script src="{{asset('assets/calendario1/js/dataTables.min.js')}}"></script>
  <script src="{{asset('assets/calendario1/js/sweetalert.min.js')}}"></script>
  <script>

    // Função que executa os scripts quando a página é carregada
    $(document).ready(function() {
      // Função jquery relativa ao dataTable
      var table = $('#myTable').DataTable( {
        lengthMenu: [10, 15, 20, 30, 40, 50]
      });

      /* Função que limita a seleção de escolhas para o campo de data */
      $(function(){
        var today = dataAjustada();
        $('#data_solicitada').attr('min',today);
      });
    });

    function ajustarData(){
      var today = dataAjustada();
      var solicitada = $('#data_solicitada').val();
      if(solicitada < today){
        $('#data_solicitada').val(today);
      }
    }

    function dataAjustada(){
      var today = new Date();
      today.setDate(today.getDate() + 1);
      today = today.toISOString().split('T')[0];
      return today;
    }

    /* Funções para solicitação e aceitar solicitação*/
    function escalaSolicitante(){
        var dataSolicitada = $('#data_solicitada').val();
        var data = dataSolicitada.replace(/(\d*)-(\d*)-(\d*).*/, '$3/$2/$1');
        var p_operador = $('#p_operador').val(); 
        if(p_operador){
          $('#data_solicitada').attr('disabled',true);
          $.ajax({
            url: '/operador/get/escala/troca/'+ dataSolicitada +'/'+ p_operador,
            type: 'get',
            dataType: 'json',
            success: function(response){
              if(response != '0'){
                //Formatação da data para filtrar na tabela
                var data_escala = (response.data).replace(/(\d*)-(\d*)-(\d*).*/, '$3/$2/$1');

                $('#data_escala').val(data);
                $('#id_escala').val(response.id_escala);
                $('#id_horario').val(response.id_horario);
                $('#skill_escala').val(response.skill);
                $('#turno_escala').val(response.horario_inicial+' - '+response.horario_final);
                $('#skill_solicitada').val(response.skill);
                
                filtrarSolicitacoes(response.data,response.id_horario,response.id_skill);
              }else{
                swal.fire('Ops, algo deu errado', 'Operador não possui escala cadastrada para o dia: '+data+', impossibilitando solicitação de troca', 'error');
                //Reseta os valores para o filtro
                $('#skill_escala').val('');
                $('#turno_escala').val('');
                $('#data_solicitada').val('');
                $('#id_escala').val('');
              }
            },
            error: function(){
              swal.fire('Ops, algo deu errado', 'Sistema fora do ar!', 'error');
            },
            complete: function(){
              $('#data_solicitada').attr('disabled',false);
            }
          });
      }
    }

    function validate(){
      var erros = new Array();

      if($('#data_solicitada').val() == '' ){
        erros.push('Selecione uma data para realizar a troca  <br> ');
      }
      if($('#p_operador').val() == ''  || $('#p_operador').val() == null ){
        erros.push('Selecione um operador   <br> ');
      }
      if($('#turno_troca').val() == ''  || $('#turno_troca').val() == null){
        erros.push('Selecione um turno   <br> ');
      }
      if($('#skill_troca').val() == ''  || $('#skill_troca').val() == null){
        erros.push('Selecione uma skill   <br> ');
      }
      if($('#status_troca').val() == ''  || $('#status_troca').val() == null){
        erros.push('Selecione um status   <br> ');
      }
      
      if(erros.length > 0){
        var mensagem = erros.toString();
        var i = 0;
        console.log(mensagem)
        for(i=0;i<=erros.length;i++){
          mensagem = mensagem.replace(',','');
          if(i == erros.length){
            swal.fire("Os seguintes erros foram encontrados: ", mensagem, "error");
          }
        } 
      }else{
        $('#validar').attr('type','submit');
      }
    }

      
  </script>
  @endsection