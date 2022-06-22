@extends('adminlte::page')
@extends('common/navbar.nav_right')

@section('content')
{{-- 
@include('mensagem') <!-- Se existir uma mensagem de erro no session flash, ela é mostrada aqui -->
    <center><h1>PÁGINA INICIAL</h1> <img src='vendor/adminlte/dist/img/AdminLTELogo.png'  class="img-responsive" ></center>
--}}
@endsection
<script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="{{asset('assets/calendario1/js/dataTables.min.js')}}"></script>
<script src="{{asset('assets/calendario1/js/sweetalert.min.js')}}"></script>
<script>
    <?php if(session("Alert")){ ?>
          swal.fire('Pedido enviado', '{{ session('Alert') }}', 'success');
          <?php session()->put('Alert',''); 
        }elseif(session("AlertError")){ ?>
          swal.fire('Ocorreu um error', '{{ session('AlertError') }}', 'error');
          <?php session()->put('AlertError',''); 
        }?>
</script>