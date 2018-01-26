<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Order.php");
require_once("../models/Customer.php");
require_once("../models/Cart.php");
require_once(ROOT . '/models/Product.php');
require_once(ROOT . '/controllers/helper.php');
use ControllerHelper as Helper;


class AdminOrderController extends AdminController {

  public function index(Request $request, Response $response) {
    $params = $request->getQueryParams();
    $order_status = $params['order_status'];
    $payment_status = $params['payment_status'];
    $menu_child = 'all';
    $statistic = [];

    if (isset($payment_status) && $payment_status == 1) $menu_child = 'paid';
    $query = Order::where('status','!=','delete')->join('customer', 'customer.id', '=', 'order.customer_id');

    if (isset($order_status)) {
      $menu_child = $order_status;
      $query = $query->where('order.order_status', $order_status);
      if (isset($payment_status)) {
        $query = $query->where('order.payment_status', $payment_status);
        if ($payment_status == 0) $menu_child = 'unpaid';
        else $menu_child = 'paid';
      }
    }

    $statistic['totalSum'] = $query->sum('order.total');
    $statistic['orderCount'] = $query->count();

    $data = $query->select(
      'order.id as id',
      'order.created_at as created_at',
      'customer.name as name',
      'order.total as total',
      'order.order_status as order_status',
      'order.status as status'
    )->orderBy('id', 'desc')->get();

    return $this->view->render($response, 'admin/order', array(
      'data' => $data,
      'menu_child' => $menu_child,
      'statistic' => $statistic
    ));
  }

  public function search(Request $request, Response $response) {
    return $this->view->render($response, 'admin/order_search');
  }

  public function show (Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $order = Order::find($id);

    if (!$order) return $response->withStatus(302)->withHeader('Location', '/404');

    $cart = Cart::where('order_id', $id)->get();
    foreach ($cart as $key => $value) {
      $variant_id = $value->variant_id;
      $variant = Variant::find($variant_id);
      $product = Product::find($variant->product_id);
      $value->id = $product->id;
      $value->image = $product->featured_image;
      $value->title = $product->title;
      $value->variant_title = $variant->title;
    }

    $customer = Customer::find($order->customer_id);

    return $this->view->render($response, 'admin/order_edit', array(
      'order' => $order,
      'cart' => $cart,
      'customer' => $customer
    ));
  }

  public function update (Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $body = $request->getParsedBody();
		$code = Order::update($id, $body);
		if (!$code) History::admin('update', 'order', $id);
		$result = Helper::response($code);
		return $response->withJson($result, 200);
  }
}
?>
