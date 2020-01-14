<?php

namespace App\Http\Controllers;
use App\Repositories\Common;

use Illuminate\Http\Request;

use resources\views\layouts\app;
use App\User;
use App\Product;
use App\Customer;
use App\Invoice;
use App\InvoiceDetail;
use App\Notification;
use App\AdminSellRecord;
use App\AdminSellTotal;
use App\CustomPrice;

use Auth;
use PDF;

class InvoiceController extends Controller{
    public function index($get_unapproved = "" , $from = "" , $to = ""){
        
        $cond = $get_unapproved == "" ? ['is_approved'  => 1] : ['is_approved'  => null];
        if(Auth::user()->role < 3){
            $ids = [Auth::id()];
            foreach (Auth::user()->mysellers as $value) {
                $ids[] = $value->id;
            }
            $invoices = Invoice::whereIn('user_id' , $ids)->where($cond)->get();
            if($from != ""){
                $invoices = Invoice::whereIn('user_id' , $ids)->where($cond)->whereBetween('created_at', array($from, $to))->get();    
            }
        }
        else if(Auth::user()->role == 4){
            $cond['customer_id'] = Auth::user()->customer_id;
            $invoices = Invoice::where($cond)->get();
            if($from != ""){
                $invoices = Invoice::where($cond)->whereBetween('created_at', array($from, $to))->get();    
            }
        }
        else{
            $cond['user_id'] = Auth::id();
            $invoices = Invoice::where($cond)->get();
            if($from != ""){
                $invoices = Invoice::where($cond)->whereBetween('created_at', array($from, $to))->get();     
            }
        }
        $products = Product::where('user_id' , Auth::id())->get();
        $product_report = [];
        $counter = 0;
        foreach($products as $p){
            $ppunit = 0;$ppamount = 0;
            $product_report[$counter]['id'] = $p->id;
            $product_report[$counter]['name'] = $p->name;
            foreach($invoices as $in){
                $idet =  $in->invoicedetail->where('product_id' , $p->id);
                $ppunit += $idet->sum('unit');
                $ppamount += $idet->sum('p_amount');
            }
            $product_report[$counter]['unit'] = $ppunit;
            $product_report[$counter]['amount'] = ($p->p_price * $ppunit);
            $product_report[$counter]['checks'] = $ppamount;
            $product_report[$counter]['checkunit'] = $invoices->sum('unit');
            
            $counter++;
        }
        if($get_unapproved == ""){
            return view('approved_invoices' , compact('invoices' , 'product_report'));
        }
        else
        {
        return view('pending_invoices' , compact('invoices' , 'product_report'));
        }
    }

    public function indexAll($admin_id){
        $ids = [];
        $sub_admin = User::find($admin_id);
        foreach ($sub_admin->mysellers as $value) {
            $ids[] = $value->id;
        }
        $invoices = Invoice::whereIn('user_id' , $ids)->get();
        return view('subadmin_invoices' , compact('invoices' , 'sub_admin'));
    }
    
    public function newInvoice(){
        // if(Auth::user()->role == 1){
        //     $customers = Customer::all();
        // }
        // else{
        $ids = [Auth::id()];
        if(Auth::user()->role == 3){
            $ids[] = User::find(Auth::user()->seller_of)->id;
        }
        $customers = Customer::whereIn('created_by' , $ids)->with('user')->get()->sortBy('user.name');//}
        $products = Product::where('user_id' , Auth::id())->get();
        return view('add_invoice' , compact('customers' , 'products' , 'invoice_no'));
    }
    
