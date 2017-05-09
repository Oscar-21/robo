<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Product;
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
    $this->middleware("jwt.auth", ["only"=>["indexUser","store","update","destroy"]]);
  }

  // Let user see order history
  public function indexUser() 
  {
    $user = Auth::user();

    $order = Order::where("userId","=",$user->id)->get();
    return Response::json($order);
  }

  public function indexAdmin()
  {
    $order = Order::all();
    return Response::json($order);
  }

  public function indexByUser(Request $request)
  {
    $validator = Validator::make(Purifier::clean($request->all()));
  
    if ($validator->fails())
    {
      return Response::json(["error" => "Invalid input."]);
    }

    $user = $request->input('email');
    $order = Order::where('email','=',$user->id)->get();
  }



  public function store(Request $request) 
  {
      $validator = Validator::make(Purifier::clean($request->all()), [
        'productsId' => 'required',
        'amount' => 'required',
        'totalPrice' => 'required',
        'comment' => 'required',
        'useAddress' => 'required',
      ]);

      if ($validator->fails())
      {
        return Response::json(["error" => "You must fill out all fields."]);
      }

      $user = Auth::user();

      if ($user->roleID == 2)
      {
        // Insert new user into Users table
        $order = new Order;

        $order->userId = Auth::user()->id;
        $order->productsId = $request->input('productsId');
        $order->amount = $request->input('amount');

        # Calculate subtotal
        $subTotal = $request->input('totalPrice');
        $itemsOrdered = $request->input('amount');

        # Calculate total price
        $Total = $subTotal * $itemsOrdered;
        
        $order->totalPrice = $Total;
        $order->comment = $request->input('comment');

        $order->save();

        return Response::json(["success" => "You did it"]);
      }
      else
      {
        return Response::json(["error" => "You need to log in to place an order."]);
      }
  }

  public function update(Request $request) 
  {  
    $user = Auth::user();

    if ($user != empty)
    {
      $validator = Validator::make(Purifier::clean($request->all());

      // update last order
      if ($request->input('quantity') != empty)
      {
        $quantityUpdate = $request->input('quantity'); 
        $order = Order::where("userId","=",$user->id)->first();

        $order->amount = $quantityUpdate;
      }

      if ($request->input('item') != empty)
      {
                
        $itemUpdate = $request->input('item'); 
        $order = Order::where("userId","=",$user->id)->first();

        $order->productsId = $itemUpdate;
      }

      order->save();
          
    }
  }

    // allow user to cancel current order
    public function userDestroy($id)
    {  

        $user = Auth::user();
      
        $order = Order::where("userId","=",$user->id)->where('id','=',$id)->first();
        $order->delete();


      
    }
  }

}
