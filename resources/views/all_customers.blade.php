@extends('layouts.app')
@section('title') All Customer @endsection
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
  <li class="breadcrumb-item">
    <a href="#">Customers</a>
  </li>
  <li class="breadcrumb-item active">All Customer</li>
</ol>
<div class="row">
  <div class="col-md-12">
    <div class="card mb-3">
      <div class="card-header">
        <i class="fa fa-table"></i> <b><?php echo empty($subadmin_name) ? '' : $subadmin_name ?></b> Customers List</div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-custom-th" id="dataTable" width="100%" cellspacing="0">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Address</th>
                <th>Phone</th>
                <th>CNIC</th>
                <th>Freezer Model</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($customers as $customer)
              <tr>
                <td>{{ $customer->id }}</td>
                <td>{{ $customer->user->name }}</td>
                <td>{{ $customer->address }}</td>
                <td>{{ $customer->phone }}</td>
                <td>{{ $customer->cnic }}</td>
                <td>{{ $customer->freezer_model }}</td>
                <td>
                  @if(Auth::user()->role == 1 || $customer->created_by == Auth::id())
                  <a href="{{ route('customer.invoices' , $customer->id) }}" class="btn btn-sm btn-success"><i class="fa fa-eye"></i></a>
                  @endif
                  <a href="{{ route('edit.customer' , $customer->id) }}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                  <a href="{{ route('delete.customer' , $customer->id) }}" class="btn btn-sm btn-danger delete-btn"><i class="fa fa-trash"></i></a>
                  <a href="javascript:;" data-toggle="modal" data-target="#agreement-popup" class="btn btn-info btn-sm see-ag">
                    <span id="ag-img" style="display: none;">{{ asset($customer->image) }}</span>
                    <i class="fa fa-image"></i>
                  </a>
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
<div class="modal fade" id="agreement-popup" tabindex="-1" role="dialog" aria-labelledby="agreement-popup" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Agreement Image</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <img src="" id="append-ag-img" style="height: 500px;width: 100%">
      </div>
      <div class="modal-footer">
        <button class="btn btn-default" type="button" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
  $('.see-ag').click(function(){
    var src = $(this).find('span').text();
    $('#append-ag-img').attr('src' , src);
  });
</script>
@endsection