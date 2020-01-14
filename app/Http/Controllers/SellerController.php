<?php

namespace App\Http\Controllers;
use App\Repositories\Common;

use Illuminate\Http\Request;

use App\User;
use App\Invoice;
use App\Product;
use App\Customer;
use App\CustomOtBenefit;
use App\OtCustomer;
use App\Ordertaker;

use Auth;

class SellerController extends Controller{
    
    public function index(){
        $sellers = User::where('seller_of',Auth::id())->get();
        return view('all_sellers' , compact('sellers'));
    }

    public function indexAll($admin_id){
        $sellers = User::where('seller_of',$admin_id)->get();
        $subadmin_name = User::find($admin_id)->name;
        return view('all_sellers' , compact('sellers' , 'subadmin_name'));
    }

    public function addSeller(){
        $products = Product::where(['user_id' => Auth::id()])->get();
        $customers = Customer::where('created_by' , Auth::id())->get();
        return view('add_user', compact('products','customers'));
    }
    
    public function storeSeller(Request $request , $update_id = 0)
    {
         $request->validate([
            'email' => 'required|unique:users'
        ]);
        
        if(empty($request->name) || empty($request->email) || empty($request->password)){
            return redirect()->back()->with('error' , 'Data Not Entered Correctly!');
        }
        $seller = $update_id == 0 ? new User() : User::find($update_id);
        $seller->name = $request->name;
        $seller->email = $request->email;
        $seller->phone = $request->phone;
        if($request->password){
            $seller->password = bcrypt($request->password);
        }
        if($request->type == 1){
            $seller->seller_of = Auth::id();   
            $seller->role = 3;
        }
        else if($request->type == 3){
            $seller->role = 5;
            $seller->ot_of = Auth::id();
            
            if($request->has('custom')){
                $seller->is_ot_custom=1;
            }
            $seller->save();
            $new_ot_id = User::orderBy('id','desc')->first()->id;
            $productData = $request->all();

            if(!empty($request->product_id)){
                for($i = 0;$i < sizeof($request->product_id);$i++){
                    $customB = new CustomOtBenefit();
                    $customB->ot_id = $new_ot_id;
                    $customB->product_id = $productData['product_id'][$i];
                    $customB->ot_benefit = $productData['ot_benefit'][$i];
                    $customB->save();
                }
            }

            if(!empty($request->customer_id)){
                for($i = 0;$i < sizeof($request->customer_id);$i++){
                    $customerOT = new OtCustomer();
                    $customerOT->ot_id = $new_ot_id;
                    $customerOT->customer_id = $productData['customer_id'][$i];
                    $customerOT->save();
                }
            }

            $ordertaker=new Ordertaker();
            $ordertaker->user_id=$new_ot_id;
            $ordertaker->ben_earned=0;
            $ordertaker->ben_paid=0;
            $ordertaker->save();

          
        return Common::Message("Order Taker" , 1);
        }
        else{
            $seller->user_of = Auth::id();
            $seller->role = 2;
            if($update_id == 0){
                $seller->is_blocked = 1;
            }
            $seller->pincode = $request->pin;
        }
        $seller->save();
        return Common::Message("User" , $update_id == 0 ? 1 : 2);
    }
    
    public function getSeller($id){
        $seller = User::find($id);
        return view('edit_user' , compact('seller'));
    }

    public function getSellerSells($id){
        $invoices = Invoice::where('user_id' , $id)->get();
        $seller = User::find($id);
        if($seller->seller_of == Auth::id() || Auth::user()->role == 1){
            return view('seller_invoices' , compact('invoices' , 'seller'));
        }
        else{
            return redirect()->back()->with('error' , 'Invalid Request');
        }
    }
    
    public function updateSeller(Request $request , $id , $user_id){
        $seller = User::where(['id' => $id , 'seller_of' => $user_id])->get();
        
        if(Common::Data($seller)){
            $updateData = ['name' => $request->name , 'password' => bcrypt($request->password) , 'phone' => $request->phone];
            if(empty(User::where('email' , $request->email)->first()->name)){
                $updateData['email'] = $request->email;
            }
        
        
        
            User::where(['id' => $id , 'seller_of' => $user_id])->update($updateData);
            return Common::Message("Seller" , 2);
        }
        else{
            return Common::Message("Seller");
        }
    }
    
    public function deleteSeller($id){
        $seller = User::where(['id' => $id , 'seller_of' => Auth::id()])->get();
        if(sizeof($seller)){
            User::where(['id' => $id , 'seller_of' => Auth::id()])->delete();
            return Common::Message("Seller" , 3);
        }
        else{
            return Common::Message("Seller");
        }
    }
    
    public function ChangeUserStatus($id , $status){
        if(session('pin')){
            $user = User::find($id);
            $user->is_blocked = $status == "block" ? 1 : 0;
            $user->created_at = date('Y-m-d H:i:s');
            $user->save();
            session(['pin' => '']);
            return redirect()->back()->with('success' , 'Admin Account Status Has Been Changed Successfully');
        }
    }

    public function getUsers(){
        $sub_admins = User::where('user_of' , '!=' , null)->get();
        return view('sub_admins' , compact('sub_admins'));
    }
}