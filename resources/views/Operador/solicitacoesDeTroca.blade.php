@extends('adminlte::page')
@extends('common/navbar.nav_right')

@section('content_header') 
  <h1> Minhas solicitações de troca</h1>
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
            <div class="col-md-6">

              <label for="data">Data</label>
              <input id='data' class="form-control" onchange="filter()" type="date">
            </div>
            
            <div class="col-md-6">
              <label for="status">Status</label>
              <input id='status' class="form-control" onkeyup="filter()" type="text">
            </div>
            <div class="col-md-2 my-3">
              <button type="button" class="btn btn-default btn-block" onclick="clean()"><i class="fas fa-eraser" style="color: red"></i> Limpar</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> 

  <div class="card card-default">
    <div class="card-header">
      <h3 class="card-title">Minhas solicitações</h3>
    </div>
    <div class="card-body">
      <table class="table table-striped table-hover table-bordered" id="myTable" width="100%" table-layout:fixed>
        <thead>
          <tr>
            <th>Data solicitada</th>
            <th>Turno solicitado</th>
            <th>Skill</th>
            <th>Status</th>
          </tr>
        </thead>

        <tbody>
          <?php foreach($solicitacoes as $key=>$value){?> 
        <tr
        >   <td><?= formatarData($value->data_troca,'d/m/Y') ?></td>
            <td><?php if($value->id_solicitante_troca == $login){  echo $value->horario_inicial_escala_troca.' - '.$value->horario_final_escala_troca; }else{ echo $value->horario_inicial_escala_atual.' - '.$value->horario_final_escala_atual; } ?></td>
            <td ><?= $value->desc_skill ?></td>

            <?php if($value->id_status_autorizacao_tab_autorizacao == '1'){ ?>
            <td align="center" >
              <div class="progress">
              <div class="progress-bar  progress-bar-striped bg-warning" role="progressbar" style="width: 60%;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"><?php echo $value->nome_supervisor.' - '.$value->desc_status_autorizacao_tab_autorizacao ?> </div>
                <div class="progress-bar progress-bar-striped bg-warning" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">Supervisor 2 - Pendente</div>
              </div>
            </td>
            <?php }elseif($value->id_status_autorizacao_tab_autorizacao == '3'){ ?>
              <td align="center" >
              <div class="progress">
                <div class="progress-bar progress-bar-striped bg-success" ole="progressbar" style="width: 100%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">Solicitação Aprovada</div>
              </div>
              </td>
            <?php }elseif($value->id_status_autorizacao_tab_autorizacao == '4'){ ?>
              <td align="center" >
               </td>
            <?php } ?>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>

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

    .bg-gradient{
      background: linear-gradient( to right, #ffc107, #198754 );
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
      $('#myTable').DataTable( {
        "scrollX": true,
        sDom:'lrtip',
        lengthMenu: [10, 15, 20, 30, 40, 50],
        "order": [[ 0, "desc" ], [1, 'desc']]
      });
    });

    // Função para replicar do input para search do dataTable
    function filter(){
      var data = ($('#data').val()).replace(/(\d*)-(\d*)-(\d*).*/, '$3/$2/$1');
      var status = $('#status').val();
      var table = $('#myTable').DataTable();
      table.search(data + ' ' + status).draw();
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
