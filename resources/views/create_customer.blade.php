@extends('layouts.app')

@section('title') Add Customer @endsection

@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
  <li class="breadcrumb-item">
    <a href="#">Users</a>
  </li>
  <li class="breadcrumb-item active">Add Customer
          </li>
</ol>
<div class="row">
  <div class="col-md-6">
    <div class="card mb-3">
      <div class="card-header">
        <i class="fa fa-user"></i> Enter Customer Details
      </div>
      <div class="card-body">
        <form method="post" action="{{ route('create.customer') }}" enctype="multipart/form-data" id="customer-form">
          {{ csrf_field() }}
          <div class="form-group">
            <label>Name <span style="opacity: 0.5; font-style: italic;">(Required)</span></label>
            <input class="form-control" type="text" placeholder="Enter Name" name="name" required="">
          </div>
          <div class="form-group">
            <label>Email <span style="opacity: 0.5; font-style: italic;">(Required)</span></label>
            <input class="form-control" type="email" id="email" onblur="checkMailStatus()"size=18 maxlength=50 placeholder="Enter Email" name="email" required="">
            @if($errors->has('email'))
            <div class="alert alert-danger">
                {{ $errors->first('email') }}
            </div>
            @endif
          </div>
          <div class="form-group">
            <label>Area <span style="opacity: 0.5; font-style: italic;">(Required)</span></label>
            <a href="{{ route('add.area') }}" class="btn pull-right"><i class="fa fa-plus"></i> Add Area</a>
            <select class="form-control" name="area">
            <option value="" disabled>Select Area</option>
            @foreach($areas as $a)
            <option value="{{ $a->id }}">{{ $a->name }}</option>
            @endforeach
            </select>
            @if($errors->has('area'))
            <div class="alert alert-danger">
                {{ $errors->first('area') }}
            </div>
            @endif
          </div>
          <div class="form-group">
            <label>Password <span style="opacity: 0.5; font-style: italic;">(Required)</span></label>
            <input class="form-control" type="password" placeholder="Enter Password" name="password" required="">
          </div>
          <hr>
          <div class="form-group">
            <label>Phone <span style="opacity: 0.5; font-style: italic;">(Required)</span></label>
            <input class="form-control" type="text" placeholder="Enter Phone" name="phone" required="">
          </div>
          
          <div class="form-group">
            <label>Location Cordinates <span style="opacity: 0.5; font-style: italic;">(Required)</span></label>
            
            <div id="map-layer"></div>
            <input class="form-control" type="text"  maxlength="19" placeholder="Enter Cordinates Only" name="location_url" required="">
          </div>
          <div class="form-group">
            <label>Address <span style="opacity: 0.5; font-style: italic;">(Required)</span></label>
            <textarea class="form-control" rows="4" placeholder="Enter Address" name="address" required=""></textarea>
          </div>
          <div class="form-group">
            <label>CNIC No <span style="opacity: 0.5; font-style: italic;">(Optional)</span></label>
            <input class="form-control" type="text" id="cnic" maxlength="15" placeholder="Enter CNIC Number" name="cnic">
          </div>
          <div class="form-group">
                <label>Balance Limit <span style="opacity: 0.5; font-style: italic;">(Optional)</span></label>
                <input class="form-control" type="text" placeholder="Enter Balance Limit" name="balance_limit">
          </div>
          
          <div class="form-group">
            <label>Freezer Model <span style="opacity: 0.5; font-style: italic;">(Optional)</span></label>
            <input class="form-control" type="text" placeholder="Enter Freezer Model" name="freezer_model">
          </div>
          <div class="form-group">
            <label>Other Details <span style="opacity: 0.5; font-style: italic;">(Optional)</span></label>
            <input class="form-control" type="text" placeholder="Enter Other Details" name="other">
          </div>
          <div class="form-group">
            <label>Agreement Image* <span style="opacity: 0.5; font-style: italic;">(Optional)</span></label>
            <input class="form-control" type="file" name="image">
            @if($errors->has('image'))
            <div class="alert alert-danger">
                {{ $errors->first('image') }}
            </div>
            @endif
          </div>
          <div id="custom-prices" style="display: none;"></div>
          @foreach($products as $p)
          <input type="hidden" value="{{ $p->id }}" data-value="{{ $p->id }}" name="allowed_products[]" />
          <input type="hidden" value="{{ $p->id }}" data-fvalue="{{ $p->id }}"name="final_allowed_products[]" />
          @endforeach
          <button class="btn btn-primary btn-block">Create Customer</button>
        </form>
      </div>
    </div>
  </div>
 @if ($products)
     
 
  <div class="col-md-6">
    <div class="card mb-3">
      <div class="card-header">
        <i class="fa fa-table"></i> Product List <small class="pull-right">For Custom Prices , Edit Values And Click Edit Button</small></div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-custom-th" width="100%" cellspacing="0">
            <thead>
              <tr>
                <th>Name</th>
                 @if(Auth::user()->role < 3)
                <th style="width: 90px;">Price</th>
                <th>Purchase Price</th>
                <th>Customer Benefit</th>
                @endif
                <th>Order Benefit</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($products as $p)
              <tr>
                <input type="hidden" class="form-control p-id" name="product_id[]" value="{{ $p->id }}">
                <td>{{ $p->name }}</td>
                @if(Auth::user()->role < 3)
                <td>
                  <input type="text" class="form-control p-p" name="price[]" value="{{ $p->price }}">
                </td>
                <td>{{ $p->p_price }}</td>
                <td><input type="text" class="form-control p-c-b" name="c_benefit[]" value="{{ $p->c_benefit }}"></td>
                @endif
                <td><input type="text" readonly class="form-control p-a-b" name="a_benefit[]" value="{{ $p->ot_benefit }}"></td>
                @if(Auth::user()->role < 3)
                <td><button class ="btn btn-sm btn-primary custom-price-btn"><i class="fa fa-edit"></i></button><td>
                @endif
                <td>
                <input type="hidden" class="form-control p-id" name="" value="{{ $p->id }}">
                <label class ="btn btn-sm"><input type="checkbox" class="allowed-products" value="{{ $p->id }}" checked /> Short list</label>
                <label class="btn btn-sm"><input type="checkbox" class="final-allowed-products" value="{{ $c_price->product_id }}"  checked /> Allow</label>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  @endif
