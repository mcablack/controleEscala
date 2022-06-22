@extends('adminlte::page')
@extends('common/navbar.nav_right')

@section('content_header')
  <h1>Importar escala</h1>
@endsection

@section('content')
  {{-- Início do card 1 --}}
  <div class="card card-default">
    <div class="card-body">

      {{-- Formulário --}}
      <form method="POST" action="{{ url('/importacaoescala') }}" enctype="multipart/form-data" id="uploadForm">
        @csrf

        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="escala">Enviar arquivo</label>
              <input type="file" class="form-control-file escala" name="escala" id="escala" aria-describedby="escalaSmall" required>
              <small id="escalaSmall" class="form-text text-muted">
                Só é possível fazer a importação de arquivos .xls
              </small>
            </div>
          </div>
          <div class="col-md-2">
            <button class="btn btn-primary" type="submit" id="submitButton"><i class="fas fa-file-upload"></i> Importar</button>
          </div>
        </div>
      </form>

      {{-- Barra de progresso ao enviar --}}
      <div class='progress' id="progressDivId">
        <div class='progress-bar progress-bar-striped' id='progressBar'></div>
        <div class='percent' id='percent'>0%</div>
      </div>
      <div style="height: 10px;"></div>
      <div id='outputImage'></div>
    </div> <!-- /.card-body -->
  </div>
  {{-- Fim do card 1 --}}
@endsection

@section('footer')
  <div class="footer">
    Copyright © BS Tecnologia e Serviços - <?php echo date('Y'); ?>
  </div>
@endsection

@section('css')
  <style>
    .escala{
      border: 1px solid #ccc; border-radius: 3px; 
    }
    .progress {
      display: none;
      position: relative;
      margin-top: 20px;
      /* margin: 20px; */
      width: 100%;
      background-color: #ddd;
      border: 1px solid blue;
      /* padding: 1px; */
      /* left: 15px; */
      border-radius: 3px;
    }
    .progress-bar {
      background-color: green;
      width: 0%;
      height: 30px;
      border-radius: 4px;
      -webkit-border-radius: 4px;
      -moz-border-radius: 4px;
    }
    .percent {
      position: absolute;
      display: inline-block;
      color: #fff;
      font-weight: bold;
      top: 50%;
      left: 50%;
      margin-top: -9px;
      margin-left: -20px;
      -webkit-border-radius: 4px;
    }
    #outputImage {
      display: none;
    }
    #outputImage img {
      max-width: 300px;
    }
  </style>
@endsection

@section('js')
  <script src='{{asset('vendor/jquery/jqueryForm.min.js')}}'></script>
  <script src="{{asset('assets/calendario1/js/sweetalert.min.js')}}"></script>

  {{-- Função Ajax para exibir barra de progresso enquanto arquivo é submetido --}}
  <script type="text/javascript">
    $(document).ready(function () {
      $('#submitButton').click(function () {
        $('#uploadForm').ajaxForm({
          target: '#outputImage',
          url: '/trafego/importacaoescala',
          beforeSubmit: function () {
            $("#outputImage").hide();
            if($("#escala").val() == "") {
              $("#outputImage").show();
              $("#outputImage").html("<div class='error'>Escolha um arquivo para upload</div>");
              return false; 
            }
            $("#progressDivId").css("display", "block");
            var percentValue = '0%';

            $('#progressBar').width(percentValue);
            $('#percent').html(percentValue);
          },
          uploadProgress: function (event, position, total, percentComplete) {
            var percentValue = percentComplete + '%';
            $("#progressBar").animate({
              width: '' + percentValue + ''
            }, {
              duration: 5000,
              easing: "linear",
              step: function (x) {
                percentText = Math.round(x * 100 / percentComplete);
                $("#percent").text(percentText + "%");
                if(percentText == "100") {
                  swal.fire('Sucesso', 'Arquivo enviado com sucesso', 'success');
                }
              }
            });
          },
          error: function (response, status, e) {
            swal.fire('Oops, algo aconteceu', 'Erro ao enviar o arquivo', 'error');
            $("#progressBar").stop();
          },
          complete: function (xhr) {
            console.log(xhr.responseText);
            if (xhr.responseText == "error") {
              $("#outputImage").show();
              $("#outputImage").html("<div class='error'>Problema no upload do arquivo.</div>");
              $("#progressBar").stop();
            } else {  
              $("#outputImage").html(xhr.responseText);
            }
          }
        });
      });
    });
  </script>
@endsection
