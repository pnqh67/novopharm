<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Customer.php");
require_once("../models/Order.php");
require_once("../models/Product.php");
require_once("../models/Variant.php");
require_once('helper.php');

class CustomerController extends Controller {

  public function fetch(Request $request, Response $response){
	  $customers = Customer::all();
	  foreach ($customers as $customer){
	    $customer['orders'] = Order::orderBy('updated_at','DESC')
        ->where('customer_id',$customer['id'])
        ->get();
	    $customer['orders_count'] = count($customer['orders']);
	    $customer['last_order'] = $customer['orders'][0];
	    $customer['total_spent'] = Order::where('customer_id',$customer['id'])->groupBy('customer_id')->sum('total');
    }
	  return $response->withJson([
		  "code" => 0,
		  "message" => "success",
		  "customers" => $customers
    ]);
  }

	public function get(Request $request, Response $response){
  	if (!$_SESSION['login_user']){
  		return $response->withRedirect($_SESSION['href_user']);
	  }
	  $customer = Customer::where('id',$_SESSION['id_user'])
      ->select('id', 'name', 'email', 'phone', 'address', 'region', 'subregion')
      ->first();
  	return $this->view->render($response,'customer',array(
  		'customer' => $customer
	  ));
  }

  public function order(Request $request, Response $response){
    if (!$_SESSION['login_user']) {
      return $response->withRedirect('/');;
    }
    $orders = Order::where('customer_id', $_SESSION['id_user'])->get();
    return $this->view->render($response, 'customer_order', array(
      'orders' => $orders
    ));
  }

  public function favorite(Request $request, Response $response){
    if (!$_SESSION['login_user']) {
      return $response->withRedirect($_SESSION['href_user']);
    }
    $customer = Customer::where('id', $_SESSION['id_user'])->first();
    $favorite_product = json_decode($customer->favorite_product);
    $favorite = [];
    foreach ($favorite_product as $item){
      $product = Product::where('status','active')
        ->where('id', $item + 0)
        ->first();
      if ($product){
        $rel_prod_variant = Variant::where('product_id', $product->id)->first();
        $product->display_discount = 0;
        if($rel_prod_variant->price_compare && ($rel_prod_variant->price_compare > $rel_prod_variant->price)) {
          $product->percent = 0;
          $discount = $rel_prod_variant->price_compare - $rel_prod_variant->price;
          $product->variant_id = $rel_prod_variant->id;
          $product->percent = ($discount / $rel_prod_variant->price_compare) * 100;
          $product->percent = round($product->percent, 0) .'%';
          $product->price = $rel_prod_variant->price;
          $product->price_compare = $rel_prod_variant->price_compare;
          $product->display_discount = 1;
        }
        else{
          $product->variant_id = $rel_prod_variant->id;
          $product->price = $rel_prod_variant->price;
          $product->price_compare = $rel_prod_variant->price_compare;
        }
        array_push($favorite,$product);
      }
    }
    return $this->view->render($response, 'customer_wishlist', array(
      'favorite' => $favorite
    ));
  }

  public function favoriteStore(Request $request, Response $response){
    if (!$_SESSION['login_user']) {
      return $response->withRedirect($_SESSION['href_user']);
    }
    $body = $request->getParsedBody();
    $customer = Customer::where('id', $_SESSION['id_user'])->first(); //$_SESSION['id_user']
    if($customer){
      if ($customer->favorite_product){
        $temp = json_decode($customer->favorite_product);
        $status = 0;
        foreach ($temp as $item){
          if (($item + 0) == $body['product_id']) $status = 1;
        }
        if ($status == 0) array_push($temp, $body['product_id']);
      }
      else{
        $temp = array($body['product_id']);
      }
      $customer->favorite_product = json_encode($temp);
      if ($customer->save()) return $response->withJson([
        'code' => 0,
        'message' => 'Thành công'
      ]);
    }
    return $response->withJson([
      'code' => -4,
      'message' => 'Có lỗi xảy ra, vui lòng thử lại'
    ]);
  }

  public function favoriteDelete(Request $request, Response $response){
    if (!$_SESSION['login_user']) {
      return $response->withRedirect($_SESSION['href_user']);;
    }
    $body = $request->getParsedBody();
    $customer = Customer::where('id', $_SESSION['id_user'])->first();
    if($customer){
      if ($customer->favorite_product) {
        $temp = json_decode($customer->favorite_product);
        foreach ($temp as $key => $item) {
          if (($item + 0) == $body['product_id']) unset($temp[$key]);
        }
        $new_array = array();
        foreach((array) $temp as $value) { $new_array[] = $value; }
        $customer->favorite_product = json_encode($new_array);
        if ($customer->save()) return $response->withJson([
          'code' => 0,
          'message' => 'Thành công'
        ]);
      }
    }
    return $response->withJson([
      'code' => -4,
      'message' => 'Có lỗi xảy ra, vui lòng thử lại'
    ]);
  }

  public function signup(Request $request, Response $response) {
  	$body = $request->getParsedBody();
  	if (!$body['name'] || !$body['email'] || !$body['password']){
  	  return $response->withJson([
  	    'code' => -4,
        'message' => 'Trường bắt buộc không được rỗng'
      ]);
    }
    if ($body['password'] != $body['passwordconfirm']){
  	  return $response->withJson([
  	    'code' => -3,
        'message' => 'Mật khẩu xác nhận không đúng'
      ]);
    }
    if (!$body['agree']){
      return $response->withJson([
        'code' => -3,
        'message' => 'Bạn chưa đồng ý điều khoản'
      ]);
    }
  	$item = Customer::where('email', $body['email'])->first();
  	if ($item){
		  return $response->withJson([
		    'code'=> 0,
        'mesage' => 'Email đã tồn tại'
      ]);
	  }
	  Customer::store($body);
    return $response->withRedirect($_SESSION['href_user']);
  }

