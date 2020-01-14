<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>Scoops - Login</title>
  <!-- Bootstrap core CSS-->
    <link href="{{ asset('vendor_view/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
      <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor_view/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">
      <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin.css') }}" rel="stylesheet">
    <link href="{{ asset('css/toastr.min.css') }}" rel="stylesheet">
</head>

<body class="bg-dark">
  <div class="container">
    <div class="card card-login mx-auto mt-5">
      <div class="card-header">Enter Your Login Details</div>
      <div class="card-body">
        <form method="post" action="{{ route('login') }}">
            {{ csrf_field() }}
          <div class="form-group">
            <label for="exampleInputEmail1">Email address</label>
            <input class="form-control" id="exampleInputEmail1" type="email" aria-describedby="emailHelp" placeholder="Enter email" name="email">
          </div>
          <div class="form-group">
            <label for="exampleInputPassword1">Password</label>
            <input class="form-control" id="exampleInputPassword1" type="password" placeholder="Password" name="password">
          </div>
          <div class="form-group"></div>
          <button class="btn btn-primary btn-block">Login</button>
        </form>
      </div>
    </div>
  </div>
  <script src="{{ asset('vendor_view/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script type="text/javascript">
      @if(session('success'))
      toastr.success("{{ session('success') }}")
      @elseif(session('error'))
      toastr.error("{{ session('error') }}")
      @endif
    </script>
</body>
</html>
