@extends('adminlte::page')
@extends('common/navbar.nav_right')

@section('content_header') 
  <h1>Lista de colaboradores</h1>
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
      <h3 class="card-title">Escala dos colaboradores</h3>
    </div>
    <div class="card-body">
      <div class="row justify-content-md-center mb-3" style="text-align: center">
        <div class="col col-lg-2">
          <div class="legend-default legend-tc">TC</div> Troca casada
        </div>
        <div class="col col-lg-2">
          <div class="legend-default legend-se">SE</div> Sem escala <span style="color: transparent;">..</span>
        </div>
        <div class="col col-lg-2">
          <div class="legend-default legend-et">ET</div> Em treinamento.
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
          <div class="legend-default legend-fe">FE</div> Férias <span style="color: transparent;">...........</span>
        </div>
        <div class="col col-lg-2">
          <div class="legend-default legend-in">IN</div> Afastado inss
        </div>
        <div class="col col-lg-2">
          <div class="legend-default legend-af">AF</div> Afastado jurídico
        </div>
        <div class="col col-lg-2">
          <div class="legend-default legend-lm">LM</div> Licença maternidade
        </div> 
        <div class="col col-lg-2">
          <div class="legend-default legend-t">T</div> Trabalhando
        </div>
      </div>

      <table class="table table-striped table-hover table-bordered" id="myTable" width="100%" table-layout:fixed>
        <thead>
          <tr>
            <th>Editar</th>
            <th>Matrícula</th>
            <th>Nome</th>
            <th>Turno</th>
             @for($i=1;$i<=$dias;$i++)
              <th @if($i == date('d')) style='background: yellow' @endif>{{$i}}</th>
            @endfor
          
          </tr>
        </thead>
      
        <tbody>
          @foreach ($operador as $p => $escala)
            <tr>
              <td align="center">
                <a data-toggle="modal" onclick='openModal("{{$p}}")'> <i class="fas fa-edit fa-lg btn btn-primary" style='color: white;'></i> </a>
              </td>
              <td>{{$p}}</td>
              <td >{{$escala[0]->nome_operador}}</td> 
             
              <td class="tdCenter">{{$escala[0]->jornada_entrada.'  '.$escala[0]->jornada_saida}}</td>
              @foreach ($escala as $key => $value)
                <td class="tdCenter" style='background:{{$value->color}}'><b><font color="white">{{$value->codigo_status}}</font></b>
              </td>
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

  {{-- MODAL --}}
  <div class="modal" id="myModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content ">
        <div class="modal-header">
          <h4 class="modal-title">Edição de usuário</h4>
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        </div>

        <div class="modal-body">
          <form action="<?= url('/trafego/colaboradores/editar/') ?>" method="POST" id="formEditar">
            @csrf

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="nomeModal">Nome</label>
                  <input type="text" name="nomeModal" id="nomeModal" class="form-control" placeholder="Nome"/>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="matriculaModal">Matrícula</label>
                  <input type="text" name="matriculaModal" id="matriculaModal" class="form-control" placeholder="Matrícula"/>
                  <input type="hidden" name="matriculaHidden" id="matriculaHidden" class="form-control" />
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="turnoModal">Turno</label>
                  <input type="text" name="turnoModal" id="turnoModal" class="form-control" placeholder="Turno"/>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label for="skillModal">Skill</label>
                  <select name="skillModal" id="skillModal" class="form-control">
                    <option value="0" selected disabled>Selecionar...</option>
                    {{-- option retornando do banco com ajax --}}
                  </select>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label for="diaTroca">Dia da troca</label>
                  <input type="date" onchange="ajustarData();" onkeyup="" onblur="" class="form-control datepicker" name="data_solicitada" id="data_solicitada"  value="" required/>
                  <div class="invalid-feedback">
                    Por favor informe a data da troca.
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="statusModal">Status</label>
                  <select name="status" id="statusModal" class="form-control" required>
                    <option value="" selected disabled>Selecionar...</option>
                    <option value="1">Trabalho</option>
                    <option value="2">Folga</option>
                    <option value="3">Folga banco de horas</option>
                    <option value="4">Em treinamento</option>
                    <option value="5">Afastado inss</option>
                    <option value="6">Afastado jurídico</option>
                    <option value="7">Licença maternidade</option>
                    <option value="8">Férias</option>
                    <option value="9">Desligado</option>
                  </select>
                  <div class="invalid-feedback">
                    Por favor selecione um status.
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-success" id="btnSave">Salvar</button>
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
        "order": [ 2, "asc" ]
      });
      /* sDom:'lrtip' serve para esconder o campo de busca da tabela sem interferir na função de busca */
      
      // Submeter o formulário pelo botão do modal
      $("#btnSave").click(function() {
        $("#formEditar").submit();
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
      today.setDate(today.getDate());
      today = today.toISOString().split('T')[0];
      return today;
    }

    // Função para replicar do input para search do dataTable
    function filter(){
      var matricula = $('#matricula').val();
      var nome = $('#nome').val();
      var turno = $('#turnos').val();
      var status = $('#status').val();

      var table = $('#myTable').DataTable();
  
      table.search(matricula + ' ' +nome + ' ' + turno + ' ' + status).draw();
    }
    
    function openModal(matricula){
      var modal = $('#myModal');
      var matricula = matricula;

      // Executa a função 'editarColaboradores' e traz seu retorno
      $.getJSON('/trafego/colaboradores/editar/' + matricula , function(data){
        console.log(data);
        var entrada = data['user'][0].jorn_ent;
        var saida   = data['user'][0].jorn_sai;

        // Trazer campos do controller e exibir desabilitados no formulário do modal para edição
        $('#nomeModal').val(data['user'][0].nome).prop('disabled', true);
        $('#matriculaModal').val(data['user'][0].login).prop('disabled', true);
        $('#matriculaHidden').val(data['user'][0].login);
        $('#turnoModal').val(entrada.substr(0,5) + ' - ' +  saida.substr(0,5)).prop('disabled', true);

        // Criar options com skills retornadas do banco
        for(i = 0; i < data['skills'].length; i++){
          $('#skillModal').append($('<option>', {
            value: data['skills'][i].id_skill,
            text: data['skills'][i].desc_skill,
          }));

          // Verificar skill do usuário e retornar no input select
          if(data['user'][i].id_skill == i){
            $('option[value='+i+']').prop('selected', true);
          }
        }
      });

      modal.modal('show');
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
