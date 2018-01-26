<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Slider.php");


class AdminSliderController extends AdminController {

	public function index(Request $request, Response $response) {
		$data = Slider::where('status','!=','delete')->get();
		return $this->view->render($response, 'admin/slider', array(
			'data' => $data
		));
	}

	public function store (Request $request, Response $response) {
		$body = $request->getParsedBody();
		$code = Slider::store($body);
		if ($code){
      return $response->withJson(array(
        'code' => 0,
        'message' => 'Success'
      ));
		}
		return $response->withJson(array(
			'code' => -3,
			'message' => 'Error'
		));
	}

	public function create(Request $request, Response $response)	{
		$data = Slider::where('status', '!=', 'delete')
			->where('parent_id','-1')->get();
    return $this->view->render($response, 'admin/slider_create', array(
    	'data' => $data
    ));
  }

	public function get(Request $request, Response $response)	{
		$id = $request->getAttribute('id');
		$data = Slider::find($id);
		$slider = Slider::where('status','!=', 'delete')
      ->where('parent_id','-1')->get();
    return $this->view->render($response, 'admin/slider_edit', array(
      'data' => $data,
	    'slider' => $slider
    ));
	}

	public function update (Request $request, Response $response) {
		$id = $request->getAttribute('id');
		$body = $request->getParsedBody();
    $code = Slider::update($body, $id);
		if($code) {
			return $response->withJson(array(
				'code' => 0,
				'message' => 'Updated'
			));
		}
		if ($code == -2) $response->withJson(array(
			'code' => -2,
			'message' => 'Not found'
		));
		return $response->withJson(array(
			'code' => -3,
			'message' => 'Server internal error'
		));
	}

	public function delete(Request $request, Response $response) {
    $id = $request->getAttribute('id');
		$Slider = Slider::find($id);
		if($Slider) {
			if($Slider->delete()) {
				return $response->withJson(array(
					'code' => 0,
					'message' => 'Deleted'
				));
			}
			return $response->withJson(array(
				'code' => -1,
				'message' => 'Error'
			));
		}
		return $response->withJson(array(
			'code' => -1,
			'message' => 'Unknown slider'
		));
	}
}

?>
