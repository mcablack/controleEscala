@extends('adminlte::page')
@extends('common/navbar.nav_right')

@section('content_header')
  <h1>Equipe do coordenador: <?= $coordenador['nome'] ?></h1>
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
          <!--    <div class="col-md-3">
               <label for="
               ">Turno</label>
                <input id='turno' class="form-control" onkeyup="filter()" type="time">
              </div>
          -->
           <!-- **** VERIFICAR OS HORÁRIOS DO SUPERVIDOR FUTURAMENTE ******
            <div class="col-md-4">
            <label for="turno">Turno Solicitação</label>
            <select name='turno_super' id='turno_super' onchange="filter()"  class="form-control">
              <option value=""> </option>
              <?php if(isset($turnos)){
                foreach($turnos as $key=>$value){ ?>
                  <option value="<?= $value->horario_inicial ?>"><?= $value->horario_inicial ?></option>
                <?php }
                }  ?>
            </select>
          </div> -->

              <div class="col-md-4">
                <label for="funcao">Função</label>
                <input id='funcao' class="form-control" onkeyup="filter()" type="text">
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
      <h3 class="card-title">Equipe de supervisores</h3>
    </div>
    <div class="card-body">
      <table class="table table-striped table-hover table-bordered" id="myTable" widht="100%" table-layout:fixed>
        <thead>
          <tr>
            <th>Matricula</th>
            <th style="width: 250px;">Nome</th>
            <th>Turno</th>
            <th>Função</th>
            <th align="center">Operadores</th>
          </tr>
        </thead>

        <tbody>
          @foreach ($supervisores as $key => $value)
            <tr>
              <td>{{$value->login_supervisor}}</td>
              <td>{{$value->supervisor}}</td>
              <td class="tdCenter">{{$value->jornada_entrada_sup.' - '.$value->jornada_saida_sup}}</td>
              <td>{{$value->desc_funcao_supervisor}}</td>
              <td align="center">
                <button type="button" onclick="equipe('{{$value->login_supervisor}}')" class="btn btn-primary"> <i class="fas fa-users"></i> </button>
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
        lengthMenu: [10, 15, 20, 30, 40, 50],
        sDom:'lrtip',
        "order": [ 1, "asc" ]
      });
      
      /* sDom:'lrtip' serve para esconder o campo de busca da tabela sem interferir na função de busca */
    } );

    // Função para replicar do input para search do dataTable
    function filter(){
      var matricula = $('#matricula').val();
      var nome = $('#nome').val();
      var turno = $('#turno_super').val();
      var funcao = $('#funcao').val();

      var table = $('#myTable').DataTable();
  
      table.search(matricula + ' ' +nome + ' ' + funcao).draw();
    }

    // Função para limpar o campo search do dataTable 
    function clean(){
      $('#filter').find('input,select').each(function(index,item){
        $(item).val('');
      });
      var table = $('#myTable').DataTable();
      table.search('').draw();
    }

    function equipe(p){
      $.ajax({
        url:"/surpevisor/supervisorequipe/",
        method:'POST',
        data:{ 'id': p}, 
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data){
          window.location.href = "/surpevisor/supervisorequipe/"+p;
        }
      }); // fim do ]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]
    }
  </script>
@endsection