  public function signin(Request $request, Response $response) {
  	if($_SESSION['login_user']){
  		return $response->withRedirect($_SESSION['href_user']);;
	  }
    $body = $request->getParsedBody();
    $email = $body['email'];
    $password = $body['password'];
    if (!$email || !$password) {
      return $response->withJson([
        'code' => -4,
        'message' => 'Email không được rỗng'
      ]);
    }
    $customer = Customer::where('email', $email)->first();
    if($customer) {
      if(password_verify($password, $customer->password)) {
        $_SESSION['login_user'] = true;
        $_SESSION['id_user'] = $customer->id;
        $_SESSION['email_user'] = $customer->email;
        $_SESSION['name_user'] = $customer->name;
        $href = '/';
        if($_SESSION['href']) $href = $_SESSION['href'];
        return $response->withRedirect($_SESSION['href_user']);
      }
      return $response->withJson([
        'code' => -4,
        'message' => 'Mật khẩu không chính xác'
      ]);
    }
    return $response->withJson([
      'code' => -4,
      'message' => 'Email không tồn tại'
    ]);
  }

  public function changePassword(Request $request, Response $response){
    if (!$_SESSION['login_user']) {
      return $response->withRedirect($_SESSION['href_user']);;
    }
    $body = $request->getParsedBody();
    if ($body['new_password'] != $body['re_new_password']){
      return $response->withJson([
        'code' => -3,
        'message' => 'Xác nhận mật khẩu không đúng'
      ]);
    }
    if (!$body['new_password'] || !$body['old_password'] || !$body['re_new_password']){
      return $response->withJson([
        'code' => -3,
        'message' => 'Không được để trống'
      ]);
    }
    $customer = Customer::where('email', $_SESSION['email_user'])->first();
    if (password_verify($body['old_password'], $customer->password)) {
      $customer->password = password_hash($body['new_password'],PASSWORD_DEFAULT);
      $customer->save();
      unset($_SESSION['login_user']);
      unset($_SESSION['id_user']);
      unset($_SESSION['email_user']);
      unset($_SESSION['name_user']);
      return $response->withRedirect($_SESSION['href_user']);
    }
    return $response->withJson([
      'code' => -4,
      'mesage' => 'Mật khẩu không đúng'
    ]);
  }

  public function changeForgotPassword(Request $request, Response $response){
    $body = $request->getParsedBody();
    if ($body['new_password'] != $body['re_new_password']){
      return $response->withJson([
        'code' => -3,
        'message' => 'Xác nhận mật khẩu không đúng'
      ]);
    }
    $customer = Customer::where('random',$body['random'])->first();
    if ($customer){
      $customer->password = password_hash($body['new_password'],PASSWORD_DEFAULT);
      if ($customer->save()){
        unset($_SESSION['login_user']);
        unset($_SESSION['id_user']);
        unset($_SESSION['email_user']);
        unset($_SESSION['name_user']);
        return $response->withRedirect($_SESSION['href_user']);
      }
    }
    return $response->withJson([
      'code' => -2,
      'mesage' => 'User không tồn tại'
    ]);
  }

  public function changeInformation(Request $request, Response $response){
    $body = $request->getParsedBody();
    if (!$body['name']){
      return $response->withJson([
        'code' => -4,
        'message' => 'Trường bắt buộc không được rỗng'
      ]);
    }
    $item = Customer::where('email', $_SESSION['email_user'])->first();
    if ($item){
      Customer::update($body, $_SESSION['email_user']);
      return $response->withRedirect('/tai-khoan');
    }
  }

  public function forgotPassword(Request $request, Response $response){
    $body = $request->getParsedBody();
    $email = $body['email'];
    $customer = Customer::where('email', $email)->first();
    if ($customer){
      $to = $customer['email'];
      $subject = 'THÔNG TIN TÀI KHOẢN NGƯỜI DÙNG';
      $variables = array();
      $variables['website'] = 'EYE SOLUTION';
      $variables['user_name'] = $customer->name;
      $variables['user_email'] = $customer->email;
      $variables['link_create_password'] = HOST . '/customer/' . $customer->random;
      $template = file_get_contents(ROOT . '/framework/mail-template/forget-password-user.html');
      foreach ($variables as $key => $value) {
        $template = str_replace('{{'.$key.'}}', $value, $template);
      }
      PHPMailer($to, $subject, $template);
      return $response->withRedirect('/');
    }
    return $response->withJson([
      'code' => -2,
      'message' => 'Email không tồn tại'
    ]);
  }

  public function createPassword(Request $request, Response $response){
    $random = $request->getAttribute('random');
    $check = Customer::where('random', $random)->first();
    if (!$check) {
      $this->view->render($response, '404');
      return $response->withStatus(404);
    }
    return $this->view->render($response, 'create_password', [
      'random' => $random
    ]);
  }

  public function signout(Request $request, Response $response){
		if (!$_SESSION['login_user']){
      return $response->withRedirect($_SESSION['href_user']);
    }
    unset($_SESSION['login_user']);
		unset($_SESSION['id_user']);
		unset($_SESSION['email_user']);
		unset($_SESSION['name_user']);
    return $response->withRedirect($_SESSION['href_user']);
  }
}

?>
