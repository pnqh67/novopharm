<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use forxer\Gravatar\Gravatar;

class ShippingFeeRegion extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'shipping_fee_region';

  public function store($data) {
    $shipping_fee = ShippingFeeRegion::where('title',$data['title'])
        ->where('region_id',$data['region_id'])
        ->first();
    if ($shipping_fee) return -1;
    $shipping_fee = new ShippingFeeRegion;
    $shipping_fee->title = $data['title'];
    $shipping_fee->type = $data['type'];
    $shipping_fee->from = $data['from'];
    $shipping_fee->to = $data['to'];
    $shipping_fee->price = $data['price'];
    $shipping_fee->region_id = $data['region_id'];
    $shipping_fee->created_at = date('Y-m-d H:i:s');
    $shipping_fee->updated_at = date('Y-m-d H:i:s');
    if ($shipping_fee->save()) return $shipping_fee->id;
    return -3;
  }

  public function update($id, $data) {
    $shipping_fee = ShippingFeeRegion::find($id);
    if (!$shipping_fee) return -1;
    $shipping_fee->title = $data['title'];
    $shipping_fee->type = $data['type'];
    $shipping_fee->from = $data['from'];
    $shipping_fee->to = $data['to'];
    $shipping_fee->price = $data['price'];
    $shipping_fee->updated_at = date('Y-m-d H:i:s');
    if ($shipping_fee->save()) return $shipping_fee->id;
    return -3;
  }

  public function remove($id) {
    $shipping_fee = ShippingFeeRegion::find($id);
    if(!$shipping_fee) return -2;
    if($shipping_fee->delete()) return $shipping_fee->id;
    return -3;
  }
}
