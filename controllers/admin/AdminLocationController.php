<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Location.php");
require_once("../models/Region.php");
require_once("../models/SubRegion.php");
require_once(ROOT . '/controllers/helper.php');
use ControllerHelper as Helper;

class AdminLocationController extends AdminController {

  public function index(Request $request, Response $response) {
    $region = Region::all();
    $subregion = SubRegion::where('region_id',$region[0]['id'])->get();
    $location = Location::where('status', '!=', 'delete')
      ->join('region', 'location.region_id', '=', 'region.id')
      ->join('subregion', 'location.subregion_id', '=', 'subregion.id')
      ->select('location.*', 'region.name as name_region', 'subregion.name as name_subregion')
      ->orderBy('location.region_id', 'asc')
      ->get();
    return $this->view->render($response,'admin/location', array(
      'data' => $location,
      'region' => $region,
      'subregion' => $subregion
    ));
  }

  public function getSubregion(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $data = SubRegion::where('region_id', $id)->get();
    if ($data){
      return $response->withJson([
        'code' => 0,
        'data' => $data
      ]);
    }
  }

  public function get(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $data = Location::find($id);
    $subRegion = SubRegion::where('region_id', $data['region_id'])->get();
    return $response->withJson([
      'code' => 0,
      'data' => $data,
      'subregion' => $subRegion
    ]);
  }

  public function fetch(Request $request, Response $response) {
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
    $region_id = $body['region_id'];
    $subregion_id = $body['subregion_id'];
    foreach ($body['listLocation'] as $item){
      $code = Location::store($region_id, $subregion_id, $item);
    }
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function update(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $body = $request->getParsedBody();
    $code = Location::update($id, $body);
    if (!$code) History::admin('update', 'location', $id, $body['title']);
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
