@extends('layouts.app')
@section('title') Seller Invoices @endsection
@section('content')
<?php error_reporting(0) ?>
<ol class="breadcrumb">
  <li class="breadcrumb-item">
    <a href="#">Invoices</a>
  </li>
  <li class="breadcrumb-item active">Seller Invoices</li>
</ol>
<div class="row">
  <div class="col-md-12">
    <div class="card mb-3">
      <div class="card-header">
        <i class="fa fa-table"></i> <b>{{ $seller->name }}</b> Invoices List</div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-custom-th" id="dataTable" width="100%" cellspacing="0">
            <thead>
              <tr>
                <th>#</th>
                <th>Customer</th>
                <th>Units</th>
                <th>Total</th>
                <th>Sub Total</th>
                <th>Recieved</th>
                <th>Balance</th>
                <th>Advance</th>
                <th>A Benefit</th>
                <th>C Benefit</th>
                <th>Date</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($invoices as $invoice)              
              <tr>
                <input type="hidden" name="" value="{{ $invoice->created_at->diffForHumans() }}">
                <td>{{ $invoice->id }}</td>
                <td>{{ $invoice->customer->user->name }}</td>
                <td>{{ $invoice->unit }}</td>
                <td>{{ $invoice->amount }}</td>
                <td>{{ $invoice->subtotal }}</td>
                <td>{{ $invoice->received_amount }}</td>
                <td>{{ $invoice->amount_left }}</td>
                <td>{{ $invoice->advance }}</td>
                <td>{{ $invoice->a_benefit }}</td>
                <td>{{ $invoice->c_benefit }}</td>
                <td>{{ $invoice->created_at }}</td>
                <td>
                  <span style="display: none;" class="is-approved" id="{{ $invoice->is_approved }}"></span>
                  <a href="javascript:;" data-toggle="modal" data-target="#invoice-detail-popup" class="btn btn-sm btn-success view-details" id="{{ $invoice->id }}"><i class="fa fa-eye"></i></a>
                  @if(Auth::user()->role < 3)
                  <a href="{{ route('edit.invoice' , $invoice->id) }}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                  <a href="{{ route('delete.invoice' , $invoice->id) }}" class="btn btn-sm btn-danger delete-btn"><i class="fa fa-trash"></i></a>
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
      <div class="card-footer small text-muted">Total: <b>{{ $invoices->sum('amount') }}</b> | Sub Total: <b>{{ $invoices->sum('subtotal') }}</b> | Balance: <b>{{ $invoices->sum('amount_left') }}</b> | Rec Amount: <b>{{ $invoices->sum('received_amount') }}</b> | A Ben: <b>{{ $invoices->sum('a_benefit') }}</b> | C Ben: <b>{{ $invoices->sum('c_benefit') }}</b> | Advance: <b>{{ $invoices->sum('advance') }}</b> | Units: <b>{{ $invoices->sum('unit') }}</b>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="invoice-detail-popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style="width: 100%" id="exampleModalLabel">Invoice Detail <small></small></h5>
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
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
  $('.view-details').click(function(){
    var param = $(this).attr('id');
    @if(Auth::user()->role < 3){
        if($(this).closest('tr').find('.is-approved') != 1){
        $('.approve-btn').hide();
      }
      else{
        $('.approve-btn').show();
        $('.approve-btn').attr('href' , "{{ route('approve.invoice') }}/" + param);
      }
    }
    @endif
    $('#invoice-detail-popup .modal-title small').text('(' + $(this).closest('tr').find('input').val() + ')');
    $('#invoice-detail-popup .modal-body').html('<h6 class="text-center">Loading ..</h6>');
    $.get('{{ route("invoice.detail") }}/' + param , function(success){
      $('#invoice-detail-popup .modal-body').html(success);
    });
  });
</script>
@endsection