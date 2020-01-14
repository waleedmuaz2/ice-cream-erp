@extends('layouts.app')

@section('title') Edit Customer @endsection

@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
  <li class="breadcrumb-item">
    <a href="#">Users</a>
  </li>
  <li class="breadcrumb-item active">Edit Customer
   <a class="btn options selectAll btn-outline-primary">Check All</a>
          <a class="btn options unselectAll btn-outline-primary">Uncheck All</a>
          </li>
</ol>
<form method="post" action="{{ route('update.customer' , $customer->id) }}" id="edit-customer" enctype="multipart/form-data" onsubmit='disableButton()'>
  {{ csrf_field() }}
    <div class="row">
      <div class="col-md-6">
        <div class="card mb-3">
          <div class="card-header">
            <i class="fa fa-user"></i> Customer Details
          </div>
          <div class="card-body">
              <div class="form-group">
                <label>Name</label>
                <input class="form-control" type="text" placeholder="Enter Name" name="name" value="{{ $customer->user->name }}">
              </div>
              <div class="form-group">
                <label>Email</label>
                <input class="form-control" type="email" placeholder="Enter Email" name="email" value="{{ $customer->user->email }}">
              </div>
              <div class="form-group">
                <label>Area <span style="opacity: 0.5; font-style: italic;">(Required)</span></label>
                <a href="{{ route('add.area') }}" class="btn pull-right"><i class="fa fa-plus"></i> Add Area</a>
                <select class="form-control" name="area">
                <option value="" disabled>Select Area</option>
                @foreach($areas as $a)
                <option value="{{ $a->id }}" @if($a->id == $customer->area_id) selected @endif>{{ $a->name }}</option>
                @endforeach
                </select>
                @if($errors->has('area'))
                <div class="alert alert-danger">
                    {{ $errors->first('area') }}
                </div>
                @endif
              </div>
              <div class="form-group">
                <label>New Password</label>
                <input class="form-control" type="password" placeholder="Enter New Password" name="password">
              </div>
              <hr>
              <div class="form-group">
                <label>CNIC No</label>
                <input class="form-control" type="text" placeholder="Enter CNIC Number" name="cnic" value="{{ $customer->cnic }}">
              </div>
              <div class="form-group">
                <label>Phone</label>
                <input class="form-control" type="text" placeholder="Enter Phone" name="phone" value="{{ $customer->phone }}">
              </div>
              <div class="form-group">
                <label>Agreement Image*</label>
                <img src="{{ asset($customer->image) }}" style="height: 200px;width: 200px;margin: 10px 0px;display: block" />
                <input class="form-control" type="file" name="image">
              </div>
              <div class="form-group">
                <label>Balance Limit</label>
                <input class="form-control" type="text" placeholder="Enter Balance Limit" name="balance_limit" value="{{ $customer->balance_limit }}">
              </div>
              <div class="form-group">
                <label>Location URL</label>
                <input class="form-control" type="text" placeholder="Enter URL ( Google Location )" name="location_url" value="{{ $customer->location_url }}">
              </div>
              <div class="form-group">
                <label>Address</label>
                <textarea class="form-control" rows="4" placeholder="Enter Address" name="address">{{ $customer->address }}</textarea>
              </div>
              <div class="form-group">
                <label>Freezer Model</label>
                <input class="form-control" type="text" placeholder="Enter Freezer Model" name="freezer_model" value="{{ $customer->freezer_model }}">
              </div>
              <div class="form-group">
                <label>Other Details</label>
                <input class="form-control" type="text" placeholder="Enter Other Details" name="other" value="{{ $customer->other }}">
              </div>
              @php $allowed_products = explode('|', $customer->allowed_products) @endphp
              @php $final_allowed_products = explode('|', $customer->final_allowed_products) @endphp
              @foreach($products as $p)
              <input type="hidden" value="{{ $p->id }}" data-value="{{ $p->id }}" @if(in_array($p->id, $allowed_products)) name="allowed_products[]" @endif />
              <input type="hidden" value="{{ $p->id }}" data-fvalue="{{ $p->id }}" @if(in_array($p->id, $final_allowed_products)) name="final_allowed_products[]" @endif />
              @endforeach
              <button id="button" class="btn btn-primary btn-block">Edit Customer</button>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card mb-3">
          <div class="card-header">
            <i class="fa fa-table"></i> Custom Price List <small class="pull-right">To Edit Custom Prices , Edit Values And Click Edit Button</small></div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered table-custom-th" width="100%" cellspacing="0">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Purchase Price</th>
                    <th>Admin Benefit</th>
                    <th>Customer Benefit</th>
                    <th>Action</th>
                    <th>Allow</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $printed = []; ?>
                  @foreach($customer->custom_prices as $c_price)
                  <?php $printed[] = $c_price->product_id; ?>
                  <tr class="added-to-c">                
                    <input type="hidden" class="form-control this-id" name="this_id[]" value="{{ $c_price->id }}">
                    <td>{{ $c_price->product->name }}</td>
                    <td>
                      <input type="text" class="form-control p-p" name="c_price[]" value="{{ $c_price->price }}">
                    </td>
                    <td>{{ $c_price->product->p_price }}</td>
                    <td><input type="text" class="form-control p-a-b" name="c_a_benefit[]" value="{{ $c_price->a_benefit }}"></td>
                    <td><input type="text" class="form-control p-c-b" name="c_c_benefit[]" value="{{ $c_price->c_benefit }}"></td>
                    <td><button class="btn btn-sm btn-primary custom-price-btn added-to-c" type="button"><i class="fa fa-check"></i></button></td>
                    <td>
                    <input type="hidden" class="form-control p-id" name="" value="{{ $c_price->product_id }}">
                    <label class="btn btn-sm"><input type="checkbox" class="allowed-products" value="{{ $c_price->product_id }}"  @if(in_array($c_price->product_id, $allowed_products)) checked @endif /> Short list</label>
                    <label class="btn btn-sm"><input type="checkbox" class="final-allowed-products" value="{{ $c_price->product_id }}"  @if(in_array($c_price->product_id, $final_allowed_products)) checked @endif /> Allow</label>
                    </td>
                  </tr>
                  @endforeach
                  @foreach($products as $pr)
                  @if(!in_array($pr->id , $printed))
                  <tr>                
                    <input type="hidden" class="form-control p-id" name="product_id[]" value="{{ $pr->id }}">
                    <td>{{ $pr->name }}</td>
                    <td>
                      <input type="text" class="form-control p-p" name="price[]" value="{{ $pr->price }}">
                    </td>
                    <td>{{ $pr->p_price }}</td>
                    <td><input type="text" class="form-control p-a-b" name="a_benefit[]" value="{{ $pr->a_benefit }}"></td>
                    <td><input type="text" class="form-control p-c-b" name="c_benefit[]" value="{{ $pr->c_benefit }}"></td>
                    <td><button class="btn btn-sm btn-primary custom-price-btn" type="button"><i class="fa fa-edit"></i></button>
                    <td>
                    <input type="hidden" class="form-control p-id" name="" value="{{ $pr->id }}">
                    <label class ="btn btn-sm"><input type="checkbox" class="allowed-products" value="{{ $pr->id }}" @if(in_array($pr->id, $allowed_products)) checked @endif /> Short list</label>
                    <label class="btn btn-sm"><input type="checkbox" class="final-allowed-products" value="{{ $pr->id }}"  @if(in_array($pr->id, $final_allowed_products)) checked @endif /> Allow</label>
                    </td>
                    </tr>
                  @endif
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
</form>
@endsection
@section('scripts')
<script type="text/javascript">

