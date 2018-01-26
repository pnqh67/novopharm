<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use forxer\Gravatar\Gravatar;

class ShippingFeeSubregion extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'shipping_fee_subregion';

  public function store($regionId, $data) {
//    $shippingFeeSubregion = ShippingFeeSubregion::where('subregion_id',$data['subregion_id'])
//        ->where('shipping_fee_region_id',$data[$regionId])
//        ->first();
//    if ($shippingFeeSubregion) return -1;
    $shippingFeeSubregion = new ShippingFeeSubregion;
    $shippingFeeSubregion->subregion_id = $data['subregion_id'];
    $shippingFeeSubregion->shipping_fee_region_id = $regionId;
    $shippingFeeSubregion->price = $data['price'];
    $shippingFeeSubregion->created_at = date('Y-m-d H:i:s');
    $shippingFeeSubregion->updated_at = date('Y-m-d H:i:s');
    if ($shippingFeeSubregion->save()) return $shippingFeeSubregion->id;
    return -3;
  }

  public function update($id, $regionId, $data) {
    $shippingFeeSubregion = ShippingFeeSubregion::find($id);
    if (!$shippingFeeSubregion) return -2;
    $shippingFeeSubregion->subregion_id = $data['subregion_id'];
    $shippingFeeSubregion->shipping_fee_region_id = $regionId;
    $shippingFeeSubregion->price = $data['price'];
    $shippingFeeSubregion->updated_at = date('Y-m-d H:i:s');
    if ($shippingFeeSubregion->save()) return $shippingFeeSubregion->id;
    return -3;
  }

  public function remove($id) {
    $shippingFeeSubregion = ShippingFeeSubregion::find($id);
    if(!$shippingFeeSubregion) return -2;
    if($shippingFeeSubregion->delete()) return $shippingFeeSubregion->id;
    return -3;
  }
}
