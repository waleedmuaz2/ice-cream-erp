@extends('layouts.app')
@section('title') All OrderTaker @endsection
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
  <li class="breadcrumb-item">
    <a href="#">Users</a>
  </li>
  <li class="breadcrumb-item active">All Order Takers</li>
</ol>
<div class="row">
  <div class="col-md-12">
    <div class="card mb-3">
      <div class="card-header">
        <i class="fa fa-table"></i> Order Takers List</div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-custom-th" id="dataTable" width="100%" cellspacing="0">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>email</th>
                <th>phone</th>
                <th>T.Orders</th>
                <th>Ben.earned</th>
                <th>Ben.Paid</th>
                <th>Ben.Remain</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($ordertaker as $ot)
              <tr>
                <td>{{ $ot->id }}</td>
                <td>{{ $ot->name }}</td>
                <td>{{ $ot->email }}</td>
                <td>{{ $ot->phone }}</td>
                <td>{{ sizeof($ot->orders) }}</td>
                <td>{{ $ot->ordertaker->ben_earned }}</td>
                <td>{{ $ot->ordertaker->ben_paid}}</td>
                <td>{{ $ot->ordertaker->ben_earned - $ot->ordertaker->ben_paid}}</td>
                <td>
                  <button class="btn btn-sm btn-primary pay_button" data-route="{{ route('pay.ot.amount',$ot->id) }}">Pay</button>
                  <a href="{{ route('edit.ot' , $ot->id) }}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                  <a href="{{ route('delete.ot' , $ot->id) }}" class="btn btn-sm btn-danger delete-btn"><i class="fa fa-trash"></i></a> 
                  <a href="{{ route('ot.paid.history', $ot->id) }}" class="btn btn-dark btn-sm">History</a> 
                  @if($ot->is_blocked == 1)
                  <a href="{{ route('unblock.ot' , [$ot->id , 'unblock']) }}" class="btn btn-sm btn-success approve-btn">Unblock</a>
                  @else
                  <a href="{{ route('unblock.ot' , [$ot->id , 'block']) }}" class="btn btn-sm btn-warning approve-btn">block</a>
                  @endif                 
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="pay-ot-popup" tabindex="-1" role="dialog" aria-labelledby="pay-ot-popup-label" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pay Amount</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post" class="pay_form" action="">
          {{ csrf_field() }}
          <div class="form-group">
            <input type="number" name="amount" min="0" class="form-control">
          </div>
          <button class="btn btn-secondary btn-block">Continue</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
  $('.pay_button').on('click',function(e){
    e.preventDefault();
    console.log('yes');
    $('#pay-ot-popup').modal('show');
    $('.pay_form').attr('action', $(this).data('route'));
  });
</script>
@endsection
