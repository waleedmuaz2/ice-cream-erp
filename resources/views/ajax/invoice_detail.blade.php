<!--<?php error_reporting(0) ?>
<div id="report" class="table-responsive">
  <table class="table table-bordered table-custom-th" width="100%" cellspacing="0">
      <tr>
        <th>Product</th>
        <th>Price</th>
        <th>Units</th>
        <th>Amount</th>
      </tr>
      @foreach($idetails as $d)
      <tr>
        <td>{{ $d->product->name }}</td>
        <td>
          <?php $is_c_price = App\CustomPrice::where(['customer_id' => $d->invoice->customer_id , 'product_id' => $d->product_id])->first(); ?>
          @if(empty($is_c_price))
          {{ $d->product->price }}
          @else
          {{ $is_c_price->price }}
          @endif
        </td>
        <td>{{ $d->unit }}</td>
        <td>{{ $d->amount }}</td>
      </tr>
      @endforeach
    
  </table>
</div>

	<!--
      <tr>
        <th>Product</th>
        <th>Price</th>
        <th>Units</th>
        <th>Amount</th>
      </tr>
    
      @foreach($idetails as $d)
      <tr>
        <td><?php echo $d->product->name;?></td>
        <td>
          <?php $is_c_price = App\CustomPrice::where(['customer_id' => $d->invoice->customer_id , 'product_id' => $d->product_id])->first(); ?>
          @if(empty($is_c_price))
          <?php echo $d->product->price; ?>
          @else
          <?php echo $is_c_price->price; ?>
          @endif
        </td>
        <td><?php echo $d->unit; ?></td>
        <td>{{ $d->amount }}</td>
      </tr>
      @endforeach
     
          -->


<!DOCTYPE html>
<html>
<head>
	<title>Print Invoice</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>.
	<style type="text/css">
	.total{
			border: 1px solid gray;
			padding: 1px;
			font-weight: 600;
		}
	
		</style>
