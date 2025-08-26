<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Faker\Provider\Image;
use Flasher\Toastr\Laravel\Facade\Toastr;
use GrahamCampbell\ResultType\Success;
use Nette\Utils\Image as UtilsImage;


class AdminController extends Controller
{
  // public function view_category()
  // {
  //   $data = Category::all();
  //   return view('admin.category', compact('data'));
  // }

  // public function add_category(Request $request)
  // {
  //   $category = new Category;
  //   $category->category_name = $request->category;
  //   $category->save();
  //   flash()->success("Category added");
  //   return redirect()->back();
  // }

  // public function delete_category($id)
  // {
  //   $data = Category::find($id);
  //   $data->delete();
  //   flash()->info('Deleted Successfully');
  //   return redirect()->back();
  // }

  // public function edit_category($id)
  // {
  //   $data = Category::find($id);
  //   return view('admin.edit_category', compact('data'));
  // }
  // public function update_category(Request $request, $id)
  // {
  //   $data = Category::find($id);
  //   $data->category_name = $request->category;
  //   $data->save();
  //   flash()->success("Updated Successfully");
  //   return redirect('/view_category');
  // }
  
  // public function add_product()
  // {
  //   $category = Category::all();
  //   return view('admin.add_product', compact('category'));
  // }

  // public function upload_product(Request $request)
  // {
  //   $data = new Product();
  //   $data->title = $request->title;
  //   $data->description = $request->description;
  //   $data->category_id = $request->category;
  //   $data->quantity = $request->quantity;
  //   $data->price = $request->price;
  //   if ($request->hasFile('image')) {
  //     $file = $request->file('image');
  //     $extension = $file->getClientOriginalExtension();
  //     $filename = time() . '.' . $extension;
  //     $file->move(public_path('images'), $filename);
  //     $data->image = $filename;
  //   }
  //   $data->save();
  //   flash()->success("Product added");
  //   return redirect()->back();
  // }
  // public function view_product()
  // {
  //   $products = Product::all();
  //   return view('admin.view_product', compact('products'));
  // }
  // public function delete_product($id)
  // {
  //   $data = Product::find($id);
  //   $image_path = public_path('images/' . $data->image);
  //   if (file_exists($image_path)) {
  //     unlink($image_path);
  //   }
  //   flash()->info('Deleted Successfully');
  //   $data->delete();
  //   return redirect()->back();
  // }
  // public function update_product($id)
  // {
  //   $data = Product::find($id);
  //   $category = Category::all();
  //   return view('admin.update_product', compact('data', 'category'));
  // }
  // public function edit_product(Request $request, $id)
  // {
  //   $data = Product::find($id);
  //   $data->title = $request->title;
  //   $data->description = $request->description;
  //   $data->price = $request->price;
  //   $data->quantity = $request->quantity;
  //   $data->category_id = $request->category;
  //   if ($request->hasFile('image')) {
  //     $file = $request->file('image');
  //     $extension = $file->getClientOriginalExtension();
  //     $filename = time() . '.' . $extension;
  //     $file->move(public_path('images'), $filename);
  //     $data->image = $filename;
  //   }
  //   $data->save();
  //   flash()->success("Updated Successfully");
  //   return redirect()->back();
  // }

  // public function product_search(Request $request)
  // {
  //   $search = $request->search;
  //   $products = Product::where('title', 'LIKE', '%' . $search . '%')->paginate();
  //   return view('admin.view_product', compact('products'));
  // }

}
