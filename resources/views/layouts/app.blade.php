<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - Admin Dashboard</title>
      <!-- Bootstrap core CSS-->
    <link href="{{ asset('vendor_view/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
      <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor_view/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">
      <!-- Page level plugin CSS-->
    <link href="{{ asset('vendor_view/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet">
      <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin.css') }}" rel="stylesheet">

    <link href="{{ asset('css/toastr.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/multiple-select/1.2.2/multiple-select.min.css">

    <!-- Latest compiled and minified JavaScript -->
   
    <style>
        /*.space{*/
        /*    margin-left: 10px;*/
        /*}*/
    </style>
    @stack('styles')
    <style>
    .table-custom-th th {
        min-width: auto;
    }
    .p-units{
        min-width: 100px;
    }
    .space{
        margin-bottom: 8px;
        padding-right: 8px;
        
    }
    .table td, .table th{
        padding: 5px;
    }
  .top-container {
  background-color: #f1f1f1;
  padding: 30px;
  text-align: center;
}

.header {
  padding: 10px 16px;
  background: #555;
  color: #f1f1f1;
}

.content {
  padding: 16px;
}

/*.sticky {*/
/*  position: fixed;*/
/*  top: 0;*/
/*  width: 100%;*/
}

.sticky + .content {
  padding-top: 102px;
}

    </style>
