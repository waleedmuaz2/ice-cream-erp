@extends('layouts.app')
@section('title') Pending Invoices @endsection
@section('content')
<?php error_reporting(0) ?>
<ol class="breadcrumb">
  <li class="breadcrumb-item">
    <a href="#">Invoices</a>
  </li>
  <li class="breadcrumb-item active">Pending Invoices</li>
</ol>
<div class="row">
  <div class="col-md-12">
    <div class="card mb-3">
      <div class="card-header">
        <i class="fa fa-table"></i> Invoices List
        <div class="col-md-10 pull-right">
            <form method="post" action="{{ route('date.filter' , 1) }}">
                {{ csrf_field() }}
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
                    @if(Auth::user()->role < 3)
                    <div class="col-md-1">
                        <button type="button" class="btn btn-success btn-sm check-all">check all</button>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-success btn-sm app-mult approve-btn">Approve</button>
                    </div>
                    @endif
                </div>
            </form>
        </div>
        </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-custom-th" id="dataTable" width="100%" cellspacing="0">
            <thead>
              <tr  class="header" id="myHeader">
                <th style="width: 20px">#</th>
                <th>Customer</th>
                <th>Units</th>
                <th>Total</th>
                <th>Sub Total</th>
                <th>Recieved</th>
                <th>Balance</th>
                <th>Advance</th>
                @if(Auth::user()->role < 3)
                <th>A Benefit</th>
                @endif
                <th>C Benefit</th>
                <th>Date</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($invoices as $invoice)
              <tr class="content">
                <input type="hidden" name="" value="{{ $invoice->created_at->diffForHumans() }}">
                <td style="width: 20px">{{ $invoice->id }}</td>
                
                <!--Customer Name-->
                
                 @if ( $invoice->received_amount < $invoice->subtotal  )
                <td style="color: red" data-changein="subtotal">{{ $invoice->customer->user->name }}</td>
                @elseif ( $invoice->received_amount > $invoice->subtotal && $invoice->amount_left > 0  )
                <td style="color: #CC9A2E" data-changein="subtotal">{{ $invoice->customer->user->name }}</td>
                @elseif ( $invoice->received_amount > $invoice->subtotal && $invoice->amount_left <= 0  )
                <td style="color: #28B463" data-changein="subtotal">{{ $invoice->customer->user->name }}</td>
                @elseif ( $invoice->received_amount == $invoice->subtotal && $invoice->amount_left <= 0 )
                <td style="color: #2ECC71" data-changein="subtotal">{{ $invoice->customer->user->name }}</td>
                @elseif ( $invoice->received_amount == $invoice->subtotal && $invoice->amount_left > 0 )
                <td style="color: #CC9A2E" data-changein="subtotal">{{ $invoice->customer->user->name }}</td>
                @elseif ( $invoice->received_amount == 0  )
                <td data-changein="subtotal">{{ $invoice->customer->user->name }}</td>
                @endif
                
   <!--Unit-->
                
                    @if ( $invoice->received_amount < $invoice->subtotal  )
                <td style="color: red" data-changein="subtotal">{{ $invoice->unit }}</td>
                @elseif ( $invoice->received_amount > $invoice->subtotal  )
                <td style="color: #2ECC71" data-changein="subtotal">{{ $invoice->unit }}</td>
                @elseif ( $invoice->received_amount == $invoice->subtotal && $invoice->amount_left <= 0 )
                <td style="color: #2ECC71" data-changein="subtotal">{{ $invoice->unit }}</td>
                @elseif ( $invoice->received_amount == $invoice->subtotal && $invoice->amount_left > 0 )
                <td style="color: #CC9A2E" data-changein="subtotal">{{ $invoice->unit }}</td>
                @elseif ( $invoice->received_amount == 0  )
                <td data-changein="subtotal">{{ $invoice->unit }}</td>
                @endif
                
                
                <!--Total-->
                
                       
                @if ( $invoice->received_amount < $invoice->subtotal  )
                <td style="color: red" data-changein="subtotal">{{ $invoice->amount }}</td>
                @elseif ( $invoice->received_amount > $invoice->subtotal && $invoice->amount_left > 0  )
                <td style="color: #CC9A2E" data-changein="subtotal">{{ $invoice->amount }}</td>
                @elseif ( $invoice->received_amount > $invoice->subtotal && $invoice->amount_left <= 0  )
                <td style="color: #28B463" data-changein="subtotal">{{ $invoice->amount }}</td>
                @elseif ( $invoice->received_amount == $invoice->subtotal && $invoice->amount_left <= 0 )
                <td style="color: #2ECC71" data-changein="subtotal">{{ $invoice->amount }}</td>
                @elseif ( $invoice->received_amount == $invoice->subtotal && $invoice->amount_left > 0 )
                <td style="color: #CC9A2E" data-changein="subtotal">{{ $invoice->amount }}</td>
                @elseif ( $invoice->received_amount == 0  )
                <td data-changein="subtotal">{{ $invoice->amount }}</td>
                @endif
                
                
                <!--Subtotal-->
               
                @if ( $invoice->received_amount < $invoice->subtotal  )
                <td style="color: red" data-changein="subtotal">{{ $invoice->subtotal }}</td>
                @elseif ( $invoice->received_amount > $invoice->subtotal  )
                <td style="color: #2ECC71" data-changein="subtotal">{{ $invoice->subtotal }}</td>
                @elseif ( $invoice->received_amount == $invoice->subtotal && $invoice->amount_left <= 0 )
                <td style="color: #2ECC71" data-changein="subtotal">{{ $invoice->subtotal }}</td>
                @elseif ( $invoice->received_amount == $invoice->subtotal && $invoice->amount_left > 0 )
                <td style="color: #CC9A2E" data-changein="subtotal">{{ $invoice->subtotal }}</td>
                @elseif ( $invoice->received_amount == 0  )
                <td data-changein="subtotal">{{ $invoice->subtotal }}</td>
                @endif
                
                
                <!--Received Amount-->
                
                @if ( $invoice->amount_left <= 0  )
                <td style="color: #2ECC71" data-changein="received_amount">{{ $invoice->received_amount }}</td>
                @elseif ( $invoice->received_amount == 0 && $invoice->subtotal != $invoice->received_amount )
                <td style="color: red" data-changein="received_amount">{{ $invoice->received_amount }}</td>
                @elseif ( $invoice->received_amount < $invoice->subtotal && $invoice->amount_left > 0 )
                <td style="color: #CC9A2E" data-changein="received_amount">{{ $invoice->received_amount }}</td>
                @elseif ( $invoice->received_amount > $invoice->subtotal && $invoice->amount_left > 0 )
                <td style="color: #28B463" data-changein="received_amount">{{ $invoice->received_amount }}</td>
                @elseif ( $invoice->received_amount == $invoice->subtotal && $invoice->amount_left > 0 )
                <td style="color: #CC9A2E" data-changein="received_amount">{{ $invoice->received_amount }}</td>
                @elseif ( $invoice->received_amount > 0 && $invoice->subtotal < 0 && $invoice->amount_left > 0 )
                <td style="color: #2ECC71" data-changein="received_amount">{{ $invoice->received_amount }}</td>
               
                @endif
                
                    <!--Balance-->
                
                @if ($invoice->amount_left > 0)
                <td style="color: red">{{ $invoice->amount_left }}</td>
                @endif
                @if ($invoice->amount_left <= 0)
                <td style="color: #2ECC71">{{ $invoice->amount_left }}</td>
                @endif
                <td>{{ $invoice->advance }}</td>
                
                <!--Benefit-->
                     
                @if(Auth::user()->role < 3)
                @if( $invoice->received_amount >= $invoice->subtotal && $invoice->amount_left <= 0 )
                <td style= "color: #2ECC71">{{ $invoice->a_benefit }}</td>
                @elseif( $invoice->received_amount >= $invoice->subtotal && $invoice->amount_left > 0 )
                <td style= "color: #2ECC71">{{ $invoice->a_benefit }}</td>
                @elseif( $invoice->subtotal == $invoice->received_amount && $invoice->amount_left > 0 )
                <td style= "color: #CC9A2E">{{ $invoice->a_benefit }}</td>
                @elseif( $invoice->subtotal >= $invoice->received_amount && $invoice->amount_left > 0 )
                <td style= "color: #CC9A2E">{{ $invoice->a_benefit }}</td>
                @elseif( $invoice->subtotal <0 )
                <td style= "color: red">{{ $invoice->a_benefit }}</td>
                @elseif( $invoice->amount == $invoice->received_amount && $invoice->amount_left <= 0 )
                <td style= "color: #2ECC71">{{ $invoice->a_benefit }}</td>
                 @elseif( $invoice->amount =! $invoice->received_amount && $invoice->amount_left > 0 )
                <td style= "color: #2ECC71">{{ $invoice->a_benefit }}</td>
                 @elseif( $invoice->amount != $invoice->received_amount && $invoice->amount_left <= 0 )
                <td style= "color: #2ECC71">{{ $invoice->a_benefit }}</td>
                
                @elseif( $invoice->received_amount <= 0 )
                <td style= "color: red">{{ $invoice->a_benefit }}</td>
                @endif
                @endif
                <td>{{ $invoice->c_benefit }}</td>
                <td>{{ $invoice->created_at }}</td>
                <td>
                  <a href="javascript:;" data-toggle="modal" data-target="#invoice-detail-popup" class="btn btn-sm btn-success view-details" id="{{ $invoice->id }}"><i class="fa fa-eye"></i></a>
                  <a href="{{ route('customer.invoices' , $invoice->customer_id) }}" class="btn btn-sm prev-record btn-primary pull-right" style="float:left;"><i class="fa fa-reply"></i></a>
                  
                  @if(Auth::user()->role < 3)
                  <a href="{{ route('edit.invoice' , $invoice->id) }}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                  <a href="{{ route('delete.invoice' , $invoice->id) }}" class="btn btn-sm btn-danger delete-btn"><i class="fa fa-trash"></i></a>
                  <a href="#" id="btnStatus2" onclick="window.location='printerplus://send?text='+document.getElementById('p').innerHTML;" onclick="btnStatus2_Click" class="btn btn-sm btn-info"><i class="fa fa-print"></i></a>
                  <a href="#" onclick="window.location='printerplus://send?text='invoice.detail.getElementById('invoice.detail').innerHTML;" class="btn btn-sm btn-info"><i class="fa fa-print"></i></a>
                  <label class="btn btn-default btn-sm">
                      <input type="checkbox" name="approve_to[]" value="{{ $invoice->id }}" class="approve-to" checked />
                  </label>
                  @elseif(Auth::user()->role == 3 && $invoice->is_approved == null)
                  <a href="{{ route('edit.invoice' , $invoice->id) }}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                  <a href="{{ route('delete.invoice' , $invoice->id) }}" class="btn btn-sm btn-danger delete-btn"><i class="fa fa-trash"></i></a>                  
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      
      <!--Footer Data-->
      
      <div class="header" id="myHeader" class="card-footer small text-muted">Total: <b class="amount">{{ $invoices->sum('amount') }}
      </b> 
      |Db value: <b>{{ $invoices->sum('p_amount') }}
      </b> 
      | Sub Total: <b class="subtotal">{{ $invoices->sum('subtotal') }}
      </b> 
      | Balance: <b class="amount_left">{{ $invoices->sum('amount_left') }}
      </b> 
      | Rec Amount: <b class="received_amount">{{ $invoices->sum('received_amount') }}
      </b> 
      @if(Auth::user()->role < 3) | A Ben: <b>{{ $invoices->sum('a_benefit') }}
      </b> 
      |Actual Ben: <b>{{ $invoices->sum('subtotal')-$invoices->sum('p_amount') }}
      </b> 
      @endif 
      | C Ben: <b>{{ $invoices->sum('c_benefit') }}
      </b> 
      | Advance: <b>{{ $invoices->sum('advance') }}
      </b> 
      | Units: <b class="unit">{{ $invoices->sum('unit') }}
      </b>
      <button class="pull-right btn btn-info btn-sm" data-toggle="modal" data-target="#product-report-popup"><i class="fa fa-eye"></i></button>
      </div>
    </div>
  </div>
