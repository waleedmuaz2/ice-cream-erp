@extends('layouts.app')

@section('title') Edit Area @endsection

@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
  <li class="breadcrumb-item">
    <a href="#">Area</a>
  </li>
  <li class="breadcrumb-item active">Edit Area</li>
</ol>
<div class="row">
  <div class="col-md-6 m-auto">
    <div class="card mb-3">
      <div class="card-header">
        Enter Area Details
      </div>
      <div class="card-body">
        <form method="post" action="{{ route('save.area') }}">
          {{ csrf_field() }}
          <div class="form-group">
            <label>Area Name <span style="opacity: 0.5; font-style: italic;">(Required)</span></label>
            <input class="form-control" type="text" placeholder="Enter Area Name" name="name" required="" value="{{ $area->name }}">
            @if($errors->has('name'))
            <div class="alert alert-danger">{{ $errors->first('name') }}</div>
            @endif
          </div>
          <button id="button" type="submit" class="btn btn-primary btn-block">Update Area</button>
        </form>
      </div>
    </div>
  </div>
@endsection