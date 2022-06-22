@extends('adminlte::page')
@extends('common/navbar.nav_right')

@section('content_header') 
  <h1> Coordenadores </h1>
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
              <div class="col-md-4">
                <label for="matricula">Matrícula</label>
                <input id='matricula' class="form-control" onkeyup="filter()" type="text" maxlength="8">
              </div>

              <div class="col-md-4">
                <label for="nome">Nome</label>
                <input id='nome' class="form-control" onkeyup="filter()" type="text">
              </div>
            
              <div class="col-md-4">
                <label for="status">skill</label>
                <input id='status' class="form-control" onkeyup="filter()" type="text">
              </div>
              
              <div class="col-md-2 my-4">
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
      <h3 class="card-title">Tabela de Coordenadores</h3>
    </div>
    <div class="card-body">
      <table class="table table-striped table-hover table-bordered" id="myTable" width="100%" table-layout:fixed>
      <thead>
          <tr>
            <th style="width: 70px;">Matricula</th>
            <th style="width: 700px;">Nome</th>
            <th style="width: 120px;">Skill</th>
            <th style="text-align: center;">Supervisores</th>
          </tr>
        </thead>

        <tbody>
          @foreach ($funcionario_coord as $key => $value)
            <tr>
              <td>{{$value->login}}</td>
              <td>{{$value->nome}}</td>
              <td>{{$value->perfil}}</td>
              <td align="center">
                <button   type="button" onclick="equipe('{{$value->login}}')" class="btn btn-primary"><i class="fas fa-users"></i> </button>
              </td>
            </tr>
          @endforeach
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
        sDom:'lrtip',
        lengthMenu: [10, 15, 20, 30, 40, 50],
        "order": [ 1, "asc" ]
      });
      
      /* sDom:'lrtip' serve para esconder o campo de busca da tabela sem interferir na função de busca */
    });

    // Função para replicar do input para search do dataTable
    function filter(){
      var matricula = $('#matricula').val();
      var nome = $('#nome').val();
     var status = $('#status').val();

      var table = $('#myTable').DataTable();
  
      table.search(matricula+ ' ' +nome  + ' ' +status).draw();
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
        url:"/operadorescala",
        method:'POST',
        data:{ 'id': p}, 
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data){
          window.location.href = "/operadorescala/"+p;
        }
      }); // fim do ajax
    }

    function equipe(p){
      $.ajax({
        url:"/coordenador/operadorestrafego/",
        method:'POST',
        data:{ 'id': p}, 
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data){
          window.location.href = "/coordenador/operadorestrafego/"+p;
        }
      }); // fim do ajax
    }
  </script>
@endsection
