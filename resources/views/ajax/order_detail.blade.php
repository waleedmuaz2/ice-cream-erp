
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
          <?php $is_c_price = App\CustomPrice::where(['customer_id' => $d->order->customer_id , 'product_id' => $d->product_id])->first(); ?>
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
          <?php $is_c_price = App\CustomPrice::where(['customer_id' => $d->order->customer_id , 'product_id' => $d->product_id])->first(); ?>
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
	<title>Print Order</title>
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
	<div id="p">
	    <img class="img-fluid" src="{{ asset('images/PRINTER DESIGN.jpg') }}" alt="Order Header Image" width="470px" height="200px"/>
        
	    
	    
  <table class="table table-bordered table-custom-th" width="100%" cellspacing="0">
    
	                <p>Customer Name: {{ strtoupper($order->customers->user->name) }}</p>
	                <p>Address: {{ $order->customers->address }}</p>
	                <p>Date: {{ date('d/m/Y' , strtotime($order->created_at)) }} / Bill No: {{ $bill_no }}</p>
	                <p class="text-center">*******************************</p>
	                
            @foreach($idetails as $d)
			    
			    <tr><td><p class="text-center" align="1">         --------------</p></td></tr>
			    <tr><td class="text-center" align="1"><?php echo $d->product->name;?></td></tr>
                <tr><td><p class="text-center" align="1">         --------------</p></td></tr>
				
				
		<?php $is_c_price = App\CustomPrice::where(['customer_id' => $d->invoice->customer_id , 'product_id' => $d->product_id])->first(); ?>
          @if(empty($is_c_price))
          
          <tr>
          <td>Price :{{ $d->product->price }}</td>
          @else
          <td>Price :{{ $is_c_price->price }}</td>
          @endif
          </tr>
        
        <tr>
        <td>Unit  :{{ $d->unit }}</td>
        </tr>
      		
				<tr>
				    <td>Amount:{{ $d->amount }} </td>
				</tr>
				
		<?php $is_c_benefit = App\CustomPrice::where(['customer_id' => $d->invoice->customer_id , 'product_id' => $d->product_id])->first(); ?>
          @if(empty($is_c_benefit))
      	  <tr>
          <td>Benefit :{{ $d->product->c_benefit*$d->unit }}</td>
          @else
          <td>Benefit :{{ $is_c_benefit->c_benefit*$d->unit }}</td>
          @endif
          </tr>
				
				
				
			
				@endforeach
			
	
  </table>
            <p class="text-center">*******************************</p>
		<table>
	        <tbody>
	            <tr>
	                <td><p>Sub Total: {{$order->subtotal }}</p></td>
	            </tr>
	            @if($prev_order->amount_left>0)
	            <tr>
	                <td><p>Balance  : {{ $prev_order->amount_left }}</p></td>
	            </tr>
	            @elseif($prev_order->amount_left<0)
	            
	            <tr>
	                <td><p>Prev Advance : {{ $prev_order->amount_left*-1 }}</p></td>
	            </tr>
	            @endif
	            @if($order->amount>0)
	            <tr>
	                <td><p>Total    : <span class="total">{{ $order->amount }}</span></p></td>
	                
	            </tr>
	            @endif
	             
	            @if($order->received_amount>0 && $order->amount_left>=0)
	            <tr>
	                <td><p>Received: {{ $order->received_amount }}</p></td>
	            </tr>
	            <tr>
	                <td><p>Remaining Balance: {{ $order->amount_left }}</p></td>
	            </tr>
	            @endif
	           
	            @if($order->received_amount>0 && $order->amount_left<0)
	            <tr>
	                <td><p>Received: {{ $order->received_amount }}</p></td>
	            </tr>
	            <tr>
	                <td><p>Advance: {{ $order->amount_left*-1 }}</p></td>
	            </tr>
	           @endif
	            @if($order->amount<0)
	            <tr>
	                <td><p>Crrent Advance    : <span class="total">{{ $order->amount*-1 }}</span></p></td>
	            </tr>
	            @endif
	             <tr>
	                <td><p>Benefit  : {{ $order->c_benefit }}</p></td>
	            </tr>
	            <!--error-->
	            
	            
	            <tr>
	                <td><p class="text-center"><b>Previous Bill History {{date('d.m.Y',strtotime( $prev_order->created_at))}}</b></p></td>
	            </tr>
	            </tbody>
	    </table>
	    
	<p style="text-align:center;"><img class="aligncenter" src="{{ asset('images/qr.bmp') }}" align="1" alt="Order Header Image" width="auto" height="auto" align="middle"></p>
	    <p class="text-center" align="1">--------Thank You--------</p>
		
</div>
	  
</body>
</html>