@extends('adminlte::page')
@extends('common/navbar.nav_right')

@section('content_header')
  <h1>Solicitação de troca</h1>
@endsection

@section('content')
  <div class="card card-default">
    <div class="card-header">
      <h3 class="card-title">Formulário de solicitação de troca</h3>
    </div> <!-- /.card-header -->

    <div class="card-body">
      <div class="alert alert-info" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h5 class="alert-heading">Pedido de troca</h5>
        <hr>
        <p class="mb-0">Solicite sua troca informando a data para troca ou case o pedido com alguma solicitação existente na tabela abaixo</p>
      </div>
      <form action="<?= url('/operador/solicitar/troca/casada') ?>" method="post" id="solicitarTrocaCasada" enctype="multipart/form-data">
        @csrf
        <div class="row">
          <div class="col-md-6">
            <label>Matricula</label>
            <input type="text" class="form-control" <?php if(isset($operador)){ ?> value="<?php echo $operador['matricula']; ?>" <?php } ?>  readonly/>
          </div>

          <div class="col-md-6">
            <label>Supervisor</label>
            <input type="text" class="form-control" name="" <?php if(isset($operador)){ ?> value="<?php echo $operador['nome_supervisor']; ?>" <?php } ?>  readonly/>
          </div>
        </div>
        
        <div class="row my-3">
          <div class="col-md-4">
            <label>Data Escala</label>
            <input type="text" class="form-control" id="data_escala"  readonly/>
            <input type="hidden" id="id_escala" name="id_escala">
          </div>
          <div class="col-md-4">
            <label>Turno Escala</label>
            <input type="text" class="form-control" id="turno_escala" readonly/>
            <input type="hidden" id="id_horario" name="id_horario">
          </div>
          
          <div class="col-md-4">
              <label>Skill Escala</label>
              <input type="text" class="form-control" id="skill_escala" name="" <?php if(isset($operador)){ ?> value="<?php echo $operador['skill']; ?>" <?php } ?>  readonly/>
          </div>
        </div> <!-- /.row -->

        <div class="row">
          <div class="col-md-4">
            <label>Data Troca</label>
            <!--ajustarData impede do usuário burlar o campo de dala e escalaSolicitante busca os dados da escala do usuario referente ao dia selecionado -->
            <input type="date" onchange="ajustarData();escalaSolicitante();" onkeyup="" onblur="" class="form-control datepicker" name="data_solicitada" id="data_solicitada"  value=""/>
          </div>

          <div class="col-md-4">
            <label for="turno">Turno Troca</label>
            <select name='turno_solicitado' id='turno_solicitado'  class="form-control" >
              <option value="0" selected disabled>Selecionar...</option>
              <?php if(isset($turnos)){
                  foreach($turnos as $key=>$value){ ?>
                  <option value="<?= $value->id_horario ?>"><?= $value->horario_inicial.' - '.$value->horario_final ?></option>
                <?php } 
                  } ?>
            </select>
          </div>

          <div class="col-md-4">
            <label>Skill Troca </label>
            <input type="text" class="form-control" name="skill_escala" id="skill_solicitada" value="" readonly/>
          </div>

          <div class="col-md-2 my-3">
            <button type="button" class="btn btn-primary btn-block" onclick="solicitarTrocaCasada()"  id="validar"> <i class="fas fa-plus-circle"></i> Solicitar troca</button>
          </div>
        </div> <!-- /.row -->
      </form>
    </div><!-- /.card-body -->
  </div>
  <!-- fim do card 1 -->

  <!-- início do card 2 -->
  <div class="card card-default">
    <div class="card-header">
      <h3 class="card-title">Tabela de trocas</h3>
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

        <tbody id="myTableBody">
          {{-- DataTable js --}}
        </tbody>
      </table>
    </div>
  </div>
  <!-- fim do card 2 -->

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
    // Swal para mensagens de retorno
    <?php if(session("Alert")){ ?>
      swal.fire('Pedido enviado', '{{ session('Alert') }}', 'success');
      <?php session()->put('Alert',''); 
    }elseif(session("AlertError")){ ?>
      swal.fire('Ocorreu um error', '{{ session('AlertError') }}', 'error');
      <?php session()->put('AlertError',''); 
    }?>

    // Função que executa os scripts quando a página é carregada
    $(document).ready(function() {
      // Função jquery relativa ao dataTable
      var table = $('#myTable').DataTable( {
        sDom:'lrtip',
        lengthMenu: [10, 15, 20, 30, 40, 50]
      });

      /* Função que limita a seleção de escolhas para o campo de data */
      $(function(){
        var today = dataAjustada();
        $('#data_solicitada').attr('min',today);
        //$('#data_solicitada').val(today);
        //escalaSolicitante();
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
      today.setDate(today.getDate() + 2);
      today = today.toISOString().split('T')[0];
      return today;
    }

    /* Funções para solicitação e aceitar solicitação*/
    function escalaSolicitante(){
      var dataSolicitada = $('#data_solicitada').val();
      var data = dataSolicitada.replace(/(\d*)-(\d*)-(\d*).*/, '$3/$2/$1');
      $('#data_solicitada').attr('disabled',true);
      $.ajax({
        url: 'get/escala/troca/'+ dataSolicitada,
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

    function filtrarSolicitacoes(data,id_horario,skill){
      //Verifica se existe solicitação para não bugar a dataTable
      $.ajax({
        url:"/operador/get/solicitacoes/troca",
        method:'POST',
        data:{ 'data': data,'id_horario':id_horario,'skill':skill}, 
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response){
          var table=$('#myTable');
          if(response !== '0'){
            montarTabela(response);
          }else{
            $('#myTable tbody').empty();
            table.DataTable().destroy();
            table.DataTable( {
              sDom:'lrtip',
              lengthMenu: [10, 15, 20, 30, 40, 50]
            });
          }
        }
      });

    }

    function montarTabela(solicitacoes){
      var tbody = $('#myTable tbody');
      tbody.empty();
      for(var i = 0; i<solicitacoes.length;i++){
        
        var tr = $('<tr>');
        tr.append($('<td>'+formatarData(solicitacoes[i].data_troca)+'</td>'));
        tr.append($('<td>'+solicitacoes[i].tuno+'</td>'));
        tr.append($('<td>'+solicitacoes[i].desc_skil+'</td>'));
        tr.append($('<td align="center"><button type="button" class="btn btn-primary" onclick="aceitarSolicitacao('+solicitacoes[i].id_solicitacao_troca+','+solicitacoes[i].id_horario+')"> <i class="fas fa-check-circle"></i> Aceitar troca</button></td>'));
        tr.append($('</tr>'));

        tbody.append(tr);
      }
    }

    function formatarData(data){
      return data.replace(/(\d*)-(\d*)-(\d*).*/, '$3/$2/$1');
    }

    function solicitarTrocaCasada(){
      var data=$('#data_solicitada').val();
      var turno=$('#turno_solicitado').val();
      var skill=$('#skill_solicitada').val();
      var id_horario=$('#id_horario').val();

      if(data && turno && skill){
        if(turno == id_horario){
          swal.fire('Atenção', 'O turno solicitado é o mesmo da escala solicitante', 'warning');
        }else{
          $('#validar').attr('type', 'submit');  
        }
        
      }else{
        swal.fire('Atenção', 'Só é possivel solicitar uma troca informando uma data e um turno', 'info');
      }
    }

    function aceitarSolicitacao(id_solicitacao_troca,id_horario_solicitante){
      var dataSolicitada = $('#data_solicitada').val();
      var id_escala = $('#id_escala').val();
      var id_horario = $('#id_horario').val();
      if(!dataSolicitada){
        swal.fire('Atenção', 'Selecione uma data no campo data solicitação', 'info');
      }else{
        swal.fire({
        title: 'Deseja continuar?',
        text:'Não será possivel reverter a operação',
        icon:'warning',
        showCancelButton: true,
        confirmButtonText: `Confirmar`,
        }).then((result) => {
          /* Read more about isConfirmed, isDenied below */
          if (result.isConfirmed) {
            $.ajax({
              url:"/operador/aceitar/troca/casada",
              method:'POST',
              data:{ 'data': dataSolicitada,'id_solicitacao':id_solicitacao_troca,'turno_solicitado':id_horario,'turno_troca':id_horario_solicitante,'id_escala': id_escala}, 
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              success: function(data){
                if(data == '1'){
                  window.location.href = "/operador/minhassolicitacoes";
                }else{
                  swal.fire('Erro','Favor validar dados da escala para o dia solicitado!','error');
                }
              }
            }); // fim do ajax
          }
        })
      }
    }
  </script>
@endsection
