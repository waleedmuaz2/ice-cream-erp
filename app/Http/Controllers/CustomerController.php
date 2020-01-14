<?php

namespace App\Http\Controllers;
use App\Repositories\Common;

use Illuminate\Http\Request;

use App\User;
use App\Customer;
use App\CustomPrice;
use App\Product;
use App\Invoice;
use App\Order;
use App\Area;
use App\CustomOtBenefit;
use App\Ordertaker;
use Illuminate\Support\Facades\DB;
use Auth;
use App\OtCustomer;

class CustomerController extends Controller{
    
    public function index(){     
        $ids = [Auth::id()];
        if(Auth::user()->role < 3)
            $ids = array_merge($ids, User::where('ot_of', Auth::id())->pluck('id')->toArray());
        $customers = Customer::whereIn('created_by' , $ids)->get();
        return view('all_customers' , compact('customers'));
    }

    public function indexAll($admin_id){
        $customers = Customer::where('created_by' , $admin_id)->get();
        $subadmin_name = User::find($admin_id)->name;
        return view('all_customers' , compact('customers' , 'subadmin_name'));
    }
    
    public function myCustomers($user_id){
        $customers = Customer::where('created_by' , $user_id)->with('User')->get();
        return Common::Data($customers) ? Common::Data($customers) : Common::Message("Customer");
    }

    public function addCustomer(){
        $areas = Area::all();
        if(Auth::user()->role == 5){
            $allowed_products = CustomOtBenefit::where('ot_id', Auth::user()->id)->pluck('product_id')->toArray();
            $check=Auth::user()->is_ot_custom;
            if(Auth::user()->is_ot_custom==1){

                $products = DB::table('products')
                            ->join('custom_ot_benefits', 'products.id', 'custom_ot_benefits.product_id')
                            ->where('custom_ot_benefits.ot_id',Auth::user()->id)
                            ->whereIn('product.id', $allowed_products)
                            ->select('products.*','custom_ot_benefits.ot_benefit')
                            ->get();
                
                return view('create_customer' , compact('products', 'areas'));
            }
            else{
                $products = Product::whereIn('id', $allowed_products)->get();
                return view('create_customer', compact('products', 'areas'));
            }
        }

        else{
        $products = Product::where('user_id',Auth::id())->get();
        return view('create_customer' , compact('products', 'areas'));
        }
    }
     
    public function storeCustomer(Request $request)
    {
        $request->validate([
            'email' => 'required|unique:users',
            'area' => 'required'
        ]);
        
        if($request->image){
            $request->validate([
                'image' => 'required|max:2000|mimes:jpg,jpeg,png,PNG'
            ]);
        }
        
        if(empty($request->name) || empty($request->email) || empty($request->password)){
            return redirect()->back()->with('error' , 'Data Not Entered Correctly!');
        }
        $customer = new User();
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->password = bcrypt($request->password);
        $customer->role = 4;
        $customer->save();
        
        $new_user_id = User::orderBy('id','desc')->first()->id;
        
        $customerData = new Customer();
        $customerData->user_id = $new_user_id;
        $customerData->area_id = $request->area;
        $customerData->created_by = Auth::id();
        $customerData->address = $request->address;
        $customerData->phone = $request->phone;
        $customerData->cnic = $request->cnic;
        $customerData->location_url = $request->location_url;
        $customerData->balance_limit = $request->balance_limit ?: 0;
        $customerData->freezer_model = $request->freezer_model;
        $customerData->other = $request->other;
        $customerData->allowed_products = implode('|', $request->allowed_products);
        $customerData->final_allowed_products = implode('|', $request->final_allowed_products);
        
        if($request->image){
            $img = $request->image;
            $upload_image = time().$img->getClientOriginalName();
            $img->move('images/agreements', $upload_image);
            $customerData->image = 'images/agreements/'.$upload_image;
        }

        $customerData->save();
        
        $new_customer_id = Customer::orderBy('id','desc')->first()->id;
        
        User::where('id' , $new_user_id)->update(['customer_id' => $new_customer_id]);
        
        $productData = $request->all();
        if(!empty($request->product_id)){
            for($i = 0;$i < sizeof($request->product_id);$i++){
                $customP = new CustomPrice();
                $customP->customer_id = $new_customer_id;
                $customP->product_id = $productData['product_id'][$i];
                $customP->price = $productData['price'][$i];
                // $customP->p_price = $productData['p_price'][$i];
                $customP->a_benefit = $productData['a_benefit'][$i];
                $customP->c_benefit = $productData['c_benefit'][$i];
                $customP->save();
            }
        }
        
        if(Auth::user()->role == 5)
        {
            $customerOT = new OtCustomer();
            $customerOT->ot_id = Auth::id();
            $customerOT->customer_id = $customerData->id;
            $customerOT->save();
        }
        return Common::Message("Customer" , 1);
    }
    
    public function getCustomer($id){
        $areas = Area::all();
        $customer = Customer::find($id);
        $products = Product::where('user_id',Auth::id())->get();
        return view('edit_customer' , compact('customer' , 'products', 'areas'));
    }
    
