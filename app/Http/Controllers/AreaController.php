<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Area;

class AreaController extends Controller
{
    public function list()
    {
        $areas = Area::all();
        return view('list_area', compact('areas'));
    }
    
    public function add(Request $req)
    {
        return view('add_area');
    }
    
    public function edit($id)
    {
        $area = Area::find($id);
        return view('edit_area', compact('area'));
    }
    
    public function save(Request $req, $id = null)
    {
        $req->validate([
            'name' => 'required|max:191',
        ]);
        
        $area = is_null($id) ? new Area() : Area::find($id);
        $area->name = $req->name;
        $area->save();
        
        return back()->with('success', 'Area saved successfully');
    }
    
    public function delete($id)
    {
        Area::find($id)->delete();
        return back()->with('success', 'Area deleted successfully');
    }
}