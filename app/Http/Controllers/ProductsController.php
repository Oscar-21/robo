<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use Response;
use Illuminate\Support\Facades\Validator;
use Purifier;

class ProductsController extends Controller {

  public function index() {
    
    $products = Product::all();
  }

  public function storeNewProduct(Request $request) {
    
    $validator = Validator::make(Purifier::clean($request->all()), [

      'description' => 'required',
      'image' => 'required',
      'price' => 'required',
      'name' => 'required',
      'categoryId' => 'required',
      'quantity' => 'required',
      'availability' => 'required',
    ]);

    if ($validator->fails()) 
      return Response::json(["error" => "You must fill out all fields."]);

    $product = new Product;
    $product->description = $request->input('description');
    $product->price = $request->input('price');
    $product->name = $request->input('name');
    $product->categoryId = $request->input('categoryId');
    $product->quantity = $request->input('quantity');
    $product->availability = $request->input('availability');

    $image = $request->file('image');
    $imageName = $image->getClientOriginalName();
    $image->move("storage/", $imageName);
    $product->image = $request->root()."/storage/".$imageName;

    $product->save();

    return Response::json(["success" => "You did it"]);
  }

  public function toggleDescription($id) {

  }

  public function togglePrice($id) {

  }

  public function toggleName($id) {

  }


  public function toggleCategoryId($id) {

  }


  public function toggleQuantity($id) {

  }


  public function toggleAvailability($id) {

  }

  public function shoppingCart() {

    $products=
  }

  public function show($id) {

    $product = Product::find($id);
    return Response::json(['key' => 'message']);
  }
  
  public function destroy($id) {

    $product = Product::find($id);
    $product->delete();

    return Response::json(['key' => 'message']);
  }

}
