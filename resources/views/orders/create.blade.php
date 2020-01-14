@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/chosen.min.css') }}">
<style>
    .chosen-single{
        height: 40px !important;
        line-height: 36px !important;
    }
    .chosen-container-single .chosen-single div{
        top: 9px !important;
    }
    .create-invoice-section{
        display: none;
    }
    .not-in-sl{display: none;}
</style>
@endpush

@section('title') Create Order @endsection

@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
  <li class="breadcrumb-item">
    <a href="#">Order</a>
  </li>
  <li class="breadcrumb-item active">Create Order</li>
</ol>
<div class="row">
  <div class="col-md-6 m-auto">
    <div class="card mb-3">
      <div class="card-header">
        <i class="fa fa-user"></i> Enter Order Details
         
        
         <img id="bigpic" src="{{ asset('images/menu.jpg') }}" " onclick="picture()" style="display:none;">


         <button onclick="picture()"  class="btn btn-sm btn-info pull-right load-image"><i class="fa fa-img"></i>Load Menue</button>
        <button class="btn btn-sm btn-info pull-right print-invoice"><i class="fa fa-print"></i></button>
        <a href="javascript:;" target="_blank" class="btn btn-sm prev-record btn-primary pull-right" style="margin-right: 10px;display: none"><i class="fa fa-reply"></i></a>
        <span class="pull-right invoice_no" style="margin: 3px 15px;display: none"><b></b></span>
        <span class="pull-right" style="margin: 3px 15px"><b>{{ date('d/m/Y') }}</b></span>
      </div>
      <div class="card-body">
      
        <form method="post" action="{{ route('store.order') }}" id="invoice-form" onsubmit='disableButton()'>
          {{ csrf_field() }}
          <div class="form-group">
           
            @if(Auth::user()->role ==4 )
            
            <select class="form-control chosen-select" name="customer_id" id="customer-id" required="" data-placeholder="Choose a customer...">
                <option value="">Select a customer</option>
                <option value="{{ Auth::user()->customer->id }}-{{ Auth::user()->customer->user->name }}-{{ Auth::user()->customer->address }}-{{ sizeof(Auth::user()->customer->invoices) }}-{{ Auth::user()->customer->phone }}-http://maps.google.com/maps?q=+{{ Auth::user()->customer->location_url }}">{{ Auth::user()->customer->user->name }}</option>
            
            
            
            @endif
            <select class="form-control chosen-select" name="customer_id" id="customer-id" required="" data-placeholder="Choose a customer...">
                <option value="">Select a customer</option>
              @foreach($ot_customers as $customer)
                <option value="{{ $customer->customer->id }}-{{ $customer->customer->user->name }}-{{ $customer->customer->address }}-{{ sizeof($customer->customer->invoices) }}-{{ $customer->customer->phone }}-http://maps.google.com/maps?q=+{{ $customer->customer->location_url }}">{{ $customer->customer->user->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group c-selected" style="display: none">
            <div class="row cus-details form-group">
              <div class="col-md-4 cname"><h6>Name: <b></b></h6></div>
              <div class="col-md-4 cphone"><h6>Phone: <b></b></h6></div>
              <div class="col-md-4 cbalance"><h6>Balance: <b></b></h6></div>
              <div class="col-md-4 cadv"><h6>Advance: <b></b></h6></div>
              <div  class="col-md-8"><span>Set Order Date :</span><input type="date" style="margin-left:30px;" min="{{ date('Y-m-d') }}" name="order_date" value="{{ date('Y-m-d') }}" /> </div>
              <div class=" col-md-7 cus-details caddress">
                <h6>Address: <a href="" target="_blank"><b></b></a></h6>
              </div>
              <div style="padding-top:10px;" class="col-md-5 btn-group " data-toggle="buttons">
                <label class="btn btn-primary active">
                  <span>Important</span>
                  <input type="checkbox" name="important"  >
                </label>
              </div>
              <div class="col-md-12 text-center">
                  <button class="btn btn-info sl-toggler" type="button">Show All Products</button>
              </div>
            </div>
            
            <div class="table-responsive">
              <table class="table table-bordered table-custom-th" width="100%" cellspacing="0">
                <thead>
                  <tr>                    
                    <th>Name</th>
                    <th>Price</th>
                    <th>Units</th>
                    <th>Amount</th>
                  </tr>
                </thead>
                <tbody id="custom-p-check">
                </tbody>
              </table>
            </div>
          </div>
          <div class="row create-invoice-section" id="create-section">
            <div class="form-group col-md-6">
              <label>Received Amount</label>
              <input class="form-control r-amount" type="number" name="received_amount" value="0">
            </div>
            <div class="form-group col-md-6">
              <label>Total Amount</label>
              <input class="form-control t-amount" type="number" placeholder="Total Amount" disabled="" value="0">
            </div>            
            <div class="form-group col-md-6">
              <label>Advance</label>
              <input class="form-control advance-amount" type="number" name="advance" placeholder="Advance" disabled="" value="0">
            </div>
            <div class="form-group col-md-6">
              <label>Amount Left</label>
              <input class="form-control amount-left" type="number" placeholder="Amount Left" disabled="" value="0">
            </div>
            <div class="form-group col-md-6">
              <label>Customer Benefit</label>
              <input class="form-control c-benefit" type="number" placeholder="Customer Benefit" disabled="" value="0">
            </div>
            <div class="form-group col-md-6">
              <label>Sub Total</label>
              <input class="form-control sub-total" type="number" placeholder="Sub Total" disabled="" value="0">
            </div>
            <div class="col-md-12">
                <button id="button" type="submit" class="btn btn-primary btn-block">Create Order</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="od-popup" tabindex="-1" role="dialog" aria-labelledby="od-popup" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Order Date</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post" action="" class="od-form">
          {{ csrf_field() }}
          <div class="form-group">
            <label>Select Order Date</label>
            <input type="date" name="od" class="form-control old-od" required="">
          </div>
          <div class="form-group">
            <button class="btn btn-primary btn-block">Select</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript" src="{{ asset('js/invoiceJS.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/chosen.jquery.min.js') }}"></script>
<script type="text/javascript">

function disableButton() {
        var btn = document.getElementById('button');
        btn.disabled = true;
        btn.innerText = 'Order Saving Wait'

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

  $('#customer-id').on('change' , function(){
    var cdetails = $(this).val().split('-');
    console.log(cdetails);
      if($(this).val() != ''){
        $.get('{{ route("check.custom.price") }}/' + cdetails[0] , function(data){
          $('#custom-p-check').html(data);
          $('.cname b').text(cdetails[1]);
          $('.caddress a').attr('href' , cdetails[5]);
          $('.caddress b').text(cdetails[2]);
          $('.cphone b').text(cdetails[4]);
          $('.invoice_no b').text("#" + cdetails[3]);
          $('.prev-record').attr("href" ,  "{{ route('customer.invoices') }}/" + cdetails[0]);
          $('.c-selected').fadeIn('slow');
          $('.t-amount').val(parseInt($('#old_balance').val()));
          
          if(parseInt($('#old_balance').val()) < 0)
          {
              $('.cadv b').text($('#old_balance').val());
          }
          else
          {
              $('.cbalance b').text($('#old_balance').val());
          }
          
          $('.prev-record').fadeIn();
          $('.invoice_no').fadeIn();
        });
      }
      else{
        $('.c-selected').fadeOut('slow');
        $('.prev-record').fadeOut();
          $('.invoice_no').fadeOut();
      }
  });
  $('.print-invoice').click(function(){
      window.print();
  });
  $('.chosen-select').chosen();
  
 
        function picture() {
    var image = new Image();
    image.src = $('#bigpic').attr('src');

    var w = window.open("",'_blank');
    w.document.write(image.outerHTML);
    w.document.close(); 
}
$(document).on('click', '.sl-toggler', function(){
        $('.not-in-sl').toggle();
    });

</script>
@endsection