@extends('layouts.app')
@section('title') All Sellers @endsection
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
  <li class="breadcrumb-item">
    <a href="#">Users</a>
  </li>
  <li class="breadcrumb-item active">All Sellers</li>
</ol>
<div class="row">
  <div class="col-md-12">
    <div class="card mb-3">
      <div class="card-header">
        <i class="fa fa-table"></i> <b><?php echo empty($subadmin_name) ? '' : $subadmin_name ?></b> Sellers List</div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-custom-th" id="dataTable" width="100%" cellspacing="0">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>email</th>
                <th>phone</th>
                <th>T.Invoices</th>
                <th>T.Sells</th>
                <th>Created At</th>
              </tr>
            </thead>
            <tbody>
              @foreach($sellers as $seller)
              <tr>
                <td>{{ $seller->id }}</td>
                <td>{{ $seller->name }}</td>
                <td>{{ $seller->email }}</td>
                <td>{{ $seller->phone }}</td>
                <td>{{ sizeof($seller->invoices) }}</td>
                <td>{{ $seller->invoices->sum('amount') }}</td>
                <td>
                  <a href="{{ route('view.seller.sells' , $seller->id) }}" class="btn btn-sm btn-success"><i class="fa fa-eye"></i> view sells</a>
                  <a href="{{ route('edit.seller' , $seller->id) }}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                  <a href="{{ route('delete.seller' , $seller->id) }}" class="btn btn-sm btn-danger delete-btn"><i class="fa fa-trash"></i></a>                  
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
@endsection