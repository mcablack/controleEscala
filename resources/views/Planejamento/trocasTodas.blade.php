@extends('adminlte::page')
@extends('common/navbar.nav_right')

@section('content_header') 
  <h1>Solicitações de troca</h1>
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
                <label for="status">Status</label>
                <input id='status' class="form-control" onkeyup="filter()" type="text">
              </div>

              <div class="col-md-3">
            <label for="turnos">Turno solicitado</label>
            <select name='turnos' id='turnos' onchange="filter()"  class="form-control">
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
      <h3 class="card-title">Lista de trocas</h3>
    </div>
    <div class="card-body">
      <table class="table table-striped table-hover table-bordered" id="myTable" width="100%" table-layout:fixed>
        <thead>
          <tr>
            <th>Calendário</th>
            <th>Matricula</th>
            <th style="width: 250px;">Nome</th>
            <th>Data solicitada</th>
            <th>Turno solicitado</th>
            <th style="text-align: center">Skill</th>
            <th style="text-align: center">Status</th>
          </tr>
        </thead>

        <tbody>
        <?php foreach($solicitacoesTodas as  $solictacoes=>$value){?>
          <tr>
              <td align="center"><i  class="far fa-calendar-alt fa-lg btn btn-primary" onclick="calendario('<?= $value->id_usuario_cad_solicitacao?>');" style='color: white;'></i></td>
             <td><?= $value->id_usuario_cad_solicitacao?></td>
            <td><?= $value->nome_usuario_cad_solicitacao ?></td>
            <td><?= formatarData($value->data_troca,'d/m/Y')?> </td>
            <td align="center"><?= $value->horario_inicial_escala_atual.'-'.$value->horario_final_escala_atual ?></td>
            <td><?= $value->desc_skil ?></td>
            <td align="center" style="width: 180px;">
              @if($value->id_troca_2)
                <a data-toggle="modal" href="#myModal" class="btn btn-success" style="width: 210px;"> Pendente de Autorização </a>
              @else
                <a data-toggle="modal" href="#myModal" class="btn btn-secondary">  Aguardando Aceitação </a>
              @endif
            </td>
          </tr>
          <?php } ?>
        </tbody>

      </table>
    </div>
  </div>
  {{-- Fim do card 2 --}}

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

  <script>
    // Função que executa os scripts quando a página é carregada
    $(document).ready(function() {
      // Função jquery relativa ao dataTable
      $('#myTable').DataTable( {
        "scrollX": true,
        sDom:'lrtip',
        lengthMenu: [10, 15, 20, 30, 40, 50]
      });  
      /* sDom:'lrtip' serve para esconder o campo de busca da tabela sem interferir na função de busca */
    });

    // Função para replicar do input para search do dataTable
    function filter(){
      var matricula = $('#matricula').val();
      var nome = $('#nome').val();
      var turnos = $('#turnos').val();
      var status = $('#status').val();

      var table = $('#myTable').DataTable();
  
      table.search(matricula + ' ' +nome + ' ' + turnos + ' ' + status).draw();
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

    // Função para replicar do input para search do dataTable
    function filter(){
      var matricula = $('#matricula').val();
      var nome = $('#nome').val();
      var turnos = $('#turnos').val();
      var status = $('#status').val();

      var table = $('#myTable').DataTable();
  
      table.search(matricula + ' ' +nome + ' ' + turnos + ' ' + status).draw();
    }

    // Função para limpar o campo search do dataTable 
    function clean(){
      $('#filter').find('input,select').each(function(index,item){
        $(item).val('');
      });
      var table = $('#myTable').DataTable();
      table.search('').draw();
    }
  </script>
@endsection