    public function storeInvoice(Request $request , $update = false){
        $invoice = new Invoice();
        
        $cus_det = explode("-", $request->customer_id);
        
        $tt_amount = array_sum($request->amount) + $request->old_balance;
        
        $invoice->customer_id = $cus_det[0];
        $invoice->user_id = Auth::id();
        $invoice->unit = array_sum($request->unit);
        $invoice->amount = $tt_amount;
        $invoice->subtotal = array_sum($request->amount);
        $invoice->received_amount = $request->received_amount;
        if($tt_amount > $request->received_amount){
            $invoice->amount_left = $tt_amount -  $request->received_amount;
        }
        else{
            $invoice->advance = $request->received_amount - $tt_amount;
            $invoice->amount_left = $tt_amount -  $request->received_amount;
        }
        
        $checkB = $this->checkMinBalance($cus_det[0] , $invoice->amount_left);
        if($checkB){
            return redirect()->back()->with('error' , 'Customer Balance Limit Exceeded ( Limit is '.$checkB.' )');
        }
        
        $invoice->save();
        
        $invoice_id = $invoice->id;
        
        $invoiceData = $request->all();
        $total_admin_benefit = 0;$total_customer_benefit = 0;$p_amount = 0;
        for($counter = 0;$counter < sizeof($request->amount);$counter++){
            if($invoiceData['amount'][$counter] != '')
            {
                $invoiceDetails = new InvoiceDetail();
                $invoiceDetails->invoice_id = $invoice_id;
                $is_custom_price = CustomPrice::where(['customer_id' => $request->customer_id , 'product_id' => $invoiceData['product_id'][$counter]])->first();
                $is_default_price = Product::find($invoiceData['product_id'][$counter]);
                if(!empty($is_custom_price)){
                    $invoiceDetails->a_benefit = $is_custom_price->a_benefit * $invoiceData['unit'][$counter];
                    $invoiceDetails->c_benefit = $is_custom_price->c_benefit * $invoiceData['unit'][$counter];
                    $invoiceDetails->p_amount = $is_custom_price->product->p_price * $invoiceData['unit'][$counter];
                    // $total_admin_benefit += $is_custom_price->a_benefit * $invoiceData['unit'][$counter];
                    // $total_customer_benefit += $is_custom_price->c_benefit * $invoiceData['unit'][$counter];
                    // $p_amount += $is_custom_price->product->p_price * $invoiceData['unit'][$counter];
                }
                else{
                    $invoiceDetails->a_benefit = $is_default_price->a_benefit * $invoiceData['unit'][$counter];
                    $invoiceDetails->c_benefit = $is_default_price->c_benefit * $invoiceData['unit'][$counter];
                    $invoiceDetails->p_amount = $is_default_price->p_price * $invoiceData['unit'][$counter];
                    
                    // $total_admin_benefit += $is_default_price->a_benefit * $invoiceData['unit'][$counter];
                    // $total_customer_benefit += $is_default_price->c_benefit * $invoiceData['unit'][$counter];
                    // $p_amount += $is_default_price->p_price * $invoiceData['unit'][$counter];
                }
                $invoiceDetails->product_id = $invoiceData['product_id'][$counter];
                $invoiceDetails->unit = $invoiceData['unit'][$counter];
                $invoiceDetails->amount = $invoiceData['amount'][$counter];
                $invoiceDetails->save();
            }
        }
        
        // Invoice::where(['id' => $invoice_id])->update(['a_benefit' => $total_admin_benefit , 'c_benefit' => $total_customer_benefit , 'p_amount' => $p_amount]);
        Invoice::where(['id' => $invoice_id])->update(['a_benefit' => $invoice->invoicedetail->sum('a_benefit') , 'c_benefit' => $invoice->invoicedetail->sum('c_benefit') , 'p_amount' => $invoice->invoicedetail->sum('p_amount')]);
        
        if($this->getAdminId(Auth::id()) != Auth::id()){
            $this->notifyAdmin($invoice_id);   
        }
        
        return redirect()->route('invoices' , 'unapproved')->with('success' , 'Invoice Created');
    }
    
    public function getInvoice($id){
        $added = [];
        $invoice = Invoice::find($id);
        $customPrices = CustomPrice::where('customer_id' , $invoice->customer_id)->get();
        if(sizeof($customPrices)){
            foreach($invoice->invoicedetail as $d){
                foreach($customPrices as $custom){
                    if($custom->product_id == $d->product_id){
                        $d->product->price = $custom->price;
                        $d->product->c_benefit = $custom->c_benefit;
                    }
                }
                $added[] = $d->product_id;
            }
        }
        $products = Product::whereNotIn('id' , $added)->where('user_id' , Auth::id())->get();
        foreach($products as $product){
            foreach($customPrices as $custom){
                if($custom->product_id == $product->id){
                    $product->price = $custom->price;
                    $product->c_benefit = $custom->c_benefit;
                }
            }
        }
        $old_invoices = Invoice::where('customer_id' , $invoice->customer_id)->orderBy('id' , 'desc')->get();
        $old_balance = 0;
        if(sizeof($old_invoices) > 1){
            $old_balance = $old_invoices[1]->amount_left;
        }
        return view('edit_invoice' , compact('invoice' , 'products' , 'old_balance'));
    }
    
    public function customerInvoices($customer_id , $from = "" , $to = ""){
        error_reporting(0);
        if($from != ""){
           $invoices = Invoice::where(['customer_id' => $customer_id])->whereBetween('created_at', array($from, $to))->get();
        }
        else{
            $invoices = Invoice::where(['customer_id' => $customer_id])->get();
        }
        $customer = Customer::find($customer_id);
        
        
        $products = Product::where('user_id' , Auth::id())->get();
        $product_report = [];
        $counter = 0;
        foreach($products as $p){
            $ppunit = 0;$ppamount = 0;
            $product_report[$counter]['id'] = $p->id;
            $product_report[$counter]['name'] = $p->name;
            foreach($invoices as $in){
                $idet =  $in->invoicedetail->where('product_id' , $p->id);
                $ppunit += $idet->sum('unit');
                $ppamount += $idet->sum('p_amount');
            }
            $product_report[$counter]['unit'] = $ppunit;
            $product_report[$counter]['amount'] = ($p->p_price * $ppunit);
            $product_report[$counter]['tamount'] = ($p->price * $ppunit);
            $product_report[$counter]['checks'] = $ppamount;
            
            $counter++;
        }
            
        if($customer->created_by == Auth::id() || Auth::user()->role == 1 || Auth::user()->customer_id == $customer_id){
            return view('customer_invoices' , compact('invoices' , 'customer' , 'product_report'));
        }
        else{
            return redirect()->back()->with('error' , 'Invalid Request');
        }
    }
    
