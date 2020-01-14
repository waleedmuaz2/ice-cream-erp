@extends('layouts.app')

@section('title') Home @endsection

@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
  <li class="breadcrumb-item">
    <a href="#">Dashboard</a>
  </li>
  <li class="breadcrumb-item active">My Dashboard</li>
</ol>
<!-- Icon Cards-->
<div class="row">
    <?php $columns = Auth::user()->role < 3 ? 'col-md-3' : 'col-md-4'; ?>
  @if(Auth::user()->role < 3)
  <div class="{{ $columns }} mb-3">
    <div class="card text-white bg-primary o-hidden h-100">
      <div class="card-body">
        <div class="card-body-icon">
          <i class="fa fa-fw fa-comments"></i>
        </div>
        <div class="mr-5">{{ $tcustomers }} Customers!</div>
      </div>
      <a class="card-footer text-white clearfix small z-1" href="{{ route('all.customers') }}">
        <span class="float-left">View Details</span>
        <span class="float-right">
          <i class="fa fa-angle-right"></i>
        </span>
      </a>
    </div>
  </div>
  @endif
    @if(Auth::user()->role != 5)
  <div class="{{ $columns }} mb-3">
    <div class="card text-white bg-warning o-hidden h-100">
      <div class="card-body">
        <div class="card-body-icon">
          <i class="fa fa-fw fa-list"></i>
        </div>
        <div class="mr-5">{{ $app_in }} Approved Invoices!</div>
      </div>
      <a class="card-footer text-white clearfix small z-1" href="{{ route('invoices') }}">
        <span class="float-left">View Details</span>
        <span class="float-right">
          <i class="fa fa-angle-right"></i>
        </span>
      </a>
    </div>
  </div>
  <div class="{{ $columns }} mb-3">
    <div class="card text-white bg-success o-hidden h-100">
      <div class="card-body">
        <div class="card-body-icon">
          <i class="fa fa-fw fa-shopping-cart"></i>
        </div>
        <div class="mr-5">{{ $unapp_in }} Pending Invoices!</div>
      </div>
      <a class="card-footer text-white clearfix small z-1" href="{{ route('invoices' , 'unapproved') }}">
        <span class="float-left">View Details</span>
        <span class="float-right">
          <i class="fa fa-angle-right"></i>
        </span>
      </a>
    </div>
  </div>
  <div class="{{ $columns }} mb-3">
    <div class="card text-white bg-danger o-hidden h-100">
      <div class="card-body">
        <div class="card-body-icon">
          <i class="fa fa-fw fa-support"></i>
        </div>
        <div class="mr-5">{{ sizeof($today_in) }} Today Invoices!</div>
      </div>
      <a class="card-footer text-white clearfix small z-1" href="javascript:;">
        <span class="float-left">View Details</span>
        <span class="float-right">
          <i class="fa fa-angle-right"></i>
        </span>
      </a>
    </div>
  </div>

</div>

<div class="row">
  <?php $invoices = $today_in; ?>
  <div class="col-lg-10">
    <!-- Example Bar Chart Card-->
    <div class="card mb-3">
      <div class="card-header">
        <i class="fa fa-table"></i> Today Invoices</div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-custom-th" id="dataTable" width="100%" cellspacing="0">
            <thead>
              <tr class="header" id="myHeader">
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
              <tr>
                <input type="hidden" name="" value="{{ $invoice->created_at->diffForHumans() }}">
                <td style="width: 20px">{{ $invoice->id }}</td>
               
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
                  @if(Auth::user()->role < 3)
                 <a href="{{ route('print.invoice' , $invoice->id) }}" class="btn btn-sm btn-info"><i class="fa fa-print"></i></a>
                  <a href="{{ route('customer.invoices' , $invoice->customer_id) }}" class="btn btn-sm prev-record btn-primary pull-right" style="margin-top:1px;"><i class="fa fa-reply"></i></a>
                  
                  <a href="{{ route('edit.invoice' , $invoice->id) }}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                  <a href="{{ route('delete.invoice' , $invoice->id) }}" class="btn btn-sm btn-danger delete-btn"><i class="fa fa-trash"></i></a>
                  <span id="{{ $invoice->is_approved }}" style="display: none"></span>
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
      <div class="header" id="myHeader" class="card-footer small text-muted">Total: <b>{{ $invoices->sum('amount') }}</b> | Sub Total: <b>{{ $invoices->sum('subtotal') }}</b> | Balance: <b>{{ $invoices->sum('amount_left') }}</b> | Rec Amount: <b>{{ $invoices->sum('received_amount') }}</b> @if(Auth::user()->role == 1) | A Ben: <b>{{ $invoices->sum('a_benefit') }}</b>@endif | C Ben: <b>{{ $invoices->sum('c_benefit') }}</b> | Advance: <b>{{ $invoices->sum('advance') }}</b> | Units: <b>{{ $invoices->sum('unit') }}</b>
      
    <!--added-->
      
      <button class="pull-right btn btn-info btn-sm" data-toggle="modal" data-target="#product-report-popup"><i class="fa fa-eye"></i></button>
      
      <!--added-->
      
      </div>
    </div>
  </div>
  <div class="col-lg-2">
    <div class="card mb-3">
      <div class="card-header">
        <i class="fa fa-bar-chart"></i> My Record</div>
      <div class="card-body">
        <div class="row">
          <div class="col-sm-12 text-center my-auto">
            <div class="h4 mb-0 text-primary">{{ $total_sell }}</div>
            <div class="small text-muted">Total <?php echo Auth::user()->role < 3 ? 'Sell' : 'Amount' ?></div>
            <hr>
            <div class="h4 mb-0 text-warning">{{ $balance }}</div>
            <div class="small text-muted">Amount Left</div>
            <hr>
            @if(Auth::user()->role < 3)
            <div class="h4 mb-0 text-success">{{ $admin_benefit }}</div>
            <div class="small text-muted">Admin Benefit</div>
            @else
            <div class="h4 mb-0 text-success">{{ $customer_benefit }}</div>
            <div class="small text-muted">Customer Benefit</div>
            @endif
            @if(Auth::user()->role == 1)
            <div class="form-group"></div>
            <a href="{{ route('admin.sell.clear') }}" class="btn btn-danger btn-block approve-btn btn-sm">Clear Record</a>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!--added-->
  
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


<!--added-->

</div>
@endif
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
        @endif
        <button onclick="window.location='printerplus://send?text='+document.getElementById('p').innerHTML;">
      Send to Printer+
      </button>
      </div>
    </div>
  </div>
</div>
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
  });
</script>
@endsection