</div>
      <div class="modal-footer">
       </div>
    </div>
  </div>
</div>
<div class="modal fade" id="invoice-detail-popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
        <button id="btnStatus2" onclick="window.location='printerplus://send?text='+document.getElementById('p').innerHTML;" onclick="btnStatus2_Click">
      Send to Printer+
    </button>
        @endif
      </div>
    </div>
  </div>
</div>
<form style="display: none" method="post" action="{{ route('approve.invoice.multiple') }}" id="multiple-approve">
    {{ csrf_field() }}
</form>
@endsection
@section('scripts')
<script type="text/javascript">
  $('.view-details').click(function(){
    var param = $(this).attr('id');
    $('.approve-btn').attr('href' , "{{ route('approve.invoice') }}/" + param);
    $('#invoice-detail-popup .modal-title small').text('(' + $(this).closest('tr').find('input').val() + ')');
    $('#invoice-detail-popup .modal-body').html('<h6 class="text-center">Loading ..</h6>');
    $.get('{{ route("invoice.detail") }}/' + param , function(success){
      $('#invoice-detail-popup .modal-body').html(success);
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
      $('tbody .approve-to').each(function(){
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
          
          updateCardFooterData(this);
      });
  });
  $('#multiple-approve .approve-to').on('change' , function(){
      if(!this.checked){
          $(this).remove();
      }
  });
  $('tbody .approve-to').on('change' , function(){
      if(this.checked){
          $('#multiple-approve').append($(this).closest('label').html());
      }
      else{
          var this_val = $(this).val();
          $('#multiple-approve .approve-to').each(function(){
              if($(this).val() == this_val){
                  $(this).remove();
              }
          });
      }
      
      updateCardFooterData(this)
  });
  
  function updateCardFooterData(elm)
  {
      var tr = $(elm).closest('tr');
      if($(elm).is(':checked'))
      {
          $('b.amount').text(parseFloat($('b.amount').text()) + parseFloat($(tr).find('[data-changein="amount"]').text()));
          $('b.subtotal').text(parseFloat($('b.subtotal').text()) + parseFloat($(tr).find('[data-changein="subtotal"]').text()));
          $('b.received_amount').text(parseFloat($('b.received_amount').text()) + parseFloat($(tr).find('[data-changein="received_amount"]').text()));
          $('b.unit').text(parseFloat($('b.unit').text()) + parseFloat($(tr).find('[data-changein="unit"]').text()));
      }
      else
      {
          $('b.amount').text(parseFloat($('b.amount').text()) - parseFloat($(tr).find('[data-changein="amount"]').text()));
          $('b.subtotal').text(parseFloat($('b.subtotal').text()) - parseFloat($(tr).find('[data-changein="subtotal"]').text()));
          $('b.received_amount').text(parseFloat($('b.received_amount').text()) - parseFloat($(tr).find('[data-changein="received_amount"]').text()));
          $('b.unit').text(parseFloat($('b.unit').text()) - parseFloat($(tr).find('[data-changein="unit"]').text()));
      }
  }
  

</script>
@endsection