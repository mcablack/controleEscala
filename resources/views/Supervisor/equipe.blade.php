@extends('adminlte::page')
@extends('common/navbar.nav_right')

@section('content_header') 
  <h5>Consolidado do dia</h5>
@endsection

@section('content')
  {{-- Pequenos cards informativos --}}
  <div class="row">
    <div class="col-lg-6 col-xs-6">
      <div class="small-box bg-green">
        <div class="inner">
          <h3> <?php echo $operadoresTrabalhando; ?></h3>
          <p>Escalados (hoje)</p>
        </div>
        <div class="icon">
          <i class="fas fa-users"></i>
        </div>
        {{-- <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> --}}
      </div>
    </div>

    <div class="col-lg-6 col-xs-6">
      <div class="small-box bg-red">
        <div class="inner">
          <h3> {{$operadoresFolgando}} </h3>
          <p>Folgando (hoje)</p>
        </div>
        <div class="icon">
          <i class="fas fa-user-slash"></i>
        </div>
        {{-- <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> --}}
      </div>
    </div>
  </div> 
  
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
                <label for="turnos">Turno Solicitação</label>
                <select name='turnos' id='turnos' onchange="filter()"  class="form-control">
                  <option value=""> </option>
                  <?php if(isset($turnos)){
                    foreach($turnos as $key=>$value){ ?>
                      <option value="<?= $value->horario_inicial?>"><?= $value->horario_inicial.' - '.$value->horario_final ?></option>
                    <?php }
                    }  
                  ?>
                </select> 
              </div> 

              <div class="col-md-3">
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
  </div> <!-- /.card-body --> 
  {{-- Fim do card 1 --}}
  
  {{-- Início do card 2 --}}
  <div class="card card-default">
    <div class="card-header">
      <h3 class="card-title">Escala da minha equipe</h3>
    </div>
    <div class="card-body">
      <div class="row justify-content-md-center mb-3" style="text-align: center">
        <div class="col col-lg-2">
          <div class="legend-default legend-tc">TC</div> Troca casada
        </div>
        <div class="col col-lg-2">
          <div class="legend-default legend-se">SE</div> Sem escala <span style="color: transparent;">...</span>
        </div>
        <div class="col col-lg-2">
          <div class="legend-default legend-et">ET</div> Em treinamento<span style="color: transparent;">..</span>
        </div>
        <div class="col col-lg-2">
          <div class="legend-default legend-fh">FH</div> Folga banco de horas
        </div>
        <div class="col col-lg-2">
          <div class="legend-default legend-f">F</div> Folgando <span style="color: transparent;">.....</span>
        </div>
      </div>
      <div class="row justify-content-md-center mb-3" style="text-align: center">
        <div class="col col-lg-2">
          <div class="legend-default legend-fe">FE</div> Férias <span style="color: transparent;">..............</span>
        </div>
        <div class="col col-lg-2">
          <div class="legend-default legend-in">IN</div> Afastado inss
        </div>
        <div class="col col-lg-2">
          <div class="legend-default legend-af">AF</div> Afastado jurídico
        </div>
        <div class="col col-lg-2">
          <div class="legend-default legend-lm">LM</div> Licença maternidade<span style="color: transparent;">.</span>
        </div> 
        <div class="col col-lg-2">
          <div class="legend-default legend-t">T</div> Trabalhando
        </div>
      </div>
      

      <table class="table table-striped table-hover table-bordered" id="myTable" width="215%" table-layout:fixed>
        <thead>
          <tr>
            <th >Calendário</th>
            <th >Matricula</th>
            <th style="width: 250px;">Nome</th>
            <th >Turno</th>
            @for($i=1;$i<=$dias;$i++)
              <th @if($i == date('d')) style='background: yellow' @endif>{{$i}}</th>
            @endfor
          </tr>
        </thead>
      
        <tbody>
          @foreach ($operador as $p => $escala)
            <tr>
              <td align="center"><i  class="far fa-calendar-alt fa-lg btn btn-primary" onclick="calendario('{{$p}}');" style='color: white;'></i></td>
              <td>{{$p}}</td>
              <td >{{$escala[0]->nome_operador}}</td> 
              <td class="tdCenter">{{$escala[0]->jornada_entrada.'-'.$escala[0]->jornada_saida}}</td>
              @foreach ($escala as $key => $value)
                <td class="tdCenter" style='background:{{$value->color}}'><b><font color="white">{{$value->codigo_status}}</font></b></td>
              @endforeach
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
    .legend-default{
      padding: 4px; background-color: #28a746; display: inline; font-weight: bold; color: white;
    }
    .legend-t{  background-color: #28a746; }
    .legend-f{  background-color: #666666; }
    .legend-et{ background-color: #007bff; }
    .legend-tc{ background-color: #1E90FF; }
    .legend-in{ background-color: #fc8c1c; }
    .legend-fh{ background-color: #808080; }
    .legend-af{ background-color: #fc8c1c; }
    .legend-fe{ background-color: #bd5353; }
    .legend-se{ background-color: #778899; }
    .legend-lm{ background-color: #FFB6C1; }
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
        lengthMenu: [10, 15, 20, 30, 40, 50],
        "order": [ 2, "asc" ]
      });
      
      /* sDom:'lrtip' serve para esconder o campo de busca da tabela sem interferir na função de busca */
    });

    // Função para replicar do input para search do dataTable
    function filter(){
      var matricula = $('#matricula').val();
      var nome = $('#nome').val();
      var turno = $('#turnos').val();
      var status = $('#status').val();

      var table = $('#myTable').DataTable();
  
      table.search(matricula + ' ' +nome + ' ' + turno + ' ' + status).draw();
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
