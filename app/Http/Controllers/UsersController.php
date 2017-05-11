<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
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
    $this->middleware("jwt.auth", ["only"=>["destroy","show",]]);
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

    $check = User::where("id","=",1)->get();

    if (empty($check))
    {
      $user = new User;
      $user->name = "Admin";
      $user->password = Hash::make("password");
      $user->email = "admin@mail.com";
      $user->roleID = 1;
    }

    $user = new User;
    $user->name = $request->input('username');
    $user->password = Hash::make($request->input('password'));
    $user->email = $request->input('email');
    $user->roleID = 2;
    /*$user->roleID = Role::where("name","=",customer->get(id);*/

    $user->save();
    return Response::json(["success" => "Account created successfully"]);
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

  # let admin delelte specific user
  public function deleteUser($id)
  {
    $admin = Auth::id();

    if ($admin == 1)
    {
      $user = User::find($id);
      $user->delete();
    }
  }
  
  # let admin see all users
  public function show()
  {
    $admin = Auth::id();

    if ($admin == 1)
    {
      $user = User::all();
      return Response::json($user);
    }
  }

  
  # let admin see specific user
  public function showUser($id)
  {
    $admin = Auth::id();

    if ($admin == 1)
    {
      $user = User::where("id","=",$id);
      return Response::json($user);
    }
  }

  # allow user to update address
  public function updateAdress(Request $request)
  {
    $user = Auth::id();
    
    if ($user == 2)
    {
      $user = new User;
    }
  }


}
