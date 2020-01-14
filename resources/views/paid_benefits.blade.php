@extends('layouts.app')
@section('title') OT benefit Paid History @endsection
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
  <li class="breadcrumb-item">
    <a href="#">Home</a>
  </li>
  <li class="breadcrumb-item active"> OT Benefit Paid History</li>
</ol>
<div class="row">
  <div class="col-md-12">
    <div class="card mb-3">
      <div class="card-header">
        <i class="fa fa-table"></i> Paid History
        <button class="btn btn-primary pull-right" data-target="#pay-popup" data-toggle="modal"></button>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-custom-th" id="dataTable" width="100%" cellspacing="0">
            <thead>
              <tr>
                <th>ID</th>
                <th>Total</th>
                <th>Paid</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              @foreach($paid_benefits as $pay_amount)
              <tr>
                <td>{{ $pay_amount->id }}</td>
                <td>{{ ($pay_amount->total_is - $pay_amount->paid) }}</td>
                <td>{{ $pay_amount->paid }}</td>
                <td>{{ $pay_amount->created_at->format('d l Y') }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="pay-popup" tabindex="-1" role="dialog" aria-labelledby="pay-popup-label" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pay Amount</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post" action="{{ route('pay.amount') }}">
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