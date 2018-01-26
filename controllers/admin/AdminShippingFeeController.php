<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Region.php");
require_once("../models/SubRegion.php");
require_once("../models/ShippingFeeRegion.php");
require_once("../models/ShippingFeeSubregion.php");
require_once(ROOT . '/controllers/helper.php');
use ControllerHelper as Helper;

class AdminShippingFeeController extends AdminController {

  public function fetch(Request $request, Response $response) {
    $data = ShippingFeeRegion::join('region','shipping_fee_region.region_id','region.id')
        ->orderBy('region.id')
        ->get();
    return $this->view->render($response, 'admin/shipping_fee',array(
        'data'=> $data
    ));
  }

  public function create(Request $request, Response $response) {
    $region = Region::all();
    return $this->view->render($response, 'admin/shipping_fee_create',array(
        'regions' =>$region
    ));
  }

  public function loaddata(Request $request, Response $response){
      $id = $request->getAttribute('id');
      $data = ShippingFeeRegion::find($id);
      $subRegion = ShippingFeeSubregion::where('shipping_fee_region_id',$id)->get();
      return $response->withJson(['data'=> $data,'subregion'=>$subRegion]);
  }

  public function shippingfee(Request $request, Response $response){
      $id = $request->getAttribute('id');
      $data = ShippingFeeRegion::where('region_id', $id)->get();
      $this->view->render($response,'admin/snippet/box-shipping-fee',array(
         'shippingfee' => $data
      ));
  }

  public function edit(Request $request, Response $response) {
    $region = Region::all();
    $regionId = $request->getAttribute('regionid');
    $shippingFee = ShippingFeeRegion::where('region_id', $regionId)->get();
    $subregion = SubRegion::where('region_id', $regionId)->get();
    return $this->view->render($response, 'admin/shipping_fee_edit',array(
        'regionid' => $regionId,
        'subregion' => $subregion,
        'regions' =>$region,
        'shippingfee' =>$shippingFee
    ));
  }

  public function store(Request $request, Response $response) {
    $data = $request->getParsedBody();
    $code = ShippingFeeRegion::store($data['data']);
    if($code>0){
        foreach ($data['subRegion'] as $item){
            ShippingFeeSubregion::store($code,$item);
        }
    }
    $result = Helper::response($code);
    return $response->withJson($result,200);
  }

  public function update(Request $request, Response $response) {
    $data = $request->getParsedBody();
    $idShippingFee = $request->getAttribute('id');
    $code = ShippingFeeRegion::update($idShippingFee,$data['data']);
    if ($code > 0){
        $subRegion = ShippingFeeSubregion::where('shipping_fee_region_id',$code)->get();
        foreach ($subRegion as $item){
            ShippingFeeSubregion::remove($item['id']);
        }
        foreach ($data['subRegion'] as $item){
            ShippingFeeSubregion::store($code,$item);
        }
    }
    $result = Helper::response($code);
    return $response->withJson($result,200);
  }

  public function delete(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $code = ShippingFeeRegion::remove($id);
    if ($code > 0){
        $subRegion = ShippingFeeSubregion::where('shipping_fee_region_id',$code)->get();
        foreach ($subRegion as $item){
            ShippingFeeSubregion::remove($item['id']);
        }
    }
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }
}

?>
