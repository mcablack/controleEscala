@extends('adminlte::page')
@extends('common/navbar.nav_right')

@section('content_header') 
  <h1>Trocas pendentes</h1>
@endsection

@section('content')
  {{-- Início do card 1 --}}
  <div class="card card-default">
    <div class="card-header">
      <h3 class="card-title">Filtros</h3>
    </div>

    <div class="card-body">
      <div class="row">
        <div class="col-md-12">
          <div class="row" id='filter'>
              <div class="col-md-3">
                <label for="matricula">Matrícula</label>
                <input id='matricula' class="form-control" onkeyup="filter()" type="text" maxlength="8">
              </div>

              <div class="col-md-3">
                <label for="nome">Nome</label>
                <input id='nome' class="form-control" onkeyup="filter()" type="text">
              </div>

              <div class="col-md-3">
                <label for="skill">Skill</label>
                <input id='skill' class="form-control" onkeyup="filter()" type="text">
              </div>
              
              <div class="col-md-3">
                <label for="turnos">Turno solicitado</label>
                <select name='turnos' id='turnos' onchange="filter()" class="form-control">
                  <option value=" z"> </option>
                  <?php if(isset($turnos)){
                  foreach($turnos as $key=>$value){ ?>
                    <option value="<?= $value->horario_inicial?>"><?= $value->horario_inicial.' - '.$value->horario_final ?></option>
                  <?php }
                  }  ?>
                </select> 
              </div>

              <div class="col-md-2 my-3">
                <button type="button" class="btn btn-default btn-block" onclick="clean()"><i class="fas fa-eraser" style="color: red"></i> Limpar</button>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div> <!-- /.card-body --> 
  {{-- Fim do card 1 --}}
  
  {{-- Início do card 2 --}}
  <div class="card card-default">
    <div class="card-header">
      <h3 class="card-title">Lista de trocas pendentes</h3>
    </div>
    <div class="card-body">
      <table class="table table-striped table-hover table-bordered" id="myTable" width="100%" table-layout:fixed>
        <thead>
          <tr>
            <th class="tdCenter">Matricula</th>
            <th style="width: 250px;" class="tdCenter">Nome</th>
            <th class="tdCenter">Data</th>
            <th class="tdCenter">Turno</th>
            <th class="tdCenter">Skill</th>
            <th class="tdCenter">Ações/Status</th>
          </tr>
        </thead>

        <tbody>
          <?php $cont=0; foreach ($solicitacoes as $key => $value) {  ?>
              <tr id="tr-<?=$cont?>">
                <td class="tdCenter"><?= $value->id_solicitante_troca ?></td>
                <td class="tdCenter"><?= $value->nome_solicitante_troca  ?></td>
                <td class="tdCenter"><?= formatarData($value->data_troca,'d/m/Y')  ?></td>
                <td class="tdCenter"><?= $value->horario_inicial_escala_atual.' - '.$value->horario_final_escala_atual  ?></td>
                <td class="tdCenter"><?= $value->desc_skill  ?></td>
                <td class="tdCenter" ><a data-toggle="modal" onclick='openModal(<?= json_encode($value) ?>,<?= $cont ?>)' class="btn btn-primary"> <i class="fas fa-eye" style="color: #FFF" ></i></a>
                   <!--   <button class="btn btn-success"> <i class="fas fa-eye"></i> Aceitar</button>
                    <button class="btn btn-danger"> <i class="fas fa-times-circle"></i> Recusar</button> --></td>
              </tr>
          <?php $cont++; } ?>
        </tbody>
      </table>
    </div>
  </div>
  {{-- Fim do card 2 --}}

  {{-- Versão --}}
  <div style="color: #8c8b8b; display: flex; justify-content: flex-end;">Versão: 1.0.0</div>

  {{-- MODAL --}}
  <div class="modal" id="myModal">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Informações da Troca</h4>
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        </div>
        <div class="modal-body" id="body_operador_1">
          <div class="card-header">
            <h2 class="card-title" id="nome_supervisor_1">Supervisor1</h2>
          </div>
  
          <div class="card-body">
            <table class="table table-striped table-hover table-bordered" id="operador_1" width="100%" table-layout:fixed>
              <thead>
                <tr>
                  <th>Matricula</th>
                  <th style="width: 300px;">Nome</th>
                  <th>Tipo da Troca</th>
                  <th>Data</th>
                  <th >Turno</th>
                  <th align="center" style="width: 250px;">Ação/Status</th>
                </tr>
              </thead>
              <tbody>

               {{--  <tr>
                  <td>P536879</td>
                  <td>Alfredo</td>
                  <td>Troca Casada</td>
                  <td class="tdCenter">20/11/2020</td>
                  <td class="tdCenter">14:00 - 20:00</td>
                  <td style="width: 250px;" class="tdCenter">
                    <button class="btn btn-success"> <i class="fas fa-check-circle"></i> Aceitar</button>
                    <button class="btn btn-danger"> <i class="fas fa-times-circle"></i> Recusar</button>
                  </td>
                </tr> --}}
              
              </tbody>
            </table>
          </div>
        </div>

  <div class="modal-body" id="body_operador_2">
      <div class="card-header">
            <h2 class="card-title" id="nome_supervisor_2">Supervisor2</h2>
        </div>
          <div class="card-body">
            <table class="table table-striped table-hover table-bordered" id="operador_2" width="100%" table-layout:fixed>
              <thead>
                <tr>
                  <th>Matricula</th>
                  <th style="width: 300px;">Nome</th>
                  <th>Tipo da Troca</th>
                  <th>Data</th>
                  <th>Turno</th>
                  <th align="center" style="width: 250px;">Ação/staus</th>
                </tr>
              </thead>
              <tbody> 
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  {{-- FIM DO MODAL --}}
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
      $('#myTable').DataTable( {
        sDom:'lrtip',
        lengthMenu: [10, 15, 20, 30, 40, 50],
        "order": [[ 2, "asc" ], [3, 'desc']]
      });
      
      /* sDom:'lrtip' serve para esconder o campo de busca da tabela sem interferir na função de busca */
    });

    //Variaveis globais
    var rowSolicitacao = 0;

    // Função para replicar do input para search do dataTable
    function filter(){

      var matricula = $('#matricula').val();
      var nome = $('#nome').val();
      var table = $('#myTable').DataTable();
  
      table.search(matricula + ' ' +nome).draw();
    }

    function openModal(solicitacao,index){
      rowSolicitacao = index;
      var modal = $('#myModal');
      var id_relacionamento = solicitacao.id_relacionamento_troca;
      montarTabelaOperador(solicitacao,1);

      $.getJSON('/surpevisor/get/solicitacao/relacionamento' + '/' + id_relacionamento + '/' + solicitacao.id_solicitacao_troca , function(solicitacao_troca){
        console.log(solicitacao_troca);
        if(solicitacao_troca != '0'){
          montarTabelaOperador(solicitacao_troca,2);
          modal.modal('show');
        }else if(solicitacao_troca != '404'){
          $('#body_operador_2').hide();
          modal.modal('show');
        }else{
          swal.fire('Erro','Ocorreu um erro inesperado, atualize a página e tente novamente','error');
        }
      });
    }

    function montarTabelaOperador(solicitacao,indice){
      // indice indica para qual operador a tabela será montada

      $('#nome_supervisor_'+indice).html('SUPERVISOR: <strong>'+solicitacao.nome_supervisor+'</strong>');

      var tableBody = $('#operador_'+indice+' > tbody');
      tableBody.empty();

      var tr = $('<tr>');
        tr.append($('<td>'+solicitacao.id_solicitante_troca+'</td>'));
        tr.append($('<td>'+solicitacao.nome_solicitante_troca+'</td>'));
        tr.append($('<td>'+solicitacao.desc_tipo_troca+'</td>'));
        tr.append($('<td>'+formatarData(solicitacao.data_troca)+'</td>'));
        tr.append($('<td>'+solicitacao.horario_inicial_escala_atual +' - '+solicitacao.horario_final_escala_atual+'</td>'));
        if(indice == 1){
          tr.append($('<td style="width: 250px;"  class="tdCenter"><button class="btn btn-success" onclick="acaoSolicitacao('+solicitacao.id_solicitacao_troca+',1)"> <i  class="fas fa-check-circle"></i> Aceitar</button> &nbsp <button class="btn btn-danger" onclick="acaoSolicitacao('+solicitacao.id_solicitacao_troca+',0)"> <i  class="fas fa-times-circle"></i> Recusar</button></td>'));
        }else{
          tr.append($('<td style="width: 250px;" class="tdCenter"><button class="btn btn-primary">'+solicitacao.desc_status_autorizacao+'</button></td>'));
        }
        
      tableBody.append(tr);

       return true;

    }

    function formatarData(data){
      return data.replace(/(\d*)-(\d*)-(\d*).*/, '$3/$2/$1');
    }

    function acaoSolicitacao(id_solicitacao,tipo){
      //tipo diz a natureza da operação, 1: aceitar, 0: recusar
     
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
            url:"/surpevisor/acaoSolicitacao",
            method:"POST",
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:{'id_solicitacao':id_solicitacao,'tipo':tipo},
            success: function(response){
              //console.log(response);
              if(response != '0'){
                
                if(tipo == '1'){
                  swal.fire('Aceita','Solicitação de troca aceita com sucesso!','success');
                }else{
                  swal.fire('Recusada','Solicitação de troca recusada','error');
                }
                
                //Para remover uma linha e ela não retornar 
                $('#myTable').DataTable().destroy();
                $('#tr-'+rowSolicitacao).remove();
                $('#myTable').DataTable( {
                  sDom:'lrtip',
                  lengthMenu: [10, 15, 20, 30, 40, 50]
                });
                $('#myModal').modal('hide');
              
              } else {
                swal.fire('Atenção','Erro de conexão com o servidor!','error');
                
              }
            }
          });
        }
      })

      return false;
    }
    
    // Função para limpar o campo search do dataTable 
    function clean(){
      $('#filter').find('input,select').each(function(index,item){
        $(item).val('');
      });
      var table = $('#myTable').DataTable();
      table.search('').draw();
    }

    //Função que leva supervisor ao calendário do operador
    function calendario(p){
      $.ajax({
        url:"/operador/operadorescala",
        method:'POST',
        data:{ 'id': p}, 
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data){
          window.location.href = "/operador/operadorescala/"+p;
        }
      }); // fim do ajax
    }
  </script>
@endsection
