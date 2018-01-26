<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once("../models/Location.php");
require_once("../models/Region.php");
require_once("../models/SubRegion.php");

class LocationController extends Controller
{
  public function get(Request $request, Response $response)  {
    $params = $request->getQueryParams();
    $subregionId = $params['subregion'];
    $locations = Location::where('status', 'active')
      ->where('subregion_id', $subregionId)
      ->get();
    if (!$locations){
      $this->view->render($response, '404');
      return $response->withStatus(404);
    }
    $region = Region::find(SubRegion::find($subregionId)->region_id)->name;
    return $this->view->render($response, 'location_detail', array(
      'region' => $region,
      'locations' => $locations
    ));
  }
  public function fetch(Request $request, Response $response) {
    $arrRegionId = Location::distinct()->where('status', 'active')->pluck('region_id');
    $listRegionTemp = Region::whereIn('id', $arrRegionId)->get();
    $north = [];
    $mid = [];
    $south = [];
    foreach ($listRegionTemp as $item){
      if ($item->zone_id == 1) array_push($north,$item);
      if ($item->zone_id == 2) array_push($mid,$item);
      if ($item->zone_id == 3) array_push($south,$item);
    }
    $listRegion = [];
    if ($north) $listRegion['north'] = $north;
    if ($mid) $listRegion['mid'] = $mid;
    if ($south) $listRegion['south'] = $south;
    $arrSubregionIdFirst = Location::distinct()->where('status', 'active')
      ->where('region_id', $arrRegionId[0])
      ->pluck('subregion_id');
    $listSubregionFirst = SubRegion::whereIn('id', $arrSubregionIdFirst)->get();
    return $this->view->render($response, 'location', array(
      'regions' => $listRegion,
      'subregionsFirst' => $listSubregionFirst
    ));
  }

  public function getSubregion(Request $request, Response $response) {
    $regionId = $request->getAttribute('region_id');
    $arrSubregionId = Location::distinct()
      ->where('status', 'active')
      ->where('region_id', $regionId)
      ->pluck('subregion_id');
    $listSubregion = SubRegion::whereIn('id', $arrSubregionId)->get();
    return $response->withJson(array(
      'code' => 0,
      'data' => $listSubregion
    ));
  }

}

?>
