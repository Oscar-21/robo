<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use Response;
use Illuminate\Support\Facades\Validator;
use Purifier;
use JWTAuth;

class OrdersController extends Controller {

  public function __construct() {
    $this->middleware("jwt.auth", ["only"=>["store","update","destroy"]]);
  }

  public function index() {
    $orders = Order::find($id);
    return Response::json($orders);
  }
  public function store(Request $request) {
    $orders()
  }

}
