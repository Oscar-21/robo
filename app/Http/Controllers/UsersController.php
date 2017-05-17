<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Order;
use App\Product;
use Response;
use Illuminate\Support\Facades\Validator;
use Purifier;
use Hash;
use JWTAuth;
use Role;
use Auth;

class UsersController extends Controller 
{
  public function __construct() 
  {
    $this->middleware("jwt.auth", ["only"=>["deleteUser","show","index","updateAddress", "adminShowUser","userShow"]]);
  }

  public function signUp(Request $request) 
  {
    $validator = Validator::make(Purifier::clean($request->all()), [
      'username' => 'required',
      'password' => 'required',
      'email' => 'required',
    ]);

    if ($validator->fails()) 
    {
      return Response::json(["error" => "You must eneter a username, email and password."]);
    }

    $check = User::where("email","=",$request->input("email"))->orWhere("name","=",$request->input("username"))->first();

    if (!empty($check)) 
    {
      return Response::json(["error" => "Email or username alrready in use."]);
    }

    $check = User::where("id","=",1)->first();
    $checks = $request->input('address');

    if (empty($check) )
    {
      $user = new User;
      $user->name = "Admin";
      $user->password = Hash::make("password");
      $user->email = "admin@mail.com";
      $user->roleID = 1;

      $user->save();
      return Response::json(["success" => "Admin created successfully"]);
    }

    $user = new User;
    $user->name = $request->input('username');
    $user->password = Hash::make($request->input('password'));
    $user->email = $request->input('email');
    $user->roleID = 2;

    if(!empty($checks))
    {
      $user->address = $checks;
    }
    /*$user->roleID = Role::where("name","=",customer->get(id);*/

    $user->save();
    return Response::json(["success" => "User created successfully"]);
  }

  
  public function signIn(Request $request) 
  {
    $validator = Validator::make(Purifier::clean($request->all()), [
      'email' => 'required',
      'password' => 'required'
    ]);

    if ($validator->fails()) 
    {
      return Response::json(["error" => "You must enter an email and a password."]);
    }
      $email = $request->input('email');
      $password = $request->input('password');
      $cred = compact("email","password",["email","password"]);
      $token = JWTAuth::attempt($cred);
      return Response::json(compact("token"));
  }

  # ADMIN: delelte specific user
  public function deleteUser($id)
  {
    $admin = Auth::id();

    if ($admin == 1)
    {
      $user = User::find($id);
      $user->delete();

      return Response::json(["success" => "Deleted User"]);
    }
    else
    {
      return Response::json(["error" => "Invalid credentials"]);
    }
  }
  
  # USER: see email/username/address/orders
  public function userShow()
  {
    $user = Auth::user(); 

    if ($user->roleID == 2)
    {
      $show = User::where("id","=",$user->id)->select("email","name","address")->first();
      
      
      $orders = Order::where("userId","=",$user->id)->select("productsId")->get();
  
      $userInfo = [$show];
      $userOrders = [];
      $length = count($orders);

      for ( $i = 0; $i < $length; $i++)
      {

        $check = $orders[$i]['productsId'];
        
        $userOrders[$i] = Product::where("categoryId","=",$check)->select("name")->first();
      }

      $userAllInfo = (object) array_merge((array) $userInfo, (array) $userOrders);
      return Response::json($userAllInfo);
    }
  }

  # ADMIN: show specific user
  public function adminShowUser($id)
  {
    $admin = Auth::id();

    if ($admin == 1)
    {
      $user = User::where("id","=",$id)->first();

      if (!empty($user))
      {
        return Response::json($user);
      }
      else
      {
        return Response::json(["error" => "No user exists with this id!"]);
      }
    }
    else
    {
      return Response::json(["error" => "invalid credentials"]);
    }
  }

  
  # USER update address
  public function updateAddress(Request $request)
  {
   $id = Auth::id();
 
   $user = User::where("id","=",$id)->first();
   $check = $user->roleID;
   $address = $user->Address;

    if ($check == 2 )
    {
      $user->address = $request->input("address");
      $user->save();
      return Response::json(["success" => "Address updated"]);
    }
  }

  # ADMIN show all users
  public function index()
  {
    $admin = Auth::id();

    if ($admin == 1)
    {
      $user = User::all();
      return Response::json($user);
    }
    else
    {
      return Response::json(["error" => "Invalid Credentials"]);
    }

  }
}
