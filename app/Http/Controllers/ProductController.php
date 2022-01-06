<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function products()
    {
        $products = Product::all();
        return view('admin.products')->with('products', $products);
    }
    public function addproduct()
    {
        $categories = Category::all()->pluck('category_name', 'category_name');
        return view('admin.addproduct')->with('categories', $categories);
    }
    public function saveproduct(Request $request)
    {
        $this->validate($request, [
            'product_name' => 'required',
            'product_price' => 'required',
            'product_category' => 'required',
            'product_image' => 'image|nullable|max:1999'
        ]);

        if ($request->hasFile('product_image')) {
            // get file nam with Extension
            $fileNameWithExt = $request->file('product_image')->getClientOriginalName();
            // get just file extension
            $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            // get just file extension
            $extension = $request->file('product_image')->getClientOriginalExtension();
            // file name to store
            $fileNameToStore = $fileName . '_' . time() . '.' . $extension;

            // upload image
            $path = $request->file('product_image')->storeAs('public/product_images', $fileNameToStore);
        } else {
            $fileNameToStore = 'noimage.jpg';
        }

        $product = new Product();
        $product->product_name     = $request->input('product_name');
        $product->product_price    = $request->input('product_price');
        $product->product_category = $request->input('product_category');
        $product->product_image    = $fileNameToStore;
        $product->status           = 1;
        $product->save();
        return back()->with('status', 'The Product has been successfully saved !!');
    }
    public function edit_product(Request $request, $id)
    {
        $product = Product::find($id);
        $categories = Category::all()->pluck('category_name', 'category_name');
        return view('admin.edit_product')->with('product', $product)->with('categories', $categories);
    }


    public function updateproduct(Request $request)
    {
        $this->validate($request, [
            'product_name' => 'required',
            'product_price' => 'required',
            'product_category' => 'required',
            'product_image' => 'image|nullable|max:1999'
        ]);

        $product = Product::find($request->input('id'));
        $product->product_name     = $request->input('product_name');
        $product->product_price    = $request->input('product_price');
        $product->product_category = $request->input('product_category');
        if ($request->hasFile('product_image')) {
            // get file nam with Extension
            $fileNameWithExt = $request->file('product_image')->getClientOriginalName();
            // get just file extension
            $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            // get just file extension
            $extension = $request->file('product_image')->getClientOriginalExtension();
            // file name to store
            $fileNameToStore = $fileName . '_' . time() . '.' . $extension;

            // upload image
            $path = $request->file('product_image')->storeAs('public/product_images', $fileNameToStore);
            if ($product->product_image != 'noimage.jpg') {
                Storage::delete('public/product_images/' . $product->product_image);
            }
            $product->product_image = $fileNameToStore;
        }

        $product->update();
        return redirect('/products')->with('status', 'The Product has been successfully updated !!');
    }

    public function delete_product($id)
    {
        $product = Product::find($id);
        if ($product->product_image != 'noimage.jpg') {
            Storage::delete('public/product_images/' . $product->product_image);
        }
        $product->delete();
        return back()->with('status', 'The Product has been successfully deleted !!');
    }

    public function activate_product($id)
    {
        $product = Product::find($id);
        $product->status = 0;
        $product->update();
        return back()->with('status', 'The Product has been successfully Activated !!');
    }
    public function unactivate_product($id)
    {
        $product = Product::find($id);
        $product->status = 1;
        $product->update();
        return back()->with('status', 'The Product has been successfully UnActivated !!');
    }

    public function view_product_by_category($category_name)
    {
        $products = Product::all()->where('product_category', $category_name)->where('status', 1);
        $categories = Category::all();

        return view('client.shop')->with('products', $products)->with('categories', $categories);
    }
}
