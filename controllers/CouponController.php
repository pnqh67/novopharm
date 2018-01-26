<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/helper.php");
require_once("../models/Coupon.php");

class CouponController extends Controller {

  public function check (Request $request, Response $response) {
    $body = $request->getParsedBody();
    $check = Coupon::checkValidCoupon($body['total'], $body['code']);
    if ($check == -1) {
      $mess = "Coupon không tồn tại";
      if ($_SESSION["lang"] == 'en') $mess = 'Coupon not exist';
      return $response->withJson([
        'code' => -1,
        'message' => $mess
      ]);
    }
    if ($check == -2) {
      $mess = "Coupon đã hết số lần sử dụng";
      if ($_SESSION["lang"] == 'en') $mess = 'This coupon out of usage';
      return $response->withJson([
        'code' => -2,
        'message' => $mess
      ]);
    }
    if ($check == -3) {
      $coupon = Coupon::where('code', $body['code'])->where('status', 'active')->first();
      $mess = "Giá trị đơn hàng nhỏ hơn ". money($coupon->min_value_order);
      if ($_SESSION["lang"] == 'en') $mess = 'Order value is smaller '. money($coupon->min_value_order);
      return $response->withJson([
        'code' => -3,
        'message' => $mess
      ]);
    }
    if ($check == -4) {
      $coupon = Coupon::where('code', $body['code'])->where('status', 'active')->first();
      $mess = "Coupon chưa hiệu sử dụng, có hiệu lực từ Oh ngày " . ymd2dmy($coupon->start_date);
      if ($_SESSION["lang"] == 'en') $mess = 'This coupon available from Oh' . ymd2dmy($coupon->start_date);
      return $response->withJson([
        'code' => -4,
        'message' => $mess
      ]);
    }
    if ($check == -5) {
      $mess = "Coupon đã hết hạn sử dụng";
      if ($_SESSION["lang"] == 'en') $mess = 'This coupon out of date';
      return $response->withJson([
        'code' => -5,
        'message' => $mess
      ]);
    }
    $value = Coupon::getCouponDiscount($body['total'], $body['code']);
    $mess = "Áp dụng thành công";
    if ($_SESSION["lang"] == 'en') $mess = 'Success';
    return $response->withJson([
      'code' => 0,
      'value' => $value,
      'coupon_id' => $check->id,
      'message' => $mess
    ]);
  }

}

?>
