<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Product;
use App\User;
use Role;
use Response;
use Illuminate\Support\Facades\Validator;
use Purifier;
use JWTAuth;
use Auth;

/*$order->userID=Auth::user()->id;*/

class OrdersController extends Controller
{
  public function __construct()
  {
    $this->middleware("jwt.auth", [
      "only"=>["indexUser","store","update","destroy","indexAdmin","indexByUser","userDestroy"]]);
  }

  #TODO Let user see order history
  public function indexUser()
  {
    $userID = Auth::id();

    $order = Order::where("userId","=",$userID)->get();
    return Response::json($order);
  }

  #TODO allow admin to see all orders
  public function indexAdmin()
  {
    $admin = Auth::user();

    if ($admin->id == 1)
    {
      $order = Order::all();
      return Response::json($order);
    }
  }

  #TODO allow admin to see all orderes made by specific user
  public function indexByUser(Request $request)
  {
    $validator = Validator::make(Purifier::clean($request->all()));

    if ($validator->fails())
    {
      return Response::json(["error" => "Invalid input."]);
    }

    $admin = Auth::user();

    if ($admin->id == 1)
    {
      $userQuery = $request->input('email');
      $order = Order::where('email','=',$userQuery)->get();
    }
  }
  #USER: make order
  public function store(Request $request)
  {
    $user = Auth::user();
    if ($user->roleID == 2)
    {
      $validator = Validator::make(Purifier::clean($request->all()), [
        'productsId' => 'required',
        'amount' => 'required',
        'useAddress' => 'required',
        'comment' => 'required'
      ]);

      if ($validator->fails())
        {
          return Response::json(["error" => "You must fill out all fields."]);
        }

      $id = $request->input('productsId');
      $item = Product::where("categoryId","=",$id)->select("availability")->first();
      $update = Product::where("categoryId","=",$id)->first();
      if ( $item["availability"] == 0)
      {
        return Response::json(["error", "Out of Stock"]);
      }

        // UPDATE PRODUCT INVENTORY 
        $current = $update->quantity;
    
        $update->quantity = $current - ($request->input('amount')); 
         
        // Insert new user into Users table

        $order = new Order;

        $order->userId = Auth::id();
        $order->productsId = $request->input('productsId');

        $order->amount = $request->input('amount');

        # Calculate subtotal
        $subTotal = Product::where("categoryId","=",$id)->select("price")->first();
        $itemsOrdered = $request->input('amount');

        # Calculate total price
        $Total = ($subTotal->price) * $itemsOrdered;

        $order->totalPrice = $Total;
        $order->comment = $request->input('comment');
        $order->useAddress = $request->input('useAddress');

        if ($order->useAddress != 0)
        {
          $order->address = $user->address;
        } 
        else
        {
          $order->address = $request->input('address');
        }

        $order->save();
        $update->save();
        return Response::json(["success" => "You did it"]);
      }
      else
      {
        return Response::json(["error" => "Invalid Credentials"]);
      }
    
  }

  #TODO allow user to change quantity ordered
 /* public function update(Request $request)
  {
    $user = Auth::user();

    if (!empty($user))
    {
      $validator = Validator::make(Purifier::clean($request->all()));

      // update last order
      if (!empty($request->input('quantity')))
      {
        $quantityUpdate = $request->input('quantity');
        $order = Order::where("userId","=",$user->id)->first();

        $order->amount = $quantityUpdate;
      }

      if (!empty($request->input('item')))
      {

        $itemUpdate = $request->input('item');
        $order = Order::where("userId","=",$user->id)->first();

        $order->productsId = $itemUpdate;
      }
      $order->save();
    }
  }*/

    //TODO allow user to cancel current order
    public function userDestroy($id)
    {

        $user = Auth::user();

        $order = Order::where("userId","=",$user->id)->where('id','=',$id)->first();
        $order->delete();

    }
}
