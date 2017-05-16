<?php
  namespace App\Http\Controllers;
  use Illuminate\Http\Request;
  use App\Product;
  use Response;
  use Illuminate\Support\Facades\Validator;
  use Purifier;
  use Auth;
  use App\User;
  use JWTAuth;
  class ProductsController extends Controller {

  public function __construct() 
  {
    $this->middleware("jwt.auth", ["only"=>["storeNewProduct","update","destroy", "toggleDescription","togglePrice","toggleImage","toggleName","toggleCategoryId","toggleQuantity","toggleAvailability"]]);
  }

    // Return all products
    public function index() 
    {
      $products = Product::all();
      return Response::json($products);
    }


    // ADMIN: store new product
    public function storeNewProduct(Request $request) 
    {
      $user = Auth::user();

      if ($user->roleID != 1 )
      {
        return Response::json(["error" => "No permissions"]);
      }

      $validator = Validator::make(Purifier::clean($request->all()), [
        'description' => 'required',
        'price' => 'required',
        'image' => 'required',
        'quantity' => 'required',
        'name' => 'required',
        'categoryId' => 'required',
        'availability' => 'required',
      ]);

      if ($validator->fails())
      {
        return Response::json(["error" => "You must fill out all fields."]);
      }
        
      // Insert new user into Users table
      $product = new Product;
      $product->description = $request->input('description');
      $product->price = $request->input('price');

      $image = $request->file('image');
      $imageName = $image->getClientOriginalName();
      $image->move("storage/", $imageName);
      $product->image = $request->root()."/storage/".$imageName;

      $product->quantity = $request->input('quantity');
      $product->name = $request->input('name');
      $product->categoryId = $request->input('categoryId');
      $product->availability = $request->input('availability');

      $product->save();
      return Response::json(["success" => "You did it"]);
    }

    public function toggleDescription($id, Request $request) 
    {
      $admin = Auth::user();

      if ($admin->roleID != 1)
      {
        return Response::json(["error" => "Invalid Credentials"]);
      }

      $validator = Validator::make(Purifier::clean($request->all()), [
        'description' => 'required',
      ]);

      $product = Product::where("id","=",$id)->first();
      $product->description = $request->input('description');
      $product->update();
      return Response::json(["success" => "You did it"]);
    }

    public function togglePrice($id, Request $request) 
    {
      $admin = Auth::user();

      if ($admin->roleID != 1)
      {
        return Response::json(["error" => "Invalid Credentials"]);
      }

      $validator = Validator::make(Purifier::clean($request->all()), [
        'price' => 'required',
      ]);
      $product = Product::where("id","=",$id)->first();
      $product->price = $request->input('price');
      $product->update();
      return Response::json(["success" => "You did it"]);
    }

    public function toggleImage($id, Request $request) 
    {

      $admin = Auth::user();

      if ($admin->roleID != 1)
      {
        return Response::json(["error" => "Invalid Credentials"]);
      }

      $validator = Validator::make(Purifier::clean($request->all()), [
        'image' => 'required',
      ]);
      $product = Product::where("id","=",$id)->first();

      $image = $request->file('image');
      $imageName = $image->getClientOriginalName();
      $image->move("storage/", $imageName);
      $product->image = $request->root()."/storage/".$imageName;
      $product->update();
      return Response::json(["success" => "Image updated"]);
    }

    public function toggleName($id, Request $request) 
    {

      $admin = Auth::user();

      if ($admin->roleID != 1)
      {
        return Response::json(["error" => "Invalid Credentials"]);
      }

      $validator = Validator::make(Purifier::clean($request->all()), [
        'name' => 'required',
      ]);
      $product = Product::where("id","=",$id)->first();
      $product->name = $request->input('name');
      $product->update();
      return Response::json(["success" => "You did it"]);
    }

    public function toggleCategoryId($id, Request $request) 
    {

      $admin = Auth::user();

      if ($admin->roleID != 1)
      {
        return Response::json(["error" => "Invalid Credentials"]);
      }

      $validator = Validator::make(Purifier::clean($request->all()), [
        'categoryId' => 'required',
      ]);

      $product = Product::where("id","=",$id)->first();
      $product->categoryId = $request->input('categoryId');
      $product->update();
      return Response::json(["success" => "You did it"]);
    }

    public function toggleQuantity($id, Request $request) 
    {

      $admin = Auth::user();

      if ($admin->roleID != 1)
      {
        return Response::json(["error" => "Invalid Credentials"]);
      }

      $validator = Validator::make(Purifier::clean($request->all()), [
        'quantity' => 'required',
      ]);

      $product = Product::where("id","=",$id)->first();
      $product->quantity = $request->input('quantity');
      $product->update();
      return Response::json(["success" => "You did it"]);
    }

    public function toggleAvailability($id) 
    {

      $admin = Auth::user();

      if ($admin->roleID != 1)
      {
        return Response::json(["error" => "Invalid Credentials"]);
      }

      $product = Product::where("id","=",$id)->first();
      $check = $product->availability;

      if ($check == true)
        $product->availability = false;
      else
        $product->availability = true;

      $product->update();
      return Response::json(["success" => "You did it"]);
    }

    /*public function shoppingCart() 
    {
      $products=
    }*/

    // show individual product page
    public function show($id) 
    {
      $product = Product::find($id);
      return Response::json($product);
    }

    // delete product
    public function destroy($id) 
    {
      $user = Auth::user();

      if ($user->roleID != 1)
      {
        return Response::json(["error" => "Invalid Credentials"]);
      }

      $product = Product::find($id);
      $product->delete();

      return Response::json(['sucess' => 'Deleted Item']);
    }
}