function disableButton() {
        var btn = document.getElementById('button');
        btn.disabled = true;
        btn.innerText = 'Customer Updating Wait'

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

  $('.custom-price-btn').click(function(){
      var cTR = $(this).closest('tr');
    if(!cTR.hasClass('added-to-c')){
        cTR.addClass('added-to-c');
        $(this).html('<i class="fa fa-check"></i>');
    }
    else{
      $(this).html('<i class="fa fa-edit"></i>');
        cTR.removeClass('added-to-c');
    }
  });
  $('#edit-customer').one('submit' , function(event){
      event.preventDefault();
      $('tbody tr').each(function(){
         if(!$(this).hasClass('added-to-c')){
             $(this).find('input').each(function(){
                $(this).removeAttr('name');
             });
         }
      });
      $(this).submit();
  });
  
  // Allowed Products
  
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
  
  // Allowed P
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
          $('#edit-customer [data-value="'+val+'"]').attr('name', 'allowed_products[]');
      }
      else{
          $('#edit-customer [data-value="'+val+'"]').removeAttr('name');
      }
  });
  $('.final-allowed-products').change(function(){
      var val = $(this).val();
      if($(this).is(':checked')){
          $('#edit-customer [data-fvalue="'+val+'"]').attr('name', 'final_allowed_products[]');
      }
      else{
          $('#edit-customer [data-fvalue="'+val+'"]').removeAttr('name');
      }
  });
</script>
@endsection