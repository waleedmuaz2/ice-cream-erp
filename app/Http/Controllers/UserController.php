<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Repositories\Common;

use App\User;
use App\PaidAmount;
use App\AdminSellTotal;
use App\AdminSellRecord;

use App\OauthAccessToken;

use Auth;

class UserController extends Controller
{
    public function loginUser(Request $request)
    {
        Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required'])->validate();
        
        $email = $request->email;
        $password = $request->password;
        
        $client = new \GuzzleHttp\Client;
        try {
            $response = $client->post('http://oriaitsolution.com/scoops/oauth/token', [
                'form_params' => [
                    'client_id' => 2,
                    'client_secret' => 'jnHd1lW9q9wiADYNJXjPeZz0K81G03qTXhunRye1',
                    'grant_type' => 'password',
                    'username' => $email,
                    'password' => $password,
                    'scope' => '*',
                ]
            ]);
    
            $auth = json_decode( (string) $response->getBody() );
            $auth = (array)$auth;
            $auth['user'] = User::where('email' , $email)->first()->toArray();
            
            return ['messasge' => 'User logged in !' , 'access_token' => $auth['access_token'] , 'user' => $auth['user']];
            
        } catch (GuzzleHttp\Exception\BadResponseException $e) {
            echo "Unable to retrieve access token.";
        }
    }
    
    public function logoutUser($user_id){
            OauthAccessToken::where('user_id' , $user_id)->delete();
            return response()->json(['message' => 'User Logged out']);
    }

    public function paidHistory(){
        $paid_amounts = PaidAmount::where('user_id' , Auth::id())->get();
        return view('paid_amounts' , compact('paid_amounts'));
    }
    
    public function payAmount(Request $request){
        if($request->amount > 0){
            $amountTotal = AdminSellTotal::where('user_id' , Auth::id())->first();
            
            $paid = new PaidAmount();
            $paid->user_id = Auth::id();
            $paid->total_is = $amountTotal->total_p_amount;
            $paid->paid = $request->amount;
            $paid->save();

            
            // if($now_amount == 0){
            //     $amountTotal->total_units = 0;
            //     //AdminSellRecord::where('user_id' , Auth::id())->delete();
            //     // PaidAmount::where('user_id' , Auth::id())->delete();
            // }
            $amountTotal->total_p_amount = $amountTotal->total_p_amount - $request->amount;
            $amountTotal->save();
            return Common::Message('Paid History' , 6);
        }
        else{
            return Common::Message('Paid History' , 7);
        }
    }

    public function sellRecord(){
        $sell_records = AdminSellRecord::where('user_id' , Auth::id())->get();
        $remaining = AdminSellTotal::where('user_id' , Auth::id())->first();
        return view('sell_records' , compact('sell_records' , 'remaining'));
    }


    public function validatePin(Request $request){
        if(Auth::user()->role < 3){
            if(Auth::user()->pincode == $request->pin){
                session(['pin' => $request->pin]);
                return 1;
            }
            else{
                return "Invalid Pin Code !";
            }
        }
        else{
            return "Invalid Request";
        }
    }
    
    public function clearAll(){
        if(session('pin')){
            if(Auth::user()->role < 3){
                if(AdminSellRecord::where('user_id' , Auth::id())->sum('p_amount') - PaidAmount::where('user_id' , Auth::id())->get()->sum('paid') == 0){
                    AdminSellRecord::where('user_id' , Auth::id())->delete();
                    PaidAmount::where('user_id' , Auth::id())->delete();
                    return redirect()->back()->with('success' , 'Sell Record Clear');
                }
                else{
                    return redirect()->back()->with('error' , 'Record Cannot Be Cleared ( Pay Amount First )');
                }
            }
            else{
                return redirect()->back()->with('error' , 'Invalid Request !');
            }
        }
        else{
            return redirect()->back()->with('error' , 'Pin Code Validation Failed !');
        }
    }
    
    public function sellTotalClear(){
       if(session('pin')){
            if(sizeof(AdminSellRecord::where('user_id' , Auth::id())->get()) == 0){
            AdminSellTotal::where('user_id' , Auth::id())->delete();
            return redirect()->back()->with('success' , 'Sell Total Clear');
        }
        return redirect()->back()->with('error' , 'Canot Be Cleared ( Sell Record Exist )');
       }
    }
}
