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

@section('title') Add Invoice @endsection

@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
  <li class="breadcrumb-item">
    <a href="#">Invoices</a>
  </li>
  <li class="breadcrumb-item active">Add Invoice</li>
</ol>
<div class="row">
  <div class="col-md-6 m-auto">
    <div class="card mb-3">
      <div class="card-header">
        <i class="fa fa-user"></i> Enter Invoice Details
        <button class="btn btn-sm btn-info pull-right print-invoice"><i class="fa fa-print"></i></button>
        <a href="javascript:;" target="_blank" class="btn btn-sm prev-record btn-primary pull-right" style="margin-right: 10px;display: none"><i class="fa fa-reply"></i></a>
        <span class="pull-right invoice_no" style="margin: 3px 15px;display: none"><b></b></span>
        <span class="pull-right" style="margin: 3px 15px"><b>{{ date('d/m/Y') }}</b></span>
      </div>
      <div class="card-body">
        <form method="post" action="{{ route('store.invoice') }}" id="invoice-form" onsubmit='disableButton()'>
          {{ csrf_field() }}
          <div class="form-group">
            <select class="form-control chosen-select" name="customer_id" id="customer-id" required="" data-placeholder="Choose a customer...">
                <option value="">Select a customer</option>
              @foreach($customers as $customer)
              <option value="{{ $customer->id }}-{{ $customer->user->name }}-{{ $customer->address }}-{{ sizeof($customer->invoices) }}-{{ $customer->phone }}-http://maps.google.com/maps?q=+{{ $customer->location_url }}">{{ $customer->user->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group c-selected" style="display: none">
            <div class="row cus-details form-group">
              <div class="col-md-4 cname"><h6>Name: <b></b></h6></div>
              <div class="col-md-4 cphone"><h6>Phone: <b></b></h6></div>
              <div class="col-md-4 cbalance"><h6>Balance: <b></b></h6></div>
              <div class="col-md-4 cadv"><h6>Advance: <b></b></h6></div>
              <button class="btn btn-info sl-toggler" type="button">Show All Products</button>
            </div>
            <div class="row cus-details form-group">
                <div class="col-md-12 caddress"><h6>Address: <a href="" target="_blank"><b></b></a></h6></div>
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
                <button id="button" type="submit" class="btn btn-primary btn-block">Create Invoice</button>
            </div>
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
        btn.innerText = 'Invoice Saving Wait'

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
  $(document).on('click', '.sl-toggler', function(){
        $('.not-in-sl').toggle();
    });
</script>
@endsection