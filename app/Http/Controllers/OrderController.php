<?php

namespace App\Http\Controllers;
use App\Repositories\Common;
use Illuminate\Http\Request;
use App\OtCustomer;
use App\User;
use App\Product;
use App\CustomOTBenefit;
use App\CustomPrice;
use App\Customer;
use App\Order;
use App\OrderDetail;
use App\Ordertaker;
use App\Invoice;
use App\InvoiceDetail;
use App\Area;

use Auth;

use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Null_;
class OrderController extends Controller{

    public function index($orders,$type){
     
        $products = Product::get();
        $product_report = [];
        $counter = 0;
        
        if($orders!=null){
        foreach($products as $p){
            $ppunit = 0;$ppamount = 0;
            $product_report[$counter]['id'] = $p->id;
            $product_report[$counter]['name'] = $p->name;
            foreach($orders as $in){
                $idet =  $in->orderdetail->where('product_id' , $p->id);
                $ppunit += $idet->sum('unit');
                $ppamount += $idet->sum('p_amount');
            }
            $product_report[$counter]['unit'] = $ppunit;
            $product_report[$counter]['amount'] = $ppamount;
            $counter++;
        }
         $condition=DB::select('SELECT max(id) as id,customer_id 
            FROM orders 
            where is_confirmed_seller is NULL and  is_confirmed_admin  is NULL
            GROUP BY customer_id order by id asc 
            ');
    }
        $ordertakers = User::where('role',5)->whereIn('id', $orders->pluck('ot_id')->toArray())->get();
        $areas = Area::whereIn('id', Customer::whereIn('id', $orders->pluck('customer_id'))->pluck('area_id')->toArray())->get();
        $sellers = User::where('role',3)->get();
        if($type==1){
            return view('orders.all' , compact('orders' , 'product_report','ordertakers', 'areas'));
        }
        else if($type==2){
            return view('orders.unconfirmed' , compact('orders' , 'product_report','ordertakers', 'areas' , 'condition'));
        }
        else if($type==3){
            return view('orders.seller_confirmed' , compact('orders' , 'product_report','ordertakers', 'areas'));
        }
        else if($type==4){
            return view('orders.admin_confirmed' , compact('orders' , 'product_report','ordertakers', 'areas'));
        }
        else if($type==5){
            return view('orders.important' , compact('orders' , 'product_report','ordertakers', 'areas'));
        }
        else{}
    }

    public function getImportantOrders(Request $request){

        if($request->from){
            $dates=$this->dateFilter($request->from,$request->to);
            $from=$dates[0];
            $to=$dates[1];
        }

        $id=Auth::id();

        if(Auth::user()->role == 1 ){

            if($from == ""){
                $orders = Order::has('get_ot_creater')
                            ->where('is_important',1)
                            ->where('is_confirmed_seller',NULL)
                            ->where('is_confirmed_admin',NULL)
                            ->get();
            }
            else{
                $orders = Order::has('get_ot_creater')
                            ->whereBetween('created_at', array($from, $to))
                            ->where('is_important',1)
                            ->where('is_confirmed_seller',NULL)
                            ->where('is_confirmed_admin',NULL)
                            ->get();
            }
        }

        else if(Auth::user()->role == 2){

            if($from == ""){
                $orders = Order::has('is_subadmin_customer')
                            ->has('get_ot_creater')
                            ->with('customers')
                            ->where('is_confirmed_seller',NULL)
                            ->where('is_confirmed_admin',NULL)
                            ->where('is_important',1)
                            ->get();
            }
            else{
                $orders = Order::has('is_subadmin_customer')
                        ->has('get_ot_creater')
                        ->with('customers')
                        ->whereBetween('created_at', array($from, $to))
                        ->where('is_confirmed_seller',NULL)
                        ->where('is_confirmed_admin',NULL)
                        ->where('is_important',1)
                        ->get();
            }
        }
        else if(Auth::user()->role == 3){

            $common_ot = User::where('ot_of', Auth::user()->seller_of)->pluck('id');
        //    dd($common_ot);
            if($from == ""){

                $orders = Order::whereIn('ot_id', $common_ot)
                            ->where('is_important',1)
                            ->where('is_confirmed_seller',NULL)
                            ->where('is_confirmed_admin',NULL)
                            ->get();
            }
            else{
                $orders = Order::whereBetween('created_at', array($from, $to))
                            ->where('is_important',1)
                            ->whereIn('ot_id', $common_ot)
                            ->where('is_confirmed_seller',NULL)
                            ->where('is_confirmed_admin',NULL)
                            ->get();
            }
        }

        else if(Auth::user()->role == 5){
            if($from == "" ){
                $orders = Order::where('is_important',1)
                            ->where('is_confirmed_seller',NULL)
                            ->where('is_confirmed_admin',NULL)
                            ->where('ot_id',$id)
                            ->get();
                }
            else{

                $orders = Order::where('is_important',1)
                            ->where('is_confirmed_seller',NULL)
                            ->where('is_confirmed_admin',NULL)
                            ->whereBetween('created_at', array($from, $to))
                            ->where('ot_id',$id)
                            ->get();
                }
        }
        else{}

        return $this->index($orders,5);

    }

    public function getUnconfirmedOrders(Request $request){
        if($request->from){
            $dates=$this->dateFilter($request->from,$request->to);
            $from=$dates[0];
            $to=$dates[1];
        }

        $id=Auth::id();

        if(Auth::user()->role == 1){

            if($from == ""){
                $orders = Order::has('get_ot_creater')->where('is_confirmed_seller',NULL)
                        ->where('is_confirmed_admin',NULL)
                        ->get();
            }
            else {
                $orders = Order::has('get_ot_creater')
                            ->where('is_confirmed_seller',NULL)
                            ->where('is_confirmed_admin',NULL)
                            ->whereBetween('created_at', array($from, $to))
                            ->get();
            } 
        }
        else if(Auth::user()->role == 2){
            
            if($from == ""){
                $orders = Order::has('get_ot_creater')
                            ->has('is_subadmin_customer')
                            ->with('customers')
                            ->where('is_confirmed_seller',NULL)
                            ->where('is_confirmed_admin',NULL)
                            ->get();
            }
            else {
                $orders = Order::has('is_subadmin_customer')
                        ->has('get_ot_creater')
                        ->with('customers')
                        ->where('is_confirmed_seller',NULL)
                        ->where('is_confirmed_admin',NULL)
                        ->whereBetween('created_at', array($from, $to))
                        ->get();
            }
        }

        if(Auth::user()->role == 3){

            $common_ot = User::where('ot_of', Auth::user()->seller_of)->pluck('id');

            if($from == ""){
                $orders = Order::where('is_confirmed_seller',NULL)
                            ->whereIn('ot_id', $common_ot)
                            ->where('is_confirmed_admin',NULL)
                            ->get();
            }
            else {
                $orders = Order::where('is_confirmed_seller',NULL)
                            ->where('is_confirmed_admin',NULL)
                            ->whereIn('ot_id', $common_ot)
                            ->whereBetween('created_at', array($from, $to))
                            ->get();
            } 
        }

        else if(Auth::user()->role == 5){
            if($from == ""){

                $orders = Order::where('is_confirmed_seller',NULL)
                            ->where('is_confirmed_admin',NULL)
                            ->where('ot_id',$id)
                            ->get();
            }  
            else {

                $orders = Order::where('is_confirmed_seller',NULL)
                            ->where('is_confirmed_admin',NULL)
                            ->whereBetween('created_at', array($from, $to))
                            ->where('ot_id',$id)
                            ->get();
                }
        }
        else{}

        return $this->index($orders,2);
    }
    public function getSellerConfirmedOrders(Request $request){

        if($request->from){
            $dates=$this->dateFilter($request->from,$request->to);
            $from=$dates[0];
            $to=$dates[1];
        } 

        $id=Auth::id();
        if(Auth::user()->role == 1){

            if($from == ""){
                $orders = Order::has('get_ot_creater')
                            ->where('is_confirmed_seller',"!=",NULL)
                            ->where('is_confirmed_admin',NULL)
                            ->get();
            }
            else {
                $orders = Order::has('get_ot_creater')
                            ->where('is_confirmed_seller',"!=", NULL)
                            ->where('is_confirmed_admin', NULL)
                            ->whereBetween('created_at', array($from, $to))
                            ->get();
                }
        }
        else if(Auth::user()->role == 2){
            
            if($from == ""){

                $orders = Order::has('is_subadmin_customer')
                        ->has('get_ot_creater')
                        ->with('customers')
                        ->where('is_confirmed_seller',"!=", NULL)
                        ->where('is_confirmed_admin', NULL)
                        ->get();
            }     
            else{

                $orders = Order::has('is_subadmin_customer')
                            ->has('get_ot_creater')
                            ->with('customers')
                            ->where('is_confirmed_seller',"!=", NULL)
                            ->where('is_confirmed_admin', NULL)
                            ->whereBetween('created_at', array($from, $to))
                            ->get();
            }
        }
        if(Auth::user()->role == 3){

            $common_ot = User::where('ot_of', Auth::user()->seller_of)->pluck('id');

            if($from == ""){
                $orders = Order::where('is_confirmed_seller',"!=",NULL)
                            ->where('is_confirmed_admin', NULL)
                            ->whereIn('ot_id', $common_ot)
                            ->get();
            }
            else {
                $orders = Order::where('is_confirmed_seller',"!=",NULL)
                            ->where('is_confirmed_admin', NULL)
                            ->whereIn('ot_id', $common_ot)
                            ->whereBetween('created_at', array($from, $to))
                            ->get();
            }
        }
       
        else if(Auth::user()->role == 5){
            if($from == ""){
                $orders = Order::where('is_confirmed_seller',"!=",NULL)
                        ->where('is_confirmed_admin', NULL)
                        ->where('ot_id',$id)->get();
            }
               
            else {
                $orders = Order::where('is_confirmed_seller',"!=",NULL)
                        ->whereBetween('created_at', array($from, $to))
                        ->where('is_confirmed_admin', NULL)
                        ->where('ot_id',$id)
                        ->get();
            }
        }  
        else{}

        return $this->index($orders,3);
    }

    public function getAdminConfirmedOrders(Request $request){

        if($request->from){
            $dates=$this->dateFilter($request->from,$request->to);
            $from=$dates[0];
            $to=$dates[1];
        } 

        $id=Auth::id();
        if(Auth::user()->role == 1){

            if($from == ""){
                $orders = Order::has('get_ot_creater')
                            ->where('is_confirmed_admin',"!=",NULL)
                            ->get();
            }
            else {
                $orders = Order::has('get_ot_creater')
                            ->where('is_confirmed_admin',"!=",NULL)
                            ->whereBetween('created_at', array($from, $to))
                            ->get();
                }
        }
        else if(Auth::user()->role == 2){
            
            if($from == ""){

                $orders = Order::has('is_subadmin_customer')
                        ->has('get_ot_creater')
                        ->with('customers')
                        ->where('is_confirmed_admin',"!=",NULL)
                        ->get();
            }     
            else{

                $orders = Order::has('is_subadmin_customer')
                            ->has('get_ot_creater')
                            ->with('customers')
                            ->where('is_confirmed_admin',"!=",NULL)
                            ->whereBetween('created_at', array($from, $to))
                            ->get();
            }
        }
        if(Auth::user()->role == 3){

            $common_ot = User::where('ot_of', Auth::user()->seller_of)->pluck('id');

            if($from == ""){
                $orders = Order::where('is_confirmed_admin',"!=",NULL)
                            ->whereIn('ot_id', $common_ot)
                            ->get();
            }
            else {
                $orders = Order::where('is_confirmed_admin',"!=",NULL)
                            ->whereIn('ot_id', $common_ot)
                            ->whereBetween('created_at', array($from, $to))
                            ->get();
            }
        }
       
        else if(Auth::user()->role == 5){
            if($from == ""){
                $orders = Order::where('is_confirmed_admin',"!=",NULL)
                        ->where('ot_id',$id)->get();
            }
               
            else {
                $orders = Order::where('is_confirmed_admin',"!=",NULL)
                        ->where('is_confirmed_admin', NULL)
                        ->where('ot_id',$id)
                        ->get();
            }
        }  
        else{}

        return $this->index($orders,4);
    }

    public function getAllOrders(Request $request){

        if($request->from){
            $dates=$this->dateFilter($request->from,$request->to);
            $from=$dates[0];
            $to=$dates[1];
        }

        $id=Auth::id();

        if(Auth::user()->role == 1){

            if($from == ""){
                $orders = Order::has('get_ot_creater')->get();
            }
            else {
                $orders = Order::has('get_ot_creater')
                            ->whereBetween('created_at', array($from, $to))
                            ->get();
            }
        }
        else if(Auth::user()->role == 2){
            
            if($from == ""){
                    
                $orders = Order::has('is_subadmin_customer')
                            ->has('get_ot_creater')
                            ->with('customers')
                            ->get();
            }     
            else{
                $orders = Order::has('is_subadmin_customer')
                            ->has('get_ot_creater')
                            ->with('customers')
                            ->whereBetween('created_at', array($from, $to))
                            ->get();
            }
        }
        else if( Auth::user()->role == 3){

            $common_ot = User::where('ot_of', Auth::user()->seller_of)->pluck('id');

            if($from == ""){
                $orders = Order::whereIn('ot_id', $common_ot)->get();
            }
            else {
                $orders = Order::whereIn('ot_id', $common_ot)->whereBetween('created_at', array($from, $to))->get();
            }
        }

        else if(Auth::user()->role == 5){
            if($from == ""){
                $orders = Order::where('ot_id',$id)->get();
            }
            else {
                $orders = Order::whereBetween('created_at', array($from, $to))
                            ->where('ot_id',$id)
                            ->get();
            }
        }   

        else{}
        return $this->index($orders,1);
    }

    public function createOrder(){

        $ids = [Auth::id()];
        
        if(Auth::user()->role == 3){
            $ids[] = User::find(Auth::user()->seller_of)->id;
        }

        $ot_customers = OtCustomer::where('ot_id', Auth::id())->with('customer')->get();
        
        $customers = Customer::whereIn('created_by', [Auth::user()->ot_of, Auth::id()])->get();
        
        return view('orders.create' , compact('customers', 'ot_customers'));
    }
    
    //AJAX function for order details
    public function getorderDetail($id){
        $order = Order::find($id);
        $idetails = Order::find($id)->orderdetail;
        $prev_orders = Order::where('id' , '<' , $id)->where('customer_id' , $order->customer_id)->orderBy('id' , 'desc')->get();
        $prev_order = $prev_orders[0];
        $bill_no = count($prev_orders);
        return view('ajax.order_detail' , compact('idetails' , 'order' , 'prev_order' , 'bill_no'));
    }
    

    public function dateFilter( $from,$to ){
        $from = str_replace("/" , "-" , $from);
        $to = str_replace("/" , "-" , $to);

        if($to == null){
            $to = date('Y-m-d');
        }
        if($from == null){
            $from = date('Y-m-d');
        }
        return array($from , $to);
    }

    public function checkMinBalance($cid , $balance){
        $customer = Customer::find($cid);
        $old_customer_balance = Order::where(['customer_id' => $cid])->orderBy('id' , 'desc')->first()->amount_left;
        if(($old_customer_balance + $balance) > $customer->balance_limit){
            return $customer->balance_limit;
        }
        return false;
    }

    public function storeOrder(Request $request , $update = null){
        $order = !is_null($update) ? Order::find($update) : new Order();
        
        $cus_det = explode("-", $request->customer_id);

        $tt_amount = array_sum($request->amount) + $request->old_balance;
        if(is_null ($update))
        $balance = $tt_amount + $request->old_balance;
        
        $order->customer_id = $cus_det[0];
        $order->ot_id = !is_null($update) ? $order->ot_id : Auth::id();
        $order->unit =array_sum($request->unit);
        $order->amount = $tt_amount;
        $order->subtotal = array_sum($request->amount);
        $order->received_amount = $request->received_amount;
        $order->order_date=$request->order_date;
        if($tt_amount >= $request->received_amount){
            $order->amount_left = ($tt_amount -  $request->received_amount);
        }
        else{
            $order->advance = $request->received_amount - $tt_amount;
            $order->amount_left = $old_balance -  $request->received_amount;
        }
        
        $checkB = $this->checkMinBalance($cus_det[0] , $order->amount_left);
        if($checkB){
            return redirect()->back()->with('error' , 'Customer Balance Limit Exceeded ( Limit is '.$checkB.' )');
        }
        if($request->has('important')){
            $order->is_important=1;
        }

        $order->save();
        
        $order_id = $order->id;
        
        if(!is_null($update))
            $order->orderdetail()->delete();
        
        $orderData = $request->all();
        $total_ot_benefit = 0; $total_customer_benefit = 0;$p_amount = 0;
        for($counter = 0;$counter < sizeof($request->amount);$counter++){
            if(!empty($orderData['amount'][$counter]))
            {
                $orderDetails = new OrderDetail() ;
                $orderDetails->order_id = $order_id;
                $is_custom_price = CustomPrice::where(['customer_id' => $request->customer_id , 'product_id' => $orderData['product_id'][$counter]])->first();
                $is_default_price = Product::find($orderData['product_id'][$counter]);
                if(!empty($is_custom_price)){
                    $orderDetails->ot_benefit = $is_custom_price->ot_benefit * $orderData['unit'][$counter];
                    $orderDetails->c_benefit = $is_custom_price->c_benefit * $orderData['unit'][$counter];
                    $orderDetails->p_amount = $is_custom_price->product->p_price * $orderData['unit'][$counter];
                    // $total_admin_benefit += $is_custom_price->a_benefit * $invoiceData['unit'][$counter];
                    // $total_customer_benefit += $is_custom_price->c_benefit * $invoiceData['unit'][$counter];
                    // $p_amount += $is_custom_price->product->p_price * $invoiceData['unit'][$counter];
                }
                else{
                    $orderDetails->ot_benefit = $is_default_price->ot_benefit * $orderData['unit'][$counter];
                    $orderDetails->c_benefit = $is_default_price->c_benefit * $orderData['unit'][$counter];
                    $orderDetails->p_amount = $is_default_price->p_price * $orderData['unit'][$counter];
                    
                    // $total_admin_benefit += $is_default_price->a_benefit * $invoiceData['unit'][$counter];
                    // $total_customer_benefit += $is_default_price->c_benefit * $invoiceData['unit'][$counter];
                    // $p_amount += $is_default_price->p_price * $invoiceData['unit'][$counter];
                }
                $orderDetails->product_id = $orderData['product_id'][$counter];
                $orderDetails->unit = $orderData['unit'][$counter];
                $orderDetails->amount = $orderData['amount'][$counter];
                $orderDetails->save();
            }
        }
      
        Order::where('id' , $order_id)->update(['ot_benefit' => $order->orderdetail->sum('ot_benefit') , 'c_benefit' => $order->orderdetail->sum('c_benefit') , 'p_amount' => $order->orderdetail->sum('p_amount')]);
        return redirect()->route('unconfirmed.orders')->with('success' , 'Order Created');
    }

    public function getOrder($id){
        $added = [];
        $order = Order::find($id);
        $customPrices = CustomPrice::where('customer_id' , $order->customer_id)->get();
        
        if(sizeof($customPrices)){
            foreach($order->orderdetail as $d){
                foreach($customPrices as $custom){
                    if($custom->product_id == $d->product_id){
                        $d->product->price = $custom->price;
                        $d->product->c_benefit = $custom->c_benefit;
                    }
                }
                $added[] = $d->product_id;
            }
        }
        if(Auth::user()->role == 5)
            $user_id = Auth::user()->ot_of;
        else if(Auth::user()->role == 3)
            $user_id = Auth::user()->seller_of;
        else
            $user_id = Auth::user()->id;
        $products = Product::whereNotIn('id' , $added)->where('user_id' , $user_id)->get();
        foreach($products as $product){
            foreach($customPrices as $custom){
                if($custom->product_id == $product->id){
                    $product->price = $custom->price;
                    $product->c_benefit = $custom->c_benefit;
                }
            }
        }
          $old_order = Order::where('customer_id' , $order->customer_id)->orderBy('id' , 'desc')->get();
          $amount_left=$old_order[0]->amount_left - $old_order[0]->subtotal + $old_order[0]->received_amount; 
         
        $old_balance = 0;
        if(sizeof($old_order) > 1){
            $old_balance = $old_invoices[1]->amount_left;
        }
        return view('orders.edit' , compact('order' , 'products' , 'old_balance','amount_left'));
    }


    public function updateOrder(Request $request , $id){
         $request->hid;
        $conditions = ['id' => $id];
        $order = Order::where($conditions)->get();
         $request->amount;
        if(sizeof($order)){
            if($request->amount_left_input){
                $amount_left = $order[0]->amount_left + $order[0]->received_amount;
                
                $order = Order::where($conditions)
                ->update(['amount_left' => $amount_left - $request->amount_left_input + $order[0]->received_amount,
                 'received_amount' => $order[0]->received_amount + $request->amount_left_input,
                 // 'amount' => $request->hid ,
                ]
                );
                return Common::Message("Order" , 2);
        }
        
        else{
            // $this->deleteOrder($id);
             
            Order::where('id' , $id)->update(['amount_left' => $order[0]->amount_left + $order[0]->received_amount , 'received_amount' => $order[0]->received_amount + $request->amount_left_input]);
            $this->storeOrder($request , $order[0]->id);

            return redirect()->route('unconfirmed.orders')->with('success' , 'Order Updated');
            }
        }
        else{
            return Common::Message("Order");
        }
    }


    public function deleteOrder($id){
        
        $order = Order::find($id);
        if(!empty($order)){
            Order::where('id' , $order->id)->delete();
            OrderDetail::where('order_id' , $id)->delete();
            return Common::Message("Order" , 3);
        }else{
            return Common::Message("Order");
        }   
    }

    public function confirmOrder($id, $sendToUnapprove = true){

        if(Auth::user()->role==3){
            Order::where('id' , $id)->update(['is_confirmed_seller' => Auth::id()]);

        }
        else if (Auth::user()->role < 3){

            Order::where('id' , $id)->update(['is_confirmed_admin' => Auth::id()]);
            
            if($sendToUnapprove)
            {
                $order = Order::where('id' , $id)->first();
                $invoice = new Invoice();
                $invoice->user_id = Auth::id();
                $invoice->customer_id = $order->customer_id;
                $invoice->unit = $order->unit;
                $invoice->amount = $order->amount;
                $invoice->subtotal = $order->subtotal;
                $invoice->p_amount = $order->p_amount;
                $invoice->received_amount = $order->received_amount;
                $invoice->p_amount = $order->p_amount;
                $invoice->amount_left = $order->amount_left;
                $invoice->advance = $order->advance;
                $invoice->a_benefit = $order->ot_benefit;
                $invoice->c_benefit = $order->c_benefit;
        
                $invoice->save();
    
                $order_details = OrderDetail::where('order_id' , $id)->get();
               
                $invoice_id = $invoice->id;
    
                foreach ($order_details as $od) {
                    $invoice_detail = new InvoiceDetail();
                    $invoice_detail->invoice_id = $invoice_id;
                    $invoice_detail->product_id = $od->product_id;
                    $invoice_detail->unit = $od->unit;
                    $invoice_detail->amount = $od->amount;
                    $invoice_detail->p_amount = $od->p_amount;
                    $invoice_detail->a_benefit = $od->ot_benefit;
                    $invoice_detail->c_benefit = $od->c_benefit;
                    
                    $invoice_detail->save();
                }
                
                $benefit=Order::where('ot_id', $order->ot_id)->sum('ot_benefit');
                $updateData = ['ben_earned' => $benefit];
                Ordertaker::where('user_id', $order->ot_id)->update($updateData);
            }
        }
     
        else{}
    }

    public function confirmOrderMult(Request $request){
        
        $productData = $request->all();
        if($productData['confirm-to']!=null){
            $confirmed_orders=$productData['confirm-to'];
            foreach($confirmed_orders as $ap){
                $sendToUnapprove = $request->send_to_unapprove ? true : false;
                $this->confirmOrder($ap, $sendToUnapprove);
            }
            return Common::Message("Order" , 4);
        }
        else{
            $approved_orders=$productData['approve-to'];
            foreach($approved_orders as $ap){
                $this->approveOrder($ap);
            }
            return Common::Message("Order" , 4);
        }
    }
}