    public function updateCustomer(Request $request , $id){
        $customer = Customer::where(['id' => $id , 'created_by' => Auth::id()])->get();
        if(sizeof($customer)){
            $updateUser['name'] = $request->name;
            if($request->password){
                $updateUser['password'] =  bcrypt($request->password);
            }
            $updateCustomer = ['address' => $request->address , 'phone' => $request->phone , 'area_id' => $request->area,'allowed_products' => implode('|', $request->allowed_products),'final_allowed_products' => implode('|', $request->final_allowed_products),
            'cnic' => $request->cnic , 'freezer_model' => $request->freezer_model , 'other' => $request->other , 'location_url' => $request->location_url , 'balance_limit' => $request->balance_limit];
            if($request->image){
                $img = $request->image;
                $upload_image = time().$img->getClientOriginalName();
                $img->move('images/agreements', $upload_image);
                $updateCustomer['image'] = 'images/agreements/'.$upload_image;
            }
            if(empty(User::where('email' , $request->email)->first())){
                $updateUser['email'] = $request->email;
            }
            User::where('id' , $customer[0]->user_id)->update($updateUser);
            Customer::where('id' , $id)->update($updateCustomer);
            
            $productData = $request->all();
            if($request->this_id){
                for($i = 0;$i < sizeof($request->this_id);$i++){
                    $customP = CustomPrice::find($request->this_id[$i]);                    
                    $customP->price = $productData['c_price'][$i];
                    // $customP->p_price = $productData['p_price'][$i];
                    $customP->a_benefit = $productData['c_a_benefit'][$i];
                    $customP->c_benefit = $productData['c_c_benefit'][$i];
                    $customP->save();
                }
            }
            if(!empty($request->product_id)){
                for($i = 0;$i < sizeof($request->product_id);$i++){
                    $customP = new CustomPrice();
                    $customP->customer_id = $id;
                    $customP->product_id = $productData['product_id'][$i];
                    $customP->price = $productData['price'][$i];
                    // $customP->p_price = $productData['p_price'][$i];
                    $customP->a_benefit = $productData['a_benefit'][$i];
                    $customP->c_benefit = $productData['c_benefit'][$i];
                    $customP->save();
                }
            }
            return Common::Message("Customer" , 2);
        }
        else{
            return Common::Message("Customer");
        }
    }
    
    public function deleteCustomer($id){
        $customer = Customer::where(['id' => $id])->get();
        if(sizeof($customer)){
            if(Invoice::where('customer_id' , $id)->get()->sum('amount_left') == 0){
                Customer::where('id' , $id)->delete();
                User::where('customer_id' , $id)->delete();
                Invoice::where('customer_id' , $id)->delete();
                return Common::Message("Customer" , 3);
            }
            return redirect()->back()->with('error' , 'Customer Cannot Be Deleted Due To Balance !');
        }
        return Common::Message("Customer");
    }

    public function checkCustomPrice($customer_id){
        $parent = Auth::id();
        $customer = Customer::find($customer_id);
        
        $shortListed = explode('|', $customer->allowed_products);
        if(Auth::user()->role == 3){
            $parent = Auth::user()->seller_of;
        }
        if(Auth::user()->role == 5){
            $products = Product::whereIn('id', CustomOtBenefit::where('ot_id', Auth::id())->pluck('product_id')
            ->toArray())->orderBy('category_id' , 'asc')
            ->whereIn('id', explode('|', $customer->final_allowed_products))->get();
            
        }
        else{
            $products = Product::where('user_id', $parent)->orderBy('category_id' , 'asc')
            ->whereIn('id', explode('|', $customer->final_allowed_products))->get();
        }
        
       
        $customPrices = CustomPrice::where('customer_id' , $customer_id)->get();
        if(sizeof($customPrices)){
            foreach($products as $product){
                foreach($customPrices as $custom){
                    if($custom->product_id == $product->id){
                        $product->price = $custom->price;
                        $product->c_benefit = $custom->c_benefit;
                    }
                }
            }
        }
        if(Auth::user()->role == 5){
            $order_balance = Order::where([
                'customer_id' => $customer_id,
                'is_confirmed_admin' => null
            ])->orderBy('id', 'desc')->first();
            if($order_balance)
                $old_balance = $order_balance->amount_left;
            else
                $old_balance = Invoice::where(['customer_id' => $customer_id])->orderBy('id' , 'desc')->first()->amount_left;
            $order_count = sizeof(Order::where(['customer_id' => $customer_id, 'is_confirmed_admin' => NULL])->get());
            $invoices_count = sizeof(Invoice::where(['customer_id' => $customer_id])->get());
            
            $invoice_count = $order_count + $invoices_count ;
        }
        else{
            $old_balance = Invoice::where(['customer_id' => $customer_id])->orderBy('id' , 'desc')->first()->amount_left;
            $invoice_count = sizeof(Invoice::where(['customer_id' => $customer_id])->get()) + 1;
            
            $prevOrder = Order::where([
                'customer_id' => $customer_id,
                'is_confirmed_seller' => null,
                'is_confirmed_admin' => null
            ])->first();
            if($prevOrder){
                return 'not_allowed';
            }
        }
        return view('ajax.process_custom_price' , compact('products' , 'old_balance' , 'invoice_count', 'notAllowed', 'shortListed'));
    }
}