</div>
@endsection
@section('scripts')
<script
	src="https://maps.googleapis.com/maps/api/js?key=<?php echo API_KEY; ?>&callback=initMap"
	async defer></script>
<script type="text/javascript">
$(document).ready(function() {
  {{--  $('table').dataTable();  --}}
} );
//cnic
$('#cnic').keydown(function(){

  //allow  backspace, tab, ctrl+A, escape, carriage return
  if (event.keyCode == 8 || event.keyCode == 9 
                    || event.keyCode == 27 || event.keyCode == 13 
                    || (event.keyCode == 65 && event.ctrlKey === true) )
                        return;
  if((event.keyCode < 48 || event.keyCode > 57))
   event.preventDefault();

  var length = $(this).val().length; 
              
  if(length == 5 || length == 13)
   $(this).val($(this).val()+'-');

 });

//cnic Finish
  $('.custom-price-btn').click(function(){
    if(!$(this).hasClass('added-to-c')){
      $(this).addClass('added-to-c');
      $(this).html('<i class="fa fa-check"></i>');
      var cTR = $(this).closest('tr');
      $(cTR).find('.p-p').attr('value' , $(cTR).find('.p-p').val());
      $(cTR).find('.p-a-b').attr('value' , $(cTR).find('.p-a-b').val());
      $(cTR).find('.p-c-b').attr('value' , $(cTR).find('.p-c-b').val());
      $('#custom-prices').append('<tr>' + cTR.html() + '</tr>');
    }
    else{
      $(this).removeClass('added-to-c');
      $(this).html('<i class="fa fa-edit"></i>');
      var cTR = $(this).closest('tr');
      var id=cTR.children("input[type='hidden']").val();
      $('#custom-prices tr').find('input[type=hidden][value='+id+']').parent().remove();
    }
  });
    $(".selectAll").on('click',function(){
    $(".option").prop('checked',true);
    var product=$('.products .p-id').clone();
    product.each(function(key,value){
        $('#custom-allow').append(value);
    });
  });

   $(".unselectAll").on('click',function(){
    $(".option").prop('checked',false);
    $("#custom-allow").html('');
  });

  $('.option').on('change',function(){
    var cTR = $(this).parent().parent().find("input[type='hidden']").clone();
    var product=cTR;
    var id=cTR.val();

    if($(this).is(':checked')) {
      product.attr('name','product_id[]');
      $('#custom-allow').append(product);

    } else { 
      $('#custom-allow').find('input[type=hidden][value='+id+']').remove();
    }
  });
  $('.allowed-products').change(function(){
      var val = $(this).val();
      if($(this).is(':checked')){
          $('#customer-form [data-value="'+val+'"]').attr('name', 'allowed_products[]');
      }
      else{
          $('#customer-form [data-value="'+val+'"]').removeAttr('name');
      }
  });
  $('.final-allowed-products').change(function(){
      var val = $(this).val();
      if($(this).is(':checked')){
          $('#customer-form [data-fvalue="'+val+'"]').attr('name', 'final_allowed_products[]');
      }
      else{
          $('#customer-form [data-fvalue="'+val+'"]').removeAttr('name');
      }
  });
  
  function getLocation() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition);
      } else {
        alert("Try any other browser");
      }
    }
    
    function showPosition(position) {
      $('[name="location_url"]').val(position.coords.latitude+','+position.coords.longitude);
    }
    $(document).ready(getLocation);
</script>
@endsection