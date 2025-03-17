<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
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

        return redirect()->route('admin.brands')->with('status', 'Brand added has been successfully!');
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

        return redirect()->route('admin.brands')->with('status', 'Brand Edited has been successfully!');
    }

    public function delete_brand($id)
    {
        $brand = Brand::find($id);
        if($brand->image)
        {
            Storage::delete('public/'.$brand->image);
        }
        $brand->delete();
        return redirect()->route('admin.brands')->with('status', 'Brand Deleted has been successfully!');
    }

    public function categories()
    {
        $categories = Category::orderBy('id','ASC')->paginate(10);
        return view('admin.categories',compact('categories'));
    }

    public function add_category()
    {
        return view('admin.category-add');
    }

    public function store_category(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048'
        ]);

        $image = $request->file('image');
        $imageName = Carbon::now()->timestamp . '.' . $image->getClientOriginalExtension();
        $imagePath = $image->storeAs('categories',$imageName,'public');
        Category::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'image' => $imagePath
        ]);
        return redirect()->route('admin.categories')->with('status','Category added has been successfully');
    }

    public function edit_category($id)
    {
        $category = Category::find($id);
        return view('admin.category-edit',compact('category'));
    }

    public function update_category(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,'. $request->id,
            'image' => 'required|mimes:png,jpg,jpeg|max:2048'
        ]);
        $category = Category::find($request->id);
        $category->update([
            'name' => $request->name,
            'slug' => $request->slug,
        ]);

        if($request->hasFile('image'))
        {
            if($category->image)
            {
                Storage::delete('public/'.$category->image);
            }
            $image = $request->file('image');
            $imageName = Carbon::now()->timestamp . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('categories',$imageName,'public');

            $category->update(['image' => $imagePath]);
        }
        return redirect()->route('admin.categories')->with('status','Category updated has been successfully');
    }

    public function delete_category($id)
    {
        $category = Category::find($id);
        if($category->image)
        {
            Storage::delete('public/'. $category->image);
        }
        $category->delete();
        return redirect()->route('admin.categories')->with('status', 'Category Deleted has been successfully!');
    }

    public function products()
    {
        $products = Product::orderBy('id','ASC')->paginate(10);
        return view('admin.products', compact('products'));
    }

    public function add_product()
    {
        $categories = Category::select('id', 'name')->orderBy('name',"ASC")->get();
        $brands = Brand::select('id', 'name')->orderBy('name',"ASC")->get();
        return view('admin.product-add',compact('categories','brands'));
    }

    public function store_product(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug',
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048',
            'images' => 'required',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);
        // main image
        $image = $request->file('image');
        $imageName = Carbon::now()->timestamp . '.' . $image->getClientOriginalExtension();
        $imagePath = $image->storeAs('products', $imageName, 'public');

        // gallery images
        $gimagePaths = [];
        foreach($request->file('images') as $gimage)
        {
            $gimageName = Carbon::now()->timestamp . '_' . uniqid()  . '.' . $gimage->getClientOriginalExtension();
            $gimagePath = $gimage->storeAs('products', $gimageName, 'public');

            $gimagePaths[] = $gimagePath;
        }

        Product::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'short_description' => $request->short_description,
            'description' => $request->description,
            'regular_price' => $request->regular_price,
            'sale_price' => $request->sale_price,
            'SKU' => $request->SKU,
            'stock_status' => $request->stock_status,
            'featured' => $request->featured,
            'quantity' => $request->quantity,
            'image' => $imagePath,
            'images' => implode(',',$gimagePaths),
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
        ]);
        return redirect()->route('admin.products')->with('status','Product has been created successfully');
    }

    public function edit_product($id)
    {
        $product = Product::find($id);
        $categories = Category::select('id', 'name')->orderBy('name',"ASC")->get();
        $brands = Brand::select('id', 'name')->orderBy('name',"ASC")->get();
        return view('admin.product-edit',compact('product','categories','brands'));
    }

    public function update_product(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug,'. $request->id,
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'mimes:png,jpg,jpeg|max:2048',
            'images.*' => 'required|image|mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);

        $product = Product::findOrFail($request->id);

        $product->update([
            'name' => $request->name,
            'slug' => $request->slug,
            'short_description' => $request->short_description,
            'description' => $request->description,
            'regular_price' => $request->regular_price,
            'sale_price' => $request->sale_price,
            'SKU' => $request->SKU,
            'stock_status' => $request->stock_status,
            'featured' => $request->featured,
            'quantity' => $request->quantity,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
        ]);

        // main image
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::delete('public/' . $product->image); // حذف الصورة القديمة
            }

            $image = $request->file('image');
            $imageName = Carbon::now()->timestamp . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('products', $imageName, 'public');

            $product->update(['image' => $imagePath]);
        }

         // gallery images
        if ($request->hasFile('images')) {
            // remove old images
            if (!empty($product->images)) {
                foreach (explode(',', $product->images) as $oldImage) {
                    Storage::delete('public/' . $oldImage);
                }
            }

            // upload new images
            $newImagePaths = [];
            foreach ($request->file('images') as $gimage) {
                $gimageName = Carbon::now()->timestamp . '_' . uniqid() . '.' . $gimage->getClientOriginalExtension();
                $gimagePath = $gimage->storeAs('products', $gimageName, 'public');

                $newImagePaths[] = $gimagePath;
            }

            $product->update(['images' => implode(',', $newImagePaths)]);
        }

        return redirect()->route('admin.products')->with('status', 'Product has been updated successfully');
    }



    public function delete_product($id)
    {
        $product = Product::find($id);

            if($product->image)
            {
                Storage::delete('public/'.$product->image);
            }

            foreach(explode(',',$product->images) as $image)
            {
                if($image)
                {
                    Storage::delete('public/'. $image);
                }
            }
        $product->delete();
        return redirect()->route('admin.products')->with('status','Product has been deleted successfully');
    }
}