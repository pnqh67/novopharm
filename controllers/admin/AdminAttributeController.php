<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Attribute.php");
require_once("../models/ProductAttribute.php");
require_once(ROOT . '/controllers/helper.php');
use ControllerHelper as Helper;

class AdminAttributeController extends AdminController {

	public function index(Request $request, Response $response) {
		$data = Attribute::fetch();
		return $this->view->render($response, 'admin/attribute', [
			'data' => $data
		]);
	}

	public function get(Request $request, Response $response) {
		$id = $request->getAttribute('id');
		$code = Attribute::find($id);
		if ($code == -2) $result = Helper::response($code);
		else $result = Helper::responseData($code);
		return $response->withJson($result, 200);
	}

	public function store (Request $request, Response $response) {
		$body = $request->getParsedBody();
		$code = Attribute::store($body);
		$result = Helper::response($code);
		return $response->withJson($result, 200);
	}

	public function update (Request $request, Response $response) {
		$id = $request->getAttribute('id');
		$body = $request->getParsedBody();
		$code = Attribute::update($id, $body);
		$result = Helper::response($code);
		return $response->withJson($result, 200);
	}

	public function delete(Request $request, Response $response) {
		$id = $request->getAttribute('id');
		$code = Attribute::remove($id);
		if (!$code) {
			Attribute::where('parent_id', $id)->delete();
      ProductAttribute::where('attribute_id', $id)->delete();
		}
		$result = Helper::response($code);
		return $response->withJson($result, 200);
	}

	public function getChildAttribute(Request $request, Response $response){
	  $params = $request->getQueryParams();
	  if ($params['parent_id'] == -1) return -1;
	  $attributes = Attribute::where('parent_id',$params['parent_id'])->get();
	  if ($attributes) return $response->withJson(array(
	    'code' => 0,
      'data' => $attributes
    ));
  }
}