</head>
<body>
	<p class="text-center" align="1">{{ date('d/m/Y h:i:sa') }}</p>
	<div id="p">
		<img class="img-fluid" src="{{ asset('images/PRINTER DESIGN.jpg') }}" alt="Invoice Header Image" width="470px" height="200px"/>
        
	    
	    
  <table class="table table-bordered table-custom-th" width="100%" cellspacing="0">
    
	                <p>Customer Name: {{ strtoupper($invoice->customer->user->name) }}</p>
	                <p>Address: {{ $invoice->customer->address }}</p>
	                <p>Phone   : {{ $invoice->customer->phone }}</p>
	                <p>Date: {{ date('d/m/Y h:i:sa' , strtotime($invoice->created_at)) }} / Bill No: {{ $bill_no }}</p>
	                <p class="text-center">*******************************</p>
	                
            @foreach($idetails as $d)
			    
			    <tr><td><p class="text-center" align="1">         --------------</p></td></tr>
			    <tr><td class="text-center" align="1"><?php echo $d->product->name;?></td></tr>
                <tr><td><p class="text-center" align="1">         --------------</p></td></tr>
				
				
		<!--<?php $is_c_price = App\CustomPrice::where(['customer_id' => $d->invoice->customer_id , 'product_id' => $d->product_id])->first(); ?>-->
  <!--        @if(empty($is_c_price))-->
          
  <!--        <tr>-->
  <!--        <td>Price :{{ $d->product->price }}</td>-->
  <!--        @else-->
  <!--        <td>Price :{{ $is_c_price->price }}</td>-->
  <!--        @endif-->
  <!--        </tr>-->
  
  
  
          <tr>
          <td>Price :{{ $d->amount/$d->unit }}</td>
          </tr>
          <!--@if(Auth::user()->role < 3) -->
          <!--<tr class="statuscheck">-->
          <!--<td id = "edit">P.Price :{{ $d->product->p_price }}</td>-->
          <!--</tr>-->
          <!--@endif-->
        <tr>
        <td>Unit  :{{ $d->unit }}</td>
        </tr>
      		
				<tr>
				    <td>Amount:{{ $d->amount }} </td>
				</tr>
				
		<?php $is_c_benefit = App\CustomPrice::where(['customer_id' => $d->invoice->customer_id , 'product_id' => $d->product_id])->first(); ?>
          @if(empty($is_c_benefit))
      	  <tr>
          <td>Bit :{{ $d->product->c_benefit*$d->unit }}</td>
          @else
          <td>Bit :{{ $is_c_benefit->c_benefit*$d->unit }}</td>
          @endif
          </tr>
				
				
				
			
				@endforeach
			
	
  </table>
            <p class="text-center">*******************************</p>
		<table>
	        <tbody>
	            <tr>
	                <td><p>Sub Total: {{$invoice->subtotal }}</p></td>
	            </tr>
	            @if($prev_invoice->amount_left>0)
	            <tr>
	                <td><p>Balance  : {{ $prev_invoice->amount_left }}</p></td>
	            </tr>
	            @elseif($prev_invoice->amount_left<0)
	            
	            <tr>
	                <td><p>Prev Advance : {{ $prev_invoice->amount_left*-1 }}</p></td>
	            </tr>
	            @endif
	            @if($invoice->amount>0)
	            <tr>
	                <td><p>Total    : <span class="total">{{ $invoice->amount }}</span></p></td>
	                
	            </tr>
	            @if($invoice->received_amount>0 && $invoice->amount_left>=0)
	            <tr>
	                <td><p>Received: {{ $invoice->received_amount }}</p></td>
	            </tr>
	            <tr>
	                <td><p>Remaining Balance: {{ $invoice->amount_left }}</p></td>
	            </tr>
	            @endif 
	            @if($invoice->received_amount>0 && $invoice->amount_left<0)
	            <tr>
	                <td><p>Received: {{ $invoice->received_amount }}</p></td>
	            </tr>
	            <tr>
	                <td><p>Advance: {{ $invoice->amount_left*-1 }}</p></td>
	            </tr>
	            @endif 
	            @else($invoice->amount<0)
	            <tr>
	                <td><p>Crrent Advance    : <span class="total">{{ $invoice->amount*-1 }}</span></p></td>
	            </tr>
	            @endif
	            <tr>
	                <td><p>Bit  : {{ $invoice->c_benefit }}</p></td>
	            </tr>
	            
	            <tr>
	                <td><p>Egs     : {{ $invoice->customer->invoices->sum('c_benefit')}}</p></td>
	            </tr>
	            
	            <tr>
	                <td><p class="text-center"><b>Previous Bill History {{date('d/m/Y h:i:sa',strtotime( $prev_invoice->created_at))}}</b></p></td>
	            </tr>
	            <tr>
	                <td><p class="text-center">Subtotal: {{ $prev_invoice->subtotal}}</p></td>
	            </tr>
	            <tr>
	                <td><p class="text-center">Total   : {{ $prev_invoice->amount }}</p></td>
	            </tr>
	            <tr>
	                <td><p class="text-center">Received: {{ $prev_invoice->received_amount }}</p></td>
	            </tr>
                @if($prev_invoice->amount_left>0)
                <tr>
	                <td><p class="text-center">Remaining Balance: {{ $prev_invoice->amount_left }}</p></td>
	            </tr>
	            @elseif($prev_invoice->amount_left <=-1&& $prev_invoice->amount_left != 0)
	            <tr>
	                <td><p class="text-center">Advance : {{ $prev_invoice->amount_left*-1 }}</p></td>
	            </tr>
	            
	           @endif
	        </tbody>
	    </table>
		<center><img class="img-fluid"  align="1" src="{{asset('images/qr.bmp') }}" alt="Invoice Header Image" width="auto" height="auto"/></center>
           
	<!--<p style="text-align:center;"><img class="aligncenter" src="{{ asset('images/qr.bmp') }}" align="1" alt="Invoice Header Image" width="auto" height="auto" align="middle"></p>-->
	    <p class="text-center" align="1">--------Thank You--------</p>
		
</div>
@section('scripts')
<script type="text/javascript">
$("tr.statuscheck input, tr.statuscheck select, tr.statuscheck textarea").prop('disabled', true);
</script>
	  
</body>
</html>