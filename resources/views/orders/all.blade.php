@extends('layouts.app')
@section('title') All Orders @endsection
@section('content')
<?php error_reporting(0) ?>
<ol class="breadcrumb">
  <li class="breadcrumb-item"><a href="#">Orders</a></li>
  <li class="breadcrumb-item active">All Orders</li>
</ol>
<div class="row">
  <div class="col-md-12">
    <div class="card mb-3">
      <div class="card-header">
        <i class="fa fa-table"></i> Orders List
        <div class="col-md-10 pull-right">
        @if(Auth::user()->role < 3)
          <form method="post" >
            {{ csrf_field() }}
            <div style="padding: 20px;" class="row">
              <div class="col-md-3">
                <label>Filter By Order Taker</label>
              </div>
              <div class="col-md-3">
                <select class="form-control ot_filter" name="" >
                @foreach ($ordertakers as $ot)
                  <option value="{{ $ot->id }}">{{$ot->name}}</option>
                @endforeach
                <option value="yes">Show All</option>
                </select>
              </div>
            </div>
          </form>
          @endif
            <form method="post" action="{{ route('all.orders' ) }}">
                <div class="row">
                    <div class="col-md-1 text-right">
                        <b>From</b>
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="from" value="{{ date('d-m-Y') }}" class="form-control" />
                    </div>
                    <div class="col-md-1 text-right">
                        <b>To</b>
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="to" value="{{ date('d/m/Y') }}" class="form-control" />
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-success btn-sm">Search</button>
                    </div>
                </div>
            </form>
        </div>
        </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-custom-th" id="dataTable" width="100%" cellspacing="0">
            <thead>
              <tr>
                <th >#</th>
                <th>Customer</th>
                <th>Order Taker</th>
                <th>Units</th>
                <th>Total</th>
                <th>Sub Total</th>
                <th>Recieved</th>
                <th>Balance</th>
                <th>Advance</th>
                <th>C Benefit</th>
                <th>Location</th>
                <th>Date</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($orders as $order)
              <tr>
                <input type="hidden" class="ot_id" name="ot_id" value="{{ $order->ordertaker->id }}">
                <td>{{ $order->id }}</td>
                <td>{{ $order->customers->user->name }}</td>
                <td>{{ $order->ordertaker->name }}</td>
                <td>{{ $order->unit }}</td>
                <td>{{ $order->subtotal + $order->amount_left - $order->advance }}</td>
                <td>{{ $order->subtotal }}</td>
                <td>{{ $order->received_amount }}</td>
                <td>{{ $order->amount - $order->received_amount  }}</td>
                @if($order->received_amount > ($order->subtotal + $order->amount_left - $order->advance))
                  <td>$order->amount_left</td>
                @else
                  <td>{{ $order->advance }}</td>
                @endif
                <td>{{ $order->c_benefit }}</td>
                <td>http://maps.google.com/maps?q=+{{ $order->customers->location_url }}</td>
                <td>{{ $order->created_at }}</td>
                 <td><a href="javascript:;" data-toggle="modal" data-target="#order-detail-popup" class="btn btn-sm btn-success view-details" id="{{ $order->id }}"><i class="fa fa-eye"></i></a>
                  </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="card-footer small text-muted">Total: <b>{{ $orders->sum('amount') }}</b> | Sub Total: <b>{{ $orders->sum('subtotal') }}</b> | Balance: <b>{{ $orders->sum('amount_left') }}</b> | Rec Amount: <b>{{ $orders->sum('received_amount') }}</b> @if(Auth::user()->role < 3) | A Ben: <b>{{ $orders->sum('a_benefit') }}</b> @endif | C Ben: <b>{{ $orders->sum('c_benefit') }}</b> | Advance: <b>{{ $orders->sum('advance') }}</b> | Units: <b>{{ $orders->sum('unit') }}</b>
      <button class="pull-right btn btn-info btn-sm" data-toggle="modal" data-target="#product-report-popup"><i class="fa fa-eye"></i></button>
      </div>
    </div>
  </div>
</div>
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
                <?php $show_in_modal = 0; ?>
              @foreach($product_report as $preport)
                @if($preport['amount'] != 0 || $preport['unit'] != 0)
                  <tr>
                    <td>{{ $preport['id'] }}</td>
                    <td>{{ $preport['name'] }}</td>
                    <td>{{ $preport['unit'] }}</td>
                    <td>{{ $preport['amount'] }}</td>
                    <?php $show_in_modal += $preport['amount']; ?>
                  </tr>
                @endif
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="alert alert-info text-left pull-left">
          <b>P.Total: </b>{{ $show_in_modal }}
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="order-detail-popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Invoice Detail <small></small></h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
        @if(Auth::user()->role < 3)
        <a href="" class="btn btn-primary approve-btn">Approve</a>
        <button onclick="window.location='printerplus://send?text='+document.getElementById('p').innerHTML;">
      Send to Printer+
    </button>
        @endif
      </div>
    </div>
  </div>
</div>
<form style="display: none" method="post" action="{{ route('approve.order.multiple') }}" id="multiple-approve">
    {{ csrf_field() }}
</form>
@endsection
@section('scripts')
<script type="text/javascript">
 $('.view-details').click(function(){
    console.log('es')
    var param = $(this).attr('id');
    console.log(param);
    $('.approve-btn').attr('href' , "{{ route('approve.invoice') }}/" + param);
    $('#order-detail-popup .modal-title small').text('(' + $(this).closest('tr').find('input').val() + ')');
    $('#order-detail-popup .modal-body').html('<h6 class="text-center">Loading ..</h6>');
    $.get('{{ route("order.detail") }}/' + param , function(success){
      $('#order-detail-popup .modal-body').html(success);
    });
  });
  $('.check-all').click(function(){
      if($(this).hasClass('revert')){
          var is_rev = true;
          $(this).removeClass('revert');
          $(this).text('check all');
      }
      else{
          var is_rev = false;
          $(this).addClass('revert');
          $(this).text('uncheck all');
      }
      $('tbody .approve-to').each(function(confirm){
          if(!is_rev){
              $(this).prop('checked' , 'checked');
              $('#multiple-approve').append($(this).closest('label').html());
              $('#multiple-approve .approve-to').last().attr('checked' , 'checked');
          }
          else{
              $(this).prop('checked' , false);
              $('#multiple-approve .approve-to').each(function(){
                  $(this).remove();
              });
          }
      });
  });
  
  $('#multiple-approve .approve-to').on('change' , function(){
      if(!this.checked){
          $(this).remove();
      }
  });
  $('tbody .approve-to').on('change' , function(){
   
      if(this.checked){
         console.log('yes');
          $('#multiple-approve').append($(this).closest('label').html());
          $('#multiple-approve .approve-to').last().attr('checked' , 'checked');
      }
      else{
          var this_val = $(this).val();
          $('#multiple-approve .approve-to').each(function(){
              if($(this).val() == this_val){
                  $(this).remove();
              }
          });
      }
  });
  $('.ot_filter').on('change' , function(){
    
    $ot_id=$(this).val();
    if($ot_id=='yes'){
        $('.ot_id').each(function(){
          $(this).parent().show();
        });
        $('#dataTable_info').show();
    }
    else{
      $('.ot_id').each(function(){
        $id=$(this).val();
        if($ot_id!=$id){
          $(this).parent().hide();
          $('#dataTable_info').hide();
        }
        else{
          $(this).parent().show();
        }
      });
    }
    
});
</script>
@endsection