    public function updateInvoice(Request $request , $id){
        $conditions = ['id' => $id];
        $invoice = Invoice::where($conditions)->get();

        $adminSellTotal = AdminSellTotal::where('user_id' , Auth::id())->first();
        if(sizeof($invoice)){
            if($request->amount_left_input){
                $conditions['is_approved'] = 1;
                $amount_left = $invoice[0]->amount_left;
                $invoice = Invoice::where($conditions)->update(['amount_left' => $amount_left - $request->amount_left_input , 'received_amount' => $invoice[0]->received_amount + $request->amount_left_input]);
                return Common::Message("Invoice" , 2);
            }
            else{
                if($this->getAdminId(Auth::id()) == Auth::id() || $invoice[0]->is_approved == null){
                    
                    $invoiceDetail = $invoice[0]->invoicedetail->toArray();
                    
                    if($invoice[0]->is_approved == 1){
                        foreach($invoice[0]->invoicedetail as $detail){
                            $this->updateSellRecord($detail , true);
                        }
                        AdminSellTotal::where('user_id' , $this->getAdminId(Auth::id()))->update(['total_amount' => $adminSellTotal->total_amount - $invoice[0]->amount , 'total_units' => $adminSellTotal->total_units - $invoice[0]->unit , 'a_benefit' => $invoice[0]->a_benefit , 'total_p_amount' => $adminSellTotal->total_p_amount - $invoice[0]->p_amount]);
                    }
                    
                    $this->deleteInvoice($id);

                    $this->storeInvoice($request , $invoice[0]->id);
                    if($invoice[0]->is_approved == 1){
                        $this->approveInvoice($invoice[0]->id , true);
                    }
                    
                    return redirect()->route('invoices' , 'unapproved')->with('success' , 'Invoice Updated');
                }
                else{
                    return Common::Message("Invoice" , 5); 
                }
            }
        }
        else{
            return Common::Message("Invoice");
        }
    }
    
    public function deleteInvoice($id){
        $invoice = Invoice::find($id);
        if(!empty($invoice)){
            Invoice::where('id' , $invoice->id)->delete();
            InvoiceDetail::where('invoice_id' , $id)->delete();
            return Common::Message("Invoice" , 3);
        }else{
            return Common::Message("Invoice");
        }   
    }
    
    public function approveInvoice($id , $is_multiple_or_edited = false){
        
        if(!$is_multiple_or_edited){
            if($this->validPin() !== 1){
                return $this->validPin();
            }
        }

        $invoiceDetails = InvoiceDetail::where('invoice_id' , $id)->get();
        $invoice = Invoice::where('id' , $id)->first();
        $adminSellTotal = AdminSellTotal::where('user_id' , Auth::id())->first();
        if(sizeof($invoiceDetails)){
    
            foreach($invoiceDetails as $invoiceDetail){
                $this->updateSellRecord($invoiceDetail);
            }
            
            if(!empty($adminSellTotal)){

                AdminSellTotal::where('user_id' , Auth::id())->update(['total_amount' => $adminSellTotal->total_amount + (float)$invoice->amount , 'total_units' => $adminSellTotal->total_units + (float)$invoice->unit , 'a_benefit' => $invoice->a_benefit , 'total_p_amount' => $adminSellTotal->total_p_amount + $invoice->p_amount]);
            }
            else{
                $newSell = new AdminSellTotal();
                $newSell->user_id = Auth::id();
                $newSell->total_amount = $invoice->amount;
                $newSell->total_p_amount = $invoice->p_amount;
                $newSell->total_units = $invoice->unit;
                $newSell->a_benefit = $invoice->a_benefit;
                $newSell->save();
            }
            Invoice::where('id' , $id)->update(['is_approved' => 1]);
            return Common::Message("Invoice" , 4);
        }
        else{
            return Common::Message("Invoice");
        }
        
    }
    
    public function approveInvoiceMult(Request $req){
        $multiple = $req->approve_to;
        foreach($multiple as $single){
            $this->approveInvoice($single , true);
        }
        return Common::Message("Invoices" , 4);
    }
    
