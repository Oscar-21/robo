<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Response;
use Illuminate\Support\Facades\Validator;
use Purifier;
use Hash;
use JWTAuth;
use File;

class UsersController extends Controller
{
    public function signUp(Request $request) {
      $validator = Validator::make(Purifier::clean($request->all()), [
        'username' => 'required',
        'password' => 'required',
        'email' => 'required',
      ]);

      if ($validator->fails()) {
        return Response::json(["error" => "You must eneter a username, email and password."])
      }

      $check = User::where("email", "=",$request->input("email"))->orWhere("name","=",$request)->input("username"))->first();

      if (!empty($check)) {
        return Response::json(["error" => "Email or username alrready in use."]);
      }

      $user = new User;
      $user->name = $request->input('username');
      $user->password = Hash::make($request->input('password'));
      $user->email = $request->input('email');

      $user->save();
      return Response::json(["success" => Account created successfully]);
    }

    public function signIn(Request $request) {
      $validator = Validator::make(Purifier::clean($request->all()), [
        'email' => 'required'
        'password' => 'required'
      ]);

      if ($validator->fails()) {
        return Response::json(["error" => "You must enter an email and a password."]);
      }

      $email = $request->input('email');
      $password = $request->input('password');
      $cred = compact("email","password",["email","password"]);
      $token = JWTAuth::attempt($cred);
      return Response::json(compact("token"));
    }
}
