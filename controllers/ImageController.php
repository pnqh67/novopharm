<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Image.php");

class ImageController extends Controller {

  public function get(Request $request, Response $response) {
    $params = $request->getQueryParams();
	  if ($params['type'] && $params['type_id']){
      $images = Image::getImage($params['type'],$params['type_id']);
      foreach ($images as $image){
	      $image['url'] = '/uploads/' . $image['name'];
      }
      return $response->withJson(array(
	      "code" => 0,
	      "message" => "success",
	      "images" => $images
      ));
    }
	  $images = Image::all();
	  foreach ($images as $image){
		  $image['url'] = '/uploads/' . $image['name'];
	  }
	  return $response->withJson(array(
		  "code" => 0,
		  "message" => "success",
		  "images" => $images
	  ));
  }
}

?>