    public function notifyAdmin($invoice_id){
        $notify = new Notification();
        $notify->user_id = Auth::id();
        $notify->notify_to = Auth::user()->seller_of;
        $notify->invoice_id = $invoice_id;
        $notify->save();
    }
    
    public function updateSellRecord($detail , $undo_changes = false){
        $adminOldRecord = AdminSellRecord::where(['user_id' => Auth::id() , 'product_id' => $detail->product_id])->first();
        if(!empty($adminOldRecord)){
            if(!$undo_changes){
                $adminUpdateData = ['unit' => $adminOldRecord->unit + $detail->unit , 'amount' => $adminOldRecord->amount + $detail->amount , 'p_amount' => $adminOldRecord->p_amount + $detail->p_amount , 'a_benefit' => $adminOldRecord->a_benefit + $detail->a_benefit , 'c_benefit' => $adminOldRecord->c_benefit + $detail->c_benefit];
            }
            else{
                $adminUpdateData = ['unit' => $adminOldRecord->unit - $detail->unit , 'amount' => $adminOldRecord->amount - $detail->amount , 'p_amount' => $adminOldRecord->p_amount - $detail->p_amount , 'a_benefit' => $adminOldRecord->a_benefit - $detail->a_benefit , 'c_benefit' => $adminOldRecord->c_benefit - $detail->c_benefit];
            }
            AdminSellRecord::where(['user_id' => Auth::id() , 'product_id' => $detail->product_id])->update($adminUpdateData);
        }
        else{
            $adminSellRecord = new AdminSellRecord();
            $adminSellRecord->user_id = Auth::id();
            $adminSellRecord->product_id = $detail->product_id;
            $adminSellRecord->unit = $detail->unit;
            $adminSellRecord->amount = $detail->amount;
            $adminSellRecord->p_amount = $detail->p_amount;
            $adminSellRecord->c_benefit = $detail->c_benefit;
            $adminSellRecord->a_benefit = $detail->a_benefit;
            $adminSellRecord->save();
            
        }
    }
    
    public function getAdminId($user_id){
        $check = User::where('id' , $user_id)->first();
        if($check->seller_of == null){
            $admin_id = $user_id;
        }
        else{
            $admin_id = User::where('id' , $check->seller_of)->first()->id;
        }
        return $admin_id;
    }

    public function validPin(){
        if(!session('pin')){
            return redirect()->back()->with('error' , 'Pin Code Validation Failed');
        }
        session(['pin' => '']);
        return 1;
    }


    //AJAX function for invoice details
    public function getinvoiceDetail($id){
        $invoice = Invoice::find($id);
        $idetails = Invoice::find($id)->invoicedetail;
        $prev_invoices = Invoice::where('id' , '<' , $id)->where('customer_id' , $invoice->customer_id)->orderBy('id' , 'desc')->get();
        $prev_invoice = $prev_invoices[0];
        $bill_no = count($prev_invoices);
        return view('ajax.invoice_detail' , compact('idetails' , 'invoice' , 'prev_invoice' , 'bill_no'));
    }
    
    // Date Filter
    public function dateFilter(Request $request , $unapproved = ""){
        $from = str_replace("/" , "-" , $request->from);
        $to = str_replace("/" , "-" , $request->to);
        if($to == null){
            $to = date('Y-m-d');
        }
        if($from == null){
            $from = date('Y-m-d');
        }
        if($request->customer_id){
            return $this->customerInvoices($request->customer_id , $from , $to);
        }
        return $this->index($unapproved , $from , $to);
    }
    
    // Customer Balance Check
    public function checkMinBalance($cid , $balance){
        $customer = Customer::find($cid);
        $old_customer_balance = Invoice::where(['customer_id' => $cid])->orderBy('id' , 'desc')->first()->amount_left;
        if(($old_customer_balance + $balance) > $customer->balance_limit){
            return $customer->balance_limit;
        }
        return false;
    }
    
    public function printInvoice($id)
    {
        error_reporting(0);
        $invoice = Invoice::find($id);
        if(empty($invoice))
            return redirect()->back()->with('error' , 'Not Found!');
        
        $prev_invoices = Invoice::where('id' , '<' , $id)->where('customer_id' , $invoice->customer_id)->orderBy('id' , 'desc')->get();
        $prev_invoice = $prev_invoices[0];
        $bill_no = count($prev_invoices);
        $pdf = PDF::loadView('print_invoice' , compact('invoice' , 'prev_invoice' , 'bill_no'));
        return $pdf->download($invoice->customer->user->name.date('d/m/Y').'.pdf');
         
    }
    
    public function updation()
    {
        $ins = InvoiceDetail::all();
        foreach($ins as $i)
        {
            $p = Product::find($i->product_id);
            $i->p_amount = $p->p_price * $i->unit;
            $i->save();
        }
    }
}
