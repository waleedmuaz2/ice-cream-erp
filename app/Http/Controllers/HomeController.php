<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Invoice;
use App\Customer;
use App\User;
use App\AdminSellTotal;
use Carbon\Carbon;
use Auth;
use App\Product;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    //     $client = new \GuzzleHttp\Client([
    //     'base_uri' => 'http://localhost:8000',
        
    // ]);
    // $response = $client->get('/api/test');
    // dd($response);

        // Get cURL resource
    $curl = curl_init();
    // Set some options - we are passing in a useragent too here
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'http://localhost:8000/api/test',
        // CURLOPT_USERAGENT => 'Codular Sample cURL Request'
    ));
    // Send the request & save response to $resp
    $resp = curl_exec($curl);
    // Close request to clear up some resources
    curl_close($curl);
    dd($resp);
    }
    
    public function userDashboard(){
        if($this->IsBlocked()){
            Auth::logout();
            return redirect()->route('login')->with('error' , 'Your Account Has Been Blocked , Please Contact To Admin For More Information!');
        }
        // return "waleed";
        $urole = Auth::user()->role;
        if($urole < 3){
            $myids = [Auth::id()];
            foreach(Auth::user()->mysellers as $seller){
                $myids[] = $seller->id;
            }
            $tcustomers = sizeof(Customer::where('created_by' , Auth::id())->get());
            $app_in = sizeof(Invoice::whereIn('user_id' , $myids)->where('is_approved' , 1)->get());
            $unapp_in = sizeof(Invoice::whereIn('user_id' , $myids)->where('is_approved' , null)->get());
            $today_in = Invoice::whereIn('user_id' , $myids)->whereDate('created_at', Carbon::today())->get();
            $total_sell = AdminSellTotal::where('user_id', Auth::id())->first()->total_amount ? AdminSellTotal::where('user_id', Auth::id())->first()->total_amount : 0;
            $balance = Invoice::whereIn('user_id' , $myids)->where('is_approved', 1)->get()->sum('amount_left');
            $admin_benefit = AdminSellTotal::where('user_id' , Auth::id())->first()->a_benefit;
        }
        else if($urole == 3){
            $app_in = sizeof(Invoice::whereIn('user_id' , [Auth::id()])->where('is_approved' , 1)->get());
            $unapp_in = sizeof(Invoice::whereIn('user_id' , [Auth::id()])->where('is_approved' , null)->get());
            $today_in = Invoice::whereIn('user_id' , [Auth::id()])->whereDate('created_at', Carbon::today())->get();
            
            $total_sell = Invoice::whereIn('user_id' , [Auth::id()])->where('is_approved', 1)->get()->sum('amount');
            $balance = Invoice::whereIn('user_id' , [Auth::id()])->where('is_approved', 1)->get()->sum('amount_left');
            $customer_benefit = Invoice::whereIn('user_id' , [Auth::id()])->where('is_approved', 1)->get()->sum('c_benefit');
        }
        else if($urole == 4){
            $app_in = sizeof(Invoice::whereIn('customer_id' , [Auth::user()->customer_id])->where('is_approved' , 1)->get());
            $unapp_in = sizeof(Invoice::whereIn('customer_id' , [Auth::user()->customer_id])->where('is_approved' , null)->get());
            $today_in = Invoice::whereIn('customer_id' , [Auth::user()->customer_id])->whereDate('created_at', Carbon::today())->get();
            
            $total_sell = Invoice::whereIn('customer_id' , [Auth::user()->customer_id])->where('is_approved', 1)->get()->sum('amount');
            $balance = Invoice::whereIn('customer_id' , [Auth::user()->customer_id])->where('is_approved', 1)->get()->sum('amount_left');
            $customer_benefit = Invoice::whereIn('customer_id' , [Auth::user()->customer_id])->where('is_approved', 1)->get()->sum('c_benefit');
        }
        
        $products = Product::where('user_id' , Auth::id())->get();
        $product_report = [];
        $counter = 0;
        foreach($products as $p){
            $ppunit = 0;$ppamount = 0;
            $product_report[$counter]['id'] = $p->id;
            $product_report[$counter]['name'] = $p->name;
            foreach($today_in as $in){
                $idet =  $in->invoicedetail->where('product_id' , $p->id);
                $ppunit += $idet->sum('unit');
                $ppamount += $idet->sum('p_amount');
            }
            $product_report[$counter]['unit'] = $ppunit;
            $product_report[$counter]['amount'] = ($p->p_price * $ppunit);
            $product_report[$counter]['checks'] = $ppamount;
            
            $counter++;
        }
        return view('admin_home' , compact('tcustomers' , 'app_in' , 'unapp_in' , 'today_in' , 'total_sell' , 'balance' , 'admin_benefit' , 'customer_benefit', 'product_report'));
    }
    public function IsBlocked(){
        if(Auth::user()->role == 2){
            $blocked = false;
            $created_at = Auth::user()->created_at;
            if(Auth::user()->is_blocked == 1){
                $blocked = true;
            }
            if(time() - strtotime($created_at) >= 31536000){
                $user = Auth::user();
                $user->is_blocked = 1;
                $user->save();
                $blocked = true;
            }
            return $blocked;
        }
        if(Auth::user()->is_blocked == 1){
            return true;
        }
        return false;
    }
}
