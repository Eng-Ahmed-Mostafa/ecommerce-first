<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }
    public function brands()
    {
        $brands = Brand::orderBy('id', 'ASC')->paginate(10);
        return view('admin.brands', compact('brands'));
    }
    public function add_brand()
    {
        return view('admin.brand-add');
    }

    public function store_brand(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048',
        ]);

        // معالجة رفع الصورة
        $image = $request->file('image');
        $imageName = Carbon::now()->timestamp . '.' . $image->getClientOriginalExtension();
        $imagePath = $image->storeAs('brands', $imageName, 'public'); // حفظ الصورة داخل storage/app/public/brands

        // حفظ البيانات في قاعدة البيانات
        Brand::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'image' => $imagePath, // حفظ المسار فقط
        ]);

        return redirect()->route('admin.brands')->with('success', 'Brand added has been successfully!');
    }

    public function edit_brand($id)
    {
        $brand = Brand::find($id);
        return view('admin.brand-edit',compact('brand'));
    }

    public function update_brand(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,'. $request->id,
            'image' => 'required|mimes:png,jpg,jpeg|max:2048',
        ]);
        $brand = Brand::find($request->id);
        $brand->update([
            'name' => $request->name,
            'slug' => $request->slug
        ]);

        if($request->hasFile('image'))
        {
            if($brand->image)
            {
                Storage::delete('public/'.$brand->image);
            }
            $image = $request->file('image');
            $imageName = Carbon::now()->timestamp . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('brands', $imageName, 'public');

            $brand->update(['image' => $imagePath]);
        }

        return redirect()->route('admin.brands')->with('success', 'Brand Edited has been successfully!');
    }

    public function delete_brand($id)
    {
        $brand = Brand::find($id);
        if($brand->image)
        {
            Storage::delete('public/'.$brand->image);
        }
        $brand->delete();
        return redirect()->route('admin.brands')->with('success', 'Brand Deleted has been successfully!');
    }
}
