@extends('layouts.app')

@push('styles')
<style>
    .create-invoice-section{
        display: none;
    }
</style>
@endpush

@section('title') Edit Order @endsection

@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
  <li class="breadcrumb-item">
    <a href="#">Orders</a>
  </li>
  <li class="breadcrumb-item active">Edit Order</li>
</ol>
<div class="row">
  <div class="col-md-6 m-auto">
    <div class="card mb-3">
      <div class="card-header">
        <i class="fa fa-user"></i> Order Details
      </div>
      <div class="card-body">        
        <div class="form-group update-type">
          <div class="row">
            <div class="col-md-6">
              <label class="btn btn-block btn-light">
                <input type="radio" name="radio-left" class="full-edit" checked="">
                Full Update
              </label>
            </div>
            <div class="col-md-6">
              <label class="btn btn-block btn-light">
                <input type="radio" name="radio-left" class="amount-left-r">
                Balance Update
              </label>
            </div>
          </div>
        </div>
        <form method="post" action="{{ route('update.order' , $order->id) }}" id="invoice-form" onsubmit='disableButton()'>
          {{ csrf_field() }}
          <div class="row">
            <div style="col-md-6 padding-top:10px;" class="col-md-5 btn-group " data-toggle="buttons">
              <label class="btn btn-primary active">
                <span>Important</span>
                <input type="checkbox" name="important"  >
              </label>
            </div>

            <span>Set Order Date :</span><input type="date" style="margin-left:30px;" min="{{ date('Y-m-d', strtotime($order->order_date)) }}" name="order_date" value="{{ date('Y-m-d', strtotime($order->order_date)) }}" />
          </div>
          <div class="form-group">
              <h6>Customer Name: <b>{{ strtoupper($order->customer->user->name) }}</h6>
              
              <input type="hidden" name="customer_id" value="{{ $order->customer_id }}">
              <input type="hidden" name="old_balance" id="old_balance" value="{{ $amount_left }}">
          </div>
          <div class="form-group">
            <div class="table-responsive">
              <table class="table table-bordered table-custom-th" width="100%" cellspacing="0">
                <thead>
                  <tr>                    
                    <th>Name</th>
                    <th>Price</th>
                    <th>Units</th>
                    <th>Amount</th>
                    <th>c Ben</th>
                  </tr>
                </thead>
                <tbody id="custom-p-check">
                  @foreach($order->orderdetail as $odetail)
                  <tr>
                    <input type="hidden" class="form-control p-id" name="product_id[]" value="{{ $odetail->product_id }}">                  
                    <input type="hidden" class="c-ben" name="" value="{{ $odetail->product->c_benefit }}">  
                    <td>{{ $odetail->product->name }}</td>
                    <td class="p-price">
                      <input type="text" value="{{ $odetail->product->price }}" class="form-control" {{ Auth::user()->role < 3 ? '' : 'readonly' }} /></td>
                    <td>
                      <input type="number"  onchange="myFunction()" class="form-control p-units" value="{{ $odetail->unit }}" name="unit[]">
                    </td>
                    <td><input type="number" name="amount[]" value="{{ $odetail->amount }}" class="row-amount" disabled=""></td>
                    <td class="show-row-ben">{{ $odetail->product->c_benefit*$odetail->unit }}</td>
                  </tr>
                  @endforeach
                  @foreach($products as $p)
                  <tr>
                    <input type="hidden" class="form-control p-id" name="" value="{{ $p->id }}">  
                    <input type="hidden" class="c-ben" name="" value="{{ $p->c_benefit }}">  
                    <td>{{ $p->name }}</td>
                    <td class="p-price"><input type="text" value="{{ $p->price }}" class="form-control" {{ Auth::user()->role < 3 ? '' : 'readonly' }} /></td>
                    <td>
                      <input type="number" class="form-control p-units" name="">
                    </td>
                    <td><input type="number" class="row-amount" disabled=""></td>
                    <td class="show-row-ben">0</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
          <div class="row" id="create-section">
            <div class="form-group col-md-6">
              <label>Received Amount</label>
              <input class="form-control r-amount" type="number" name="received_amount" value="{{ $order->received_amount }}">
            </div>
            <div class="form-group col-md-6">
              <label>Total Amount</label>
              <input class="form-control t-amount" id="mySelect"  type="number" name = "amount" placeholder="Total Amount" disabled="" value="{{ $order->amount }}">

            </div>
            <input style="display: none" type="number" name="hid" id="demo" value="">
            
            <div class="form-group col-md-6">
              <label>Advance</label>
              <input class="form-control advance-amount" type="number" name="advance" placeholder="Advance" disabled="" value="{{ $order->advance }}">
            </div>
            <div class="form-group col-md-6">
              <label>Amount Left</label>
              <input class="form-control amount-left" type="number" placeholder="Amount Left" disabled="" value="{{ $amount_left }}">
            </div>
            <div class="form-group col-md-6">
              <label>Customer Benefit</label>
              <input class="form-control c-benefit" type="number" name = "c_benefit" placeholder="Customer Benefit" disabled="" value="{{ $order->c_benefit }}">
            </div>
            <div class="form-group col-md-6">
              <label>Sub Total</label>
              <input class="form-control sub-total" type="number"  placeholder="Sub Total" disabled="" value="{{ $order->subtotal }}">
            </div>
            <div class="col-md-12">
                <button id="button" type="submit" class="btn btn-primary btn-block">Update Order</button>
            </div>
          </div>
        </form>
        <form method="post" id="amount-left-r-form" style="display: none">
          {{ csrf_field() }}
          <div class="form-group">
            <label>Enter Received Amount ( RS-/ <b>{{ $amount_left }}</b> Left )</label>
            <input type="number" name="amount_left_input" class="form-control">
          </div>
          <button class="btn btn-primary btn-block">Update Order</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript" src="{{ asset('js/invoiceJS.js') }}"></script>

<script type="text/javascript">


function myFunction() {
  var x = document.getElementById("mySelect").value;
  document.getElementById("demo").value = x;
}

function disableButton() {

        var btn = document.getElementById('button');
        btn.disabled = true;
        btn.innerText = 'Order Updating Wait'
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

  $('.update-type input:radio').on('change' , function(){

    if(this.checked){
      if($(this).hasClass('full-edit')){
        $('#invoice-form').attr('action' , $('#amount-left-r-form').attr('action')).show();
        $('#amount-left-r-form').hide();
      }
      else{
        $('#amount-left-r-form').attr('action' , $('#invoice-form').attr('action')).show();
        $('#invoice-form').hide(); 
      }
    }
  });
</script>
@endsection