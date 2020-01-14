@extends('layouts.app')

@section('title') Edit Order Taker @endsection

@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
  <li class="breadcrumb-item">
    <a href="#">Order Taker</a>
  </li>
  <li class="breadcrumb-item active">Edit Order Taker</li>
</ol>
<div class="row">
  <div class="col-md-6 m-auto">
    <div class="card mb-3">
      <div class="card-header">
        <i class="fa fa-user"></i> Enter Order Taker Details
      </div>
      <div class="card-body">
        <form method="post" action="{{ route('update.ot' , $ot->id) }}">
          {{ csrf_field() }}
          <div class="form-group">
            <label>Name</label>
            <input class="form-control" type="text" placeholder="Enter Name" name="name" value="{{ $ot->name }}">
          </div>
          <div class="form-group">
            <label>Email</label>
            <input class="form-control" type="email" placeholder="Enter Email" name="email" value="{{ $ot->email }}">
          </div>
          <div class="form-group">
            <label>Phone</label>
            <input class="form-control" type="text" placeholder="Enter Phone Number" name="phone" value="{{ $ot->phone }}">
          </div>
          <div class="form-group">
            <label>Password</label>
            <input class="form-control" type="password" placeholder="New Password" name="password">
          </div>
          @if ($custom_prices)
          <h3 class="mid">Custom Order Taker Benefit</h3>
          <div class="table-responsive">
            <table class="table table-bordered table-custom-th"  width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>Product Name</th>
                  <th>Order Taker Benefit</th>
                </tr>
              </thead>
              <tbody>
              @foreach($custom_prices as $cp)
              <tr>
                <input type="hidden" class="form-control p-id" name="product_id[]" value="{{ $cp->product_id }}">
                <td class="name">{{ $cp->product->name }}</td>
                <td>
                  <input type="text" class="form-control p-ot" name="ot_benefit[]" value="{{ $cp->ot_benefit }}">
                </td>
              </tr>
              
              @endforeach
               
              </tbody>
            </table>
          </div>
           @endif
        <hr>
          <div style="margin-top : 30px;" class="row">
          <h5 class="mid2" >Customer Allow to Order Taker</h5>
          <a class="btn options selectAll btn-outline-primary">Check All</a>
          <a class="btn options unselectAll btn-outline-primary">Uncheck All</a>
          </div>
        <div class="table-responsive">
          <table class="table table-bordered table-custom-th" width="100%" cellspacing="0">
            <thead>
              <tr>
                <th>Name</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($customers as $c)
              <tr class="customers">
                <input type="hidden" class="form-control c-id" value="{{ $c->id }}" @if(in_array($c->id, $ot_customers)) name="customer_id[]" @endif>
                <td>{{ $c->user->name }}</td>
                <td><input type="checkbox" @if(in_array($c->id, $ot_customers)) checked @endif class="option"></td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="btn-group mid" data-toggle="buttons">
          <label class="btn btn-primary active">
           <span>Allow Order Taker to create custom price customer</span>
           <input type="checkbox" name="custom" id="" >
          </label>
        </div> 
        </div>   
        <button class="btn btn-primary btn-block">Update Order Taker</button>
        </form>
      </div>
    </div>
  </div>
@endsection
@section('scripts')
<script type="text/javascript">
  $('#u-type').on('change' , function(){
    if($(this).val() == 2){
      $('#has_pin').html('<div class="form-group">\
            <label>Pin Code</label>\
            <input class="form-control" type="number" placeholder="Enter Pin" name="pin">\
          </div>');
    }
    else{
      $('#has_pin').empty();
    }
  });
  
  $(".selectAll").on('click',function(){
    $(".option").prop('checked',true);
    var customer=$('.customers .c-id');
    customer.each(function(key,value){
        $(this).attr('name', 'customer_id[]');
    });
  });

   $(".unselectAll").on('click',function(){
    $(".option").prop('checked',false);
    var customer=$('.customers .c-id');
    customer.each(function(key,value){
        $(this).attr('name', '');
    });
  });

  $('.option').on('change',function(){
    var customer = $(this).parent().parent().find("input[type='hidden']");
    if($(this).is(':checked')) {
      customer.attr('name','customer_id[]');
    } else { 
      customer.attr('name','');
    }
  });
</script>
@endsection