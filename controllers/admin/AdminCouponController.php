<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Coupon.php");
require_once(ROOT . '/controllers/helper.php');
use ControllerHelper as Helper;

class AdminCouponController extends AdminController {

  public function fetch(Request $request, Response $response) {
    Coupon::checkStatus();
    $coupon = Coupon::where('status', '!=', 'delete')->orderBy('id', 'desc')->get();
    return $this->view->render($response, 'admin/coupon',array(
        'coupon' => $coupon
    ));
  }

  public function create(Request $request, Response $response){
    return $this->view->render($response,'admin/coupon_create');
  }

  public function store(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $code = Coupon::store($body);
    if ($code) History::admin('create', 'coupon', $code, $body['title']);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function get(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $coupon = Coupon::find($id);
    return $this->view->render($response,'admin/coupon_edit',array(
       'data' => $coupon
    ));
  }

  public function update(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $body = $request->getParsedBody();
    $code = Coupon::update($id, $body);
    if (!$code) History::admin('update', 'coupon', $id, $body['title']);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function delete(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $coupon = Coupon::find($id);
    $code = Coupon::remove($id);
    if (!$code) History::admin('delete', 'coupon', $id, $coupon->title);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }
}

?>