</head>
<body class="fixed-nav sticky-footer bg-dark" id="page-top">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
        <a class="navbar-brand" href="javascript:;" style="font-size: 1.35rem;font-weight: 800;letter-spacing: 3px">Scoops Creamery</a>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        
        
                  
        
        @include('includes.side_nav')
    </nav>

    <div class="content-wrapper">
        <div class="container-fluid">
            @yield('content')
        </div>

        <footer class="sticky-footer">
          <div class="container">
            <div class="text-center">
              <small>Copyright © <b>Powered by Qureshi Sons</b> 2019</small>
            </div>
          </div>
        </footer>
        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
          <i class="fa fa-angle-up"></i>
        </a>

        <div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="delete-modal" aria-hidden="true">
          <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
              <div class="modal-header bg-danger">
                <h5 class="modal-title" id="delete-modal-label" style="color: white">Are You Sure ?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">Are you sure to delete it . Data will be deleted permanently from system.</div>
              <div class="modal-footer">
                <button class="btn btn-default" type="button" data-dismiss="modal">No</button>
                <a class="btn btn-danger f-delete-btn" style="color: white">Yes</a>
              </div>
            </div>
          </div>
        </div>
        <button id="d-m-open" style="display: none" data-target="#delete-modal" data-toggle="modal"></button>
        
        <div class="modal fade" id="product-report-popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style="width: 100%" id="exampleModalLabel">Product Sell Report <small></small></h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-bordered table-custom-th" id="dataTable" width="100%" cellspacing="0">
            <thead>
              <tr>
                <th>P.ID</th>
                <th>P.Name</th>
                <th>Units</th>
                <th>Amount</th>
              </tr>
            </thead>
            <tbody>
                <?php $show_in_modal = 0;$checks = 0; ?>
              @foreach($product_report as $preport)
                @if($preport['amount'] != 0 || $preport['unit'] != 0)
                  <tr>
                    <td>{{ $preport['id'] }}</td>
                    <td>{{ $preport['name'] }}</td>
                    <td>{{ $preport['unit'] }}</td>
                    <td>{{ $preport['amount'] }}</td>
                    <?php $show_in_modal += $preport['amount']; ?>
                    <?php $checks += $preport['checks']; ?>
                  </tr>
                @endif
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="alert alert-info text-left pull-left">

          @if ( $show_in_modal == $checks ) 
          <b style="color: #2ECC71">&#10003; P.Total: {{ $show_in_modal }}</b>
          @else
          <p style="color: red">&#9888; Purchase price is not match!
          <br>
          &#10003; Actual Price: {{ $show_in_modal }}
          <br>
          &bigotimes; Db Value: {{ $checks }} </p>
          @endif
    
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
        @if(Auth::user()->role < 3)
        <div class="modal fade" id="pin-modal" tabindex="-1" role="dialog" aria-labelledby="pin-modal" aria-hidden="true" style="background: rgba(0,0,0,.9);">
          <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
              <div class="modal-header bg-dark">
                <h5 class="modal-title" id="pin-modal-label" style="color: white">Enter Pin Code</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <form method="post" action="{{ route('validate.pin') }}" id="validate-pin" onsubmit='disableButton()'>
                  <div class="form-group">
                    <input type="number" class="form-control pin">
                  </div>
                  <button id="button" type="submit" class="btn btn-dark btn-block" style="color: white">Continue</button>
                </form>
              </div>
            </div>
          </div>
        </div>
        <button id="p-m-open" style="display: none" data-target="#pin-modal" data-toggle="modal"></button>
        @endif

        <!-- Scripts -->
    <!-- Bootstrap core JavaScript-->
        <script src="{{ asset('vendor_view/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('vendor_view/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <!-- Core plugin JavaScript-->
        <script src="{{ asset('vendor_view/jquery-easing/jquery.easing.min.js') }}"></script>
        <!-- Page level plugin JavaScript-->
        <!-- <script src="{{ asset('vendor_view/chart.js/Chart.min.js') }}"></script> -->
        <script src="{{ asset('vendor_view/datatables/jquery.dataTables.js') }}"></script>
        <script src="{{ asset('vendor_view/datatables/dataTables.bootstrap4.js') }}"></script>
        <!-- Custom scripts for all pages-->
        <script src="{{ asset('js/sb-admin.min.js') }}"></script>
        <!-- Custom scripts for this page-->
        <script src="{{ asset('js/sb-admin-datatables.min.js') }}"></script>
        <script src="{{ asset('js/toastr.min.js') }}"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/multiple-select/1.2.2/multiple-select.min.js"></script>

        <script type="text/javascript">
             function disableButton() {
        var btn = document.getElementById('button');
        btn.disabled = true;
        btn.innerText = 'Approving Please Wait'

        var originalText = $("#button").text(),
            i  = 0;
        setInterval(function() {

    $("#button").append(".");
    i++;

    if(i == 4)
    {
        $("#button").html(originalText);
        i = 0;
    }

}, 500);
    }

          @if(session('success'))
          toastr.success("{{ session('success') }}")
          @elseif(session('error'))
          toastr.error("{{ session('error') }}")
          @endif

          $(document).ready(function(){
            $('.delete-btn').click(function(event){
              event.preventDefault();
              $('#d-m-open').click();
              $('.f-delete-btn').attr('href' , $(this).attr('href'));
            });

            var path_param = "";
            $('.approve-btn').click(function(event){
              if(!$(this).hasClass('app-mult')){
                event.preventDefault();
                path_param = $(this).attr('href');   
              }
              else{
                  if($('#multiple-approve input:checkbox').length < 1){
                      toastr.error('Please Select Any Invoice First');
                      return false;
                  }
              }
              $('#p-m-open').click();
            });
              $('.confirm-btn').click(function(event){
                  event.preventDefault();
                 if($('#multiple-approve input:checkbox').length < 1){
                      toastr.error('Please Select Any Order First');
                      return false;
                  }
              else{
                  if($(this).hasClass('send-to-unapprove'))
                    $('input[name="send_to_unapprove"]').val("1");
                  else
                    $('input[name="send_to_unapprove"]').val("");
                 $('#multiple-approve').submit();  
              }
          });
            $('#validate-pin').on('submit' , function(event){
              event.preventDefault();
              $.post($(this).attr('action') , {
                _token: "{{ csrf_token() }}" ,
                pin: $('input.pin').val()
              },function(data){
                if(data == 1){
                    if(path_param != ""){
                        window.location.href = path_param;
                    }
                    else{
                        if($('#multiple-approve input:checkbox').length){
                            $('#multiple-approve').submit();
                        }
                    }
                }
                else{
                  toastr.error(data);
                }
              });
            });
            @if(Auth::user()->role < 3)
            $('#alertsDropdown').click(function(){
                $.get('{{ route("notify.seen") }}' , function(success){
                    $('.notify-dot').remove();
                });
            });
            $('.alert-item').one('click' , function(event){
                event.preventDefault();
                var new_this = this;
                var param = $(this).find('.notify-id').attr('id');
                $.get('{{ route("notify.clicked") }}/' + param , function(){
                    window.location.href = "{{ route('invoices' , 'unapproved') }}"
                });
            });
            @endif
          });
          
          //   Fixed Header

window.onscroll = function() {myFunction()};

var header = document.getElementById("myHeader");
var sticky = header.offsetTop;

function myFunction() {
  if (window.pageYOffset > sticky) {
    header.classList.add("sticky");
  } else {
    header.classList.remove("sticky");
  }
}
          
        </script>

        @yield('scripts')
    </div>
</body>
</html>
