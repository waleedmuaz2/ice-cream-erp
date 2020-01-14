<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Common;

use App\Product;
use App\CustomPrice;
use App\Category;
use App\AdminSellRecord;

use Auth;

class ProductController extends Controller{
    
    public function index($customer_id = ""){
        $products = Product::where(['user_id' => Auth::id()])->get();
        return view('all_products' , compact('products'));
    }
     public function myProducts($user_id){
        $products = Product::where('created_by' , $user_id)->with('User')->get();
        return Common::Data($products) ? Common::Data($products) : Common::Message("Product");
     }
    
    public function addProduct(){
        $categories = Category::where(['user_id' => Auth::id()])->get();
        return view('add_product' , compact('categories'));
    }
    public function storeProduct(Request $request){
        $product = new Product();
        $product->category_id = $request->category_id;
        $product->user_id = Auth::id();
        $product->name = $request->name;
        $product->price = $request->price;
        $product->c_benefit = $request->c_benefit;
        $product->ot_benefit = $request->ot_benefit;
        $product->p_price = $request->p_price;
        $product->a_benefit = $request->price-$request->p_price;
        $product->save();
        return Common::Message("Product" , 1);
    }
    
    public function getProduct($id){
        $product = Product::find($id);
        $categories = Category::where(['user_id' => Auth::id()])->get();
        return view('edit_product' , compact('product' , 'categories'));
    }
    
    public function updateProduct(Request $request , $id){
        $product = Product::where(['id' => $id , 'user_id' => Auth::id()])->get();
        if(Common::Data($product)){
            Product::where(['id' => $id , 'user_id' => Auth::id()])->update(['name' => $request->name , 'category_id' => $request->category_id , 'price' => $request->price ,
            'a_benefit' =>$request->a_benefit , 'c_benefit' => $request->c_benefit ,'ot_benefit' => $request->ot_benefit , 'p_price' => $request->p_price]);
            return Common::Message("Product" , 2);
        }
        else{
            return Common::Message("Product");
        }
    }
    
    public function deleteProduct($id){
        $product = Product::where(['id' => $id , 'user_id' => Auth::id()])->get();
        if(Common::Data($product)){
            if(sizeof(AdminSellRecord::where('product_id' , $id)->get()) == 0){
                Product::where('id' , $id)->delete();
                return Common::Message("Product" , 3);
            }
            return redirect()->back()->with('error' , 'Product Cannot Be Deleted ( Exist In Sell Record )');
        }
        else{
            return Common::Message("Product");
        }
    }
}