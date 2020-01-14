  target="_blank"    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav navbar-sidenav" style="overflow-y : scroll;" id="exampleAccordion">
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Dashboard">
          <a class="nav-link" href="{{ route('admin.home') }}">
            <i class="fa fa-fw fa-dashboard"></i>
            <span class="nav-link-text">Dashboard</span>
          </a>
        </li>
      
         @if(Auth::user()->role < 3 || Auth::user()->role ==5)    
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Components">
          <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#customer-col" data-parent="#exampleAccordion">
            <i class="fa fa-fw fa-list"></i>
            <span class="nav-link-text">Customers</span>
          </a>
          
          <ul class="sidenav-second-level collapse" id="customer-col">
            <li>
              <a href="{{ route('add.customer') }}">Add Customer</a>
            </li>
              @if(Auth::user()->role != 5)
            <li>
              <a href="{{ route('all.customers') }}">All Customers</a>
            </li>
             @endif
          </ul>
        </li>
        @endif
         @if(Auth::user()->role < 3 ) 
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Components">
          <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#user-col" data-parent="#exampleAccordion">
            <i class="fa fa-fw fa-user"></i>
            <span class="nav-link-text">Employee</span>
          </a>
          <ul class="sidenav-second-level collapse" id="user-col">
          
            @if(Auth::user()->role < 3)
             <li>
              <a href="{{ route('all.ot') }}">All Order Takers</a>
             </li>
            @endif
            <li>
              <a href="{{ route('all.sellers') }}">All Sellers</a>
            </li>
          </ul>
        </li>
        @endif
         @if(Auth::user()->role !=5 )
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Example Pages">
          <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#invoice-col" data-parent="#exampleAccordion">
            <i class="fa fa-fw fa-file"></i>
            <span class="nav-link-text">Invoices</span>
          </a>
          <ul class="sidenav-second-level collapse" id="invoice-col">
            @if(Auth::user()->role < 4)
            <li>
              <a href="{{ route('add.invoice') }}">New Invoice</a>
           
              <a href="{{ route('invoices' , 'unapproved') }}">Un Approved Invoices</a>
            </li>
            @else
             <li>
              <a href="{{ route('add.invoice') }}">Send Invoice</a>
            </li>
            <li>
              <a href="{{ route('customer.invoices' , Auth::user()->customer_id) }}">My Invoices</a>
            </li>
            @endif
          </ul>
        </li>
         @endif
          @if(Auth::user()->role < 4 || Auth::user()->role == 5)
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Example Pages">
          <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#order-col" data-parent="#exampleAccordion">
            <i class="fa fa-fw fa-file"></i>
            <span class="nav-link-text">Orders</span>
          </a>
          <ul class="sidenav-second-level collapse" id="order-col">
            @if(Auth::user()->role !=4 )
              <li><a href="{{ route('create.order') }}">New Order</a></li>
            @endif
              <li><a href="{{ route('important.orders') }}">Important Orders</a></li>
              <li><a href="{{ route('unconfirmed.orders') }}">Unconfirmed Orders</a></li>
              <li><a href="{{ route('confirmed.orders.seller') }}">Seller Confirmed Orders</a></li>
              <li><a href="{{ route('confirmed.orders.admin') }}">Admin Confirmed Orders</a></li>
              <li><a href="{{ route('all.orders') }}">All Orders</a></li>
          </ul>
        </li>
        @endif
  
       
       
      </ul>
      <ul class="navbar-nav sidenav-toggler">
        <li class="nav-item">
          <a class="nav-link text-center" id="sidenavToggler">
            <i class="fa fa-fw fa-angle-left"></i>
          </a>
        </li>
      </ul>
      <ul class="navbar-nav ml-auto">
        @if(Auth::user()->role < 3)
        <?php $notify = App\Notification::where('notify_to' , Auth::id())->orderBy('id' , 'desc')->limit(10)->get(); ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle mr-lg-2" id="alertsDropdown" href="javascript:;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-fw fa-bell"></i>
            <span class="d-lg-none">Alerts
              @if(Auth::user()->is_notified == 1)
              <span class="badge badge-pill badge-warning notify-dot"><i class="fa fa-fw fa-circle"></i></span>
              @endif
            </span>
            @if(Auth::user()->is_notified == 1)
            <span class="indicator text-warning d-none d-lg-block notify-dot">
              <i class="fa fa-fw fa-circle"></i>
            </span>
            @endif
          </a>
          <div class="dropdown-menu" aria-labelledby="alertsDropdown" style="left: auto;right: 18px;min-width: 350px">
            <h6 class="dropdown-header">Notifications:</h6>
            <div class="dropdown-divider"></div>
            @foreach($notify as $n)
            <a class="dropdown-item alert-item" href="{{ route('invoices' , 'unapproved') }}">
                <span style="display: none" class="notify-id" id="{{ $n->id }}"></span>
              <span class="text-primary" style="font-size: 14px">
                Customer<strong> {{ $n->invoice->customer->user->name }}</strong>
              </span>
              <span class="text-info pull-right" style="font-size: 14px">
                By<strong> {{ $n->user->name }}</strong>
              </span>
              <br />
              <div class="dropdown-message small" style="font-size: 12px">Total: <b>{{ $n->invoice->amount }}</b> | Subtotal: <b>{{ $n->invoice->subtotal }}</b> | Balance: <b>{{ $n->invoice->amount_left }}</b>
              <br />
              <span class="float-right text-success">Invoice No <b> {{ $n->invoice->id }}</b></span></div>
            </a>
            <div class="dropdown-divider"></div>
            @endforeach
            @if(sizeof($notify) == 0)
            <a class="dropdown-item alert-item" href="#">
              <span class="text-danger" style="font-size: 16px">
                No New Notification
              </span>
            </a>
            @endif
          </div>
        </li>
        @endif
         <li style="
    margin-right: 10px;"
>
             
             
            <a href="https://pos.scoopscreamery.pk"  target="_blank" class="nav-link btn-block space space1" style="margin-right: 8px; text-align:center; color: white;background-color: #636F7A !important;border-radius: 8px;">
                    <b> Got to Pos</b>
                
            </a>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle btn-block" style="padding: 8px;color: white;background-color: #636F7A !important;border-radius: 8px;" data-toggle="dropdown" href="#">
                    <b><i class="fa fa-user"></i> {{ Auth::user()->name }}</b><small> @if(Auth::user()->role == 1)Super Admin @elseif(Auth::user()->role == 2)Admin @elseif(Auth::user()->role == 3)Seller @elseif(Auth::user()->role == 4)Customer @else Order Taker @endif</small>
                <span class="caret"></span>
            </a>
        <ul class="dropdown-menu" style="width: 100%">
          <li><a href="{{ route('logout') }}"
              onclick="event.preventDefault();
                       document.getElementById('logout-form').submit();" class="nav-link" style="color: black;padding: 5px 16px">
              <i class="fa fa-fw fa-sign-out"></i> Sign Out
          </a></li>
        </ul>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
              {{ csrf_field() }}
          </form>
      </li>
      </ul>
    </div>