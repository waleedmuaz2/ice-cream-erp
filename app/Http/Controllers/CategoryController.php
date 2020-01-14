<?php

namespace App\Http\Controllers;
use App\Repositories\Common;

use Illuminate\Http\Request;

use App\Category;

use Auth;

class CategoryController extends Controller{
    
    public function index(){
        $categories = Category::where('user_id' , Auth::id())->get();
        return view('all_categories' , compact('categories'));
    }
    
    public function storeCategory(Request $request)
    {
        $category = new Category();
        $category->name = $request->name;
        $category->user_id = Auth::id();
        $category->save();
        return Common::Message("Category" , 1);
    }
    
    public function getCategory($id){
        $category = Category::where('id' , $id)->get();
        return Common::Data($category) ? Common::Data($category) : Common::Message("Category");
    }
    
    public function updateCategory(Request $request , $id){
        $category = Category::where(['id' => $id , 'user_id' => Auth::id()])->get();
        if(Common::Data($category)){
            Category::where(['id' => $id , 'user_id' => Auth::id()])->update(['name' => $request->name]);
            return Common::Message("Category" , 2);
        }
        else{
            return Common::Message("Category");
        }
    }
    
    public function deleteCategory($id){
        $category = Category::where(['id' => $id , 'user_id' => Auth::id()])->get();
        if(Common::Data($category)){
            Category::where('id' , $id)->delete();
            return Common::Message("Category" , 3);
        }
        else{
            return Common::Message("Category");
        }
    }
}