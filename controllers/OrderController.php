<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Order.php");
require_once("../models/Customer.php");
require_once("../models/Cart.php");
require_once("../models/Region.php");
require_once("../models/Product.php");
require_once(ROOT.'/framework/push-noti.php');
use GuzzleHttp\Client;
use ControllerHelper as Helper;


class OrderController extends Controller {

  public function order(Request $request, Response $response){
    $body = $request->getParsedBody();
    $customer = Customer::createOrUpdate($body);
    $code = Order::store($customer, $body, $body['total'], $body['total']);
    $code = Cart::store($code, $body['quantity']);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function get(Request $request, Response $response){
	  $cart['items'] = $_SESSION['cart'];
	  $cart['items_count'] = count($cart['items']);
	  $total = 0;
	  foreach ($cart['items'] as $key => $value) {
		  $variant = Variant::where('id', $value->variant_id)->first();
		  $product = Product::where('id', $value->product_id)->first();
		  $value->title = $product->title;
		  $value->variant = $variant->title;
		  $value->handle = $product->handle;
		  $value->price = $variant->price;
      $value->price_compare = $variant->price_compare;
		  $value->variant_id = $variant->id;
		  $value->image = $product->featured_image;
		  $value->subTotal = (int) $variant->price * (int) $value->quantity;
		  $total += $value->subTotal;
	  }
	  $cart['total_price'] = $total;
    return $this->view->render($response, 'cart', [
		  'cart' => $cart
	  ]);
  }

  public function addToCart(Request $request, Response $response) {
    $body = $request->getParsedBody();
//    $result = 0;
    if(isset($_SESSION["cart"]) && !empty($_SESSION["cart"])) {
      $cart = $_SESSION["cart"];
      $check_exists = false;
      foreach ($cart as $key => $item) {
        if($item->variant_id == $body['id']) {
//          if (!checkInventoryManagent($body['id'], $body['quantity'])) {
//            $variant = Variant::find($body['id']);
//            return $response->withJson([
//              "code" => -1,
//              "in_stock" => $variant->inventory,
//              "variant" => $variant->title
//            ]);
//          }
          $item->quantity += $body['quantity'];
          $check_exists = true;
        }
      }
      if(!$check_exists) {
//        if (!checkInventoryManagent($body['id'], $body['quantity'])) {
//          $variant = Variant::find($body['id']);
//          return $response->withJson([
//            "code" => -1,
//            "in_stock" => $variant->inventory,
//            "variant" => $variant->title
//          ]);
//        }
        $item = new stdClass();
        $item->variant_id = $body['id'];
        $item->quantity = $body['quantity'];
        array_push($cart, $item);
      }
    } else {
//      if (!checkInventoryManagent($body['id'], $body['quantity'])) {
//        $variant = Variant::find($body['id']);
//        return $response->withJson([
//          "code" => -1,
//          "in_stock" => $variant->inventory,
//          "variant" => $variant->title
//        ]);
//      }
      $cart = array();
      $item = new stdClass();
      $item->variant_id = $body['id'];
      $item->quantity = $body['quantity'];
      array_push($cart, $item);
    }
    $data = array();
    $total = 0;
    foreach ($cart as $key => $value) {
        $variant = Variant::find($value->variant_id);
        $product = Product::find($variant->product_id);
        $value->title = $product->title;
        $value->variant = $variant->title;
        $value->handle = $product->handle;
        $value->price = $variant->price;
        $value->product_id = $product->id;
        $value->image = $product->featured_image;
        $value->subTotal = (int) $variant->price * (int) $value->quantity;
        $value->in_stock = $variant->inventory;
        $total += $value->subTotal;
//        if ($value->variant_id == $body['id'])
//          $result = $value;
        array_push($data, $value);
    }
    $_SESSION["cart"] = $data;
    return $response->withJson([
      'code' => 0,
      'data' => $_SESSION["cart"],
      'total' => $total
    ]);
  }

  public function updateCart(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $note = $body['note']?$body['note']:'';
    if(isset($_SESSION["cart"]) && !empty($_SESSION["cart"])) {
      $cart = $_SESSION["cart"];
      $total = 0;
      foreach ($cart as $key => $item) {
        if($item->variant_id == $body['variant_id'] ) {
          $item->quantity = $body['quantity'];
        }
        $variant = Variant::where('id', $item->variant_id)->first();
        $total += (int) $variant->price * (int) $item->quantity;
      }
      $_SESSION["cart"] = $cart;
      return $response->withJson([
        "code" => 0,
        "data" => $_SESSION["cart"],
        "total" => $total
      ]);
    }
    return $response->withJson([
      "code" => -1,
      "message" => 'Not found'
    ]);
  }

  public function changeCart(Request $request, Response $response) {
    $body = $request->getParsedBody();
    if(isset($_SESSION["cart"]) && !empty($_SESSION["cart"])) {
      $cart = $_SESSION["cart"];
	    $total = 0;
	    foreach ($cart as $key => $item) {
        if($item->variant_id == $body['id'] ) {
          if ($body['quantity']){
            $item->quantity = $body['quantity'];
            $item->subTotal = $item->price*$item->quantity;
          }
          else{
            unset($cart[$key]);
            continue;
          }
        }
        $total += $item->price*$item->quantity;
      }
      $_SESSION["cart"] = $cart;
      return $response->withJson([
        "code" => 0,
        "data" => $_SESSION["cart"],
        "total" => $total
      ]);
    }
    return $response->withJson([
      "code" => -1,
      "message" => 'Not found'
    ]);
  }

  public function clearCart(Request $request, Response $response){
      if(isset($_SESSION["cart"]) && !empty($_SESSION["cart"])) {
        unset($_SESSION['cart']);
      }
      return $response->withJson([
        "code" => 0,
        'total' => 0
      ]);
  }

  public function getInfoCart(Request $request, Response $response) {
    $cart = $_SESSION['cart'];
    $total = 0;
    foreach ($cart as $item){
      $total += $item->subTotal;
    }
    return $response->withJson(array(
        'code' => 0,
        'data' => $_SESSION["cart"],
        'total' => $total
    ));
  }

  public function getProduct(Request $request, Response $response){
    $handle = $request->getAttribute('handle');
    $data = Product::where('handle',$handle)->first();
    $data->view+=1;
    $data->save();
    return $response->withJson(array(
        'code' => 0,
        'data' => $data
    ));
  }

  public function viewCart(Request $request, Response $response) {
    $cart = $_SESSION['cart'];
    $total = 0;
    foreach ($cart as $key => $value) {
      $variant = Variant::where('id', $value->variant_id)->first();
      $product = Product::where('id', $variant->product_id)->first();
      $value->title = $product->title;
      $value->variant = $variant->title;
      $value->handle = $product->handle;
      $value->price = $variant->price;
      $value->product_id = $variant->id;
      $value->image = $product->featured_image;
      $value->subTotal = (int) $variant->price * (int) $value->quantity;
      $total += $value->subTotal;
    }
    return $this->view->render($response, 'cart', [
      'cart' => $cart,
      'total' => $total
    ]);
  }

  public function addNote(Request $request, Response $response){
    $body = $request->getParsedBody();
	  $_SESSION['cart_note'] = $body['note'];
	  return $response->withJson(array(
		  'code' => 0,
		  'note' => $_SESSION["cart_note"]
	  ));
  }

  public function checkOut(Request $request, Response $response) {
    $cart = $_SESSION['cart'];
    $total = 0;
    foreach ($cart as $key => $value) {
      $variant = Variant::where('id', $value->variant_id)->first();
      $product = Product::where('id', $variant->product_id)->first();
      $value->title = $product->title;
      $value->variant = $variant->title;
      $value->handle = $product->handle;
      $value->price = $variant->price;
      $value->product_id = $variant->id;
      $value->image = $product->featured_image;
      $value->subTotal = (int) $variant->price * (int) $value->quantity;
      $total += $value->subTotal;
    }
    $region = Region::orderBy('name', 'asc')->get();
	  return $this->view->render($response, 'checkout', [
      'cart' => $cart,
      'total' => $total,
      'region' => $region
    ]);
  }

  public function orderSuccess(Request $request, Response $response) {
    if (isset($_SESSION['order_id']) || isset($_SESSION['order_id_dropship'])) {
      $arr_cart = array();
      $total = 0;
      if (isset($_SESSION['order_id'])) {
        $order = Order::find($_SESSION['order_id']);
        $customer = Customer::find($order->customer_id);
        $cart = Cart::where('order_id', $order->id)->get();
        foreach ($cart as $key => $value) {
          $variant = Variant::where('id', $value->variant_id)->first();
          $product = Product::where('id', $variant->product_id)->first();
          $value->title = $product->title;
          $value->variant = $variant->title;
          $value->handle = $product->handle;
          $value->price = $variant->price;
          $value->image = $product->featured_image;
          $value->subTotal = (int) $variant->price * (int) $value->quantity;
          $total += $value->subTotal;
          array_push($arr_cart, $value);
          unset($_SESSION['order_id']);
        }
      }
	    return $this->view->render($response, 'successful', [
        'customer' => $customer,
        'total' => $total,
        'cart' => $arr_cart
      ]);
    }
    return $response->withStatus(302)->withHeader('Location', '/');
  }

  public function store(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $name = $body['name'];
    $phone = $body['phone'];
    $region = $body['region'];
    $subregion = $body['subregion'];
    $address = $body['address'];
    $email = $body['email'];
    $shipping_price = $body['shipping_price'];
    $discount = $body['discount'];
    $payment_method = $body['payment_method'];

    $customer = array(
      "name" => $name,
      "email" => $email,
      "phone" => $phone,
      "address" => $address,
      "region" => $region,
      "subregion" => $subregion
    );
    $customer_id = Customer::createOrUpdate($customer);

    $details = array();
    $cart = $_SESSION['cart'];
    $subTotal = 0;
    foreach ($cart as $key => $value) {
      $item = new stdClass();
      $variant = Variant::find($value->variant_id);
      $product = Product::find($variant->product_id);
      $item->variant_id = $variant->id;
      $item->price = $variant->price;
      $item->quantity = $value->quantity;
      $subTotal += (int) $variant->price * (int) $value->quantity;
      array_push($details, $item);
    }

    $total = $subTotal + $shipping_price - $discount;
    $order_id = Order::store($customer_id, $body, $subTotal, $total);
    if ($order_id) {
      foreach ($details as $key => $value) {
        $value->order_id = $order_id;
        Cart::store($value);
        Product::updateSell($value->product_id, $value->quantity);
      }
      unset($_SESSION['cart']);
      $_SESSION['order_id'] = $order_id;
      return $response->withJson([
        'code' => 0,
        'message' => 'success'
      ]);
    }
    return $response->withJson([
      'code' => -1,
      'message' => 'error'
    ]);
  }
}

?>
