<?php

namespace App\Http\Controllers;
use App\Repositories\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Auth;

use App\OtCustomer;
use App\User;
use App\Product;
use App\CustomOtBenefit;
use App\CustomPrice;
use App\Customer;
use App\Order;
use App\OrderDetail;
use App\Ordertaker;
use App\PaidOtBenefit;

class OTController extends Controller{

    public function index(){        
        $ordertaker = User::where('role', 5)->where('ot_of',Auth::id())->has('ordertaker')->with('ordertaker')->get();
        return view('all_ordertakers' , compact('ordertaker'));
    }

    public function getOT($id){
        $ot = User::find($id);
        $custom_prices=CustomOtBenefit::where('ot_id',$id)->get();
        $customers=Customer::where('created_by', Auth::id())->get();
        $ot_customers = OtCustomer::where('ot_id', $ot->id)->get()->pluck('customer_id')->toArray();
        return view('edit_ordertaker' , compact('ot','custom_prices','ot_customers', 'customers'));
    }

    public function updateOT(Request $request , $id){
    $request->validate([
        'email' => 'required'
    ]);
    if(empty($request->name) || empty($request->email)){
        return redirect()->back()->with('error' , 'Data Not Entered Correctly!');
    }
    // dd($request);
    $ot=User::find($id);
    $ot->name = $request->name;
    $ot->email = $request->email;
    $ot->phone = $request->phone;
    if($request->password){
        $ot->password = bcrypt($request->password);
    }
    if($request->has('custom')){
        $ot->is_ot_custom=1;
    }
    $ot->save();
    $productData = $request->all();
  
        CustomOtBenefit::where('ot_id',$id)->delete();
        if(!empty($request->product_id)){
            for($i = 0;$i < sizeof($request->product_id);$i++){
                $customB = new CustomOtBenefit();
                $customB->ot_id = $ot->id;
                $customB->product_id = $productData['product_id'][$i];
                $customB->ot_benefit = $productData['ot_benefit'][$i];
                $customB->save();
            }
        }
        OtCustomer::where('ot_id',$id)->delete();

        if(!empty($request->customer_id)){
            for($i = 0;$i < sizeof($request->customer_id);$i++){
                $customerOT = new OtCustomer();
                $customerOT->ot_id = $ot->id;
                $customerOT->customer_id = $productData['customer_id'][$i];
                $customerOT->save();
            }
        }
      
    return Common::Message("Order Taker",2);
    }

    public function deleteOT($id){
        $ot = User::where('id', $id)->get();
        if(sizeof($ot)){
            User::where('id', $id)->delete();
            return Common::Message("Order Taker" , 3);
        }
        else{
            return Common::Message("Order Taker");
        }
    }

    public function ChangeUserStatus($id , $status){
        if(session('pin')){

            $updateData = ['created_at' => date('Y-m-d H:i:s') , 'is_blocked' => $status == "block" ? 1 : 0 ];
            User::where('id', $id)->update($updateData);

            session(['pin' => '']);
            return redirect()->back()->with('success' , 'Order Taker Account Status Has Been Changed Successfully');
        }
    }
    public function paidHistory($id){
        $paid_benefits = PaidOtBenefit::where('user_id', Auth::id())->where('ot_id', $id)->get();
        return view('paid_benefits' , compact('paid_benefits'));
    }
    
    public function payAmount($id,Request $request){
        if($request->amount > 0){

            $ot=Ordertaker::where('user_id' , $id)->first();
            $ben_paid = $request->amount+$ot->ben_paid;

            Ordertaker::where('user_id' , $id)->update(['ben_paid'=>$ben_paid]);

            $total=Ordertaker::where('user_id' , $id)->sum('ben_earned');
            $paid=Ordertaker::where('user_id' , $id)->sum('ben_paid');
            
            $paid_benefit=new PaidOtBenefit();
            $paid_benefit->user_id=Auth::id();
            $paid_benefit->ot_id=$id;
            $paid_benefit->total_is=$total;
            $paid_benefit->paid=$paid;
            $paid_benefit->save();

            return Common::Message('Paid History' , 6);
        }
        else{
            return Common::Message('Paid History' , 7);
        }
    }
}