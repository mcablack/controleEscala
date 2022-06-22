@extends('adminlte::page')
@extends('common/navbar.nav_right')

@section('content_header')
  <h1>Escala de trabalho</h1>
@endsection

@section('content')
  {{-- Início do card 1 --}}
  <div class="card card-default">
    <div class="card-body">
      <div class="row">
        <div class="col-md-5 col-sm-6 col-xs-12">
          <div class="info-box bg-aqua">
            <span class="info-box-icon"><i class="fa fa-user"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Nome</span>
              <span class="info-box-number">{{$operador['nome_operador']}}</span>
            </div>  <!-- /.info-box-content -->
          </div>  <!-- /.info-box -->
        </div> <!-- /.col -->
        
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-green">
            <span class="info-box-icon"><i class="fa fa-cogs"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">skill</span>
              <span class="info-box-number">{{$operador['skill']}}</span>
            </div><!-- /.info-box-content -->
          </div> <!-- /.info-box -->
        </div> <!-- /.col -->
      
        <div class="col-md-4 col-sm-6 col-xs-12">
          <div class="info-box bg-yellow">
            <span class="info-box-icon"><i class="fa fa-user-tie"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Supervisor</span>
              <span class="info-box-number">{{$operador['nome_supervisor']}}</span>
            </div> <!-- /.info-box-content -->
          </div> <!-- /.info-box -->
        </div> <!-- /.col -->
      </div>

      <div id="calendar">
        <?php
          $dateDay = date('Y-m-d');
          echo "
          <script>;
            document.addEventListener('DOMContentLoaded', renderCalendar);

            function renderCalendar() {
              var initialLocaleCode = 'pt-br';
              var localeSelectorEl  = document.getElementById('locale-selector');
              var calendarEl = document.getElementById('calendar');

              var calendar = new FullCalendar.Calendar(calendarEl, {
                headerToolbar: {
                  left: 'next today',
                  center: 'title',
                  right: 'dayGridMonth,timeGridWeek'
                },
                initialDate: '{$dateDay}',
                locale: initialLocaleCode,
                updateSize: true,
                navLinks: true, // can click day/week names to navigate views
                selectable: false,
                selectMirror: true,
                editable: false,
                dayMaxEvents: false, // allow 'more' link when too many events
                events: {$events},
                eventTimeFormat:{
                  hour: 'numeric',
                  minute: '2-digit',
                  meridiem: false
                },
                displayEventTime: true,
                displayEventEnd: true
              });

              calendar.render();
            }
          </script>";
        ?>
      </div>
    </div> <!-- /.card-body -->
  </div>
  {{-- Fim do card 1 --}}

  {{-- Versão --}}
  <div style="color: #8c8b8b; display: flex; justify-content: flex-end;">Versão: 1.0.0</div>
@endsection

@section('footer')
  <div class="footer">
    Copyright © BS Tecnologia e Serviços - <?php echo date('Y'); ?>
  </div>
@endsection

@section('css')
  <link rel="stylesheet" href='{{asset('assets/calendario1/css/main-fullcalendar.min.css')}}'>
@endsection

@section('js')
  <script src='{{asset('assets/calendario1/js/main-fullcalendar.min.js')}}'></script>
  <script src='{{asset('assets/calendario1/js/locales-fullcalendar-all.min.js')}}'></script>

  <script>
    // Script para renderizar o calendário após 2,5 milissegundos
    $('[data-widget=pushmenu]').click(function(){
      setTimeout(function(){
        renderCalendar();
      }, 250);
    });
  </script>
@endsection
