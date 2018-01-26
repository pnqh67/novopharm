<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Meta.php");
require_once("../models/SubRegion.php");
require_once("../models/Product.php");
require_once("../models/Article.php");

class AdminSettingController extends AdminController {

  public function setting(Request $request, Response $response) {
    $products = Product::where('status','active')->get();
    return $this->view->render($response, 'admin/setting', array(
      'products' => $products
    ));
  }

	public function getSetting(Request $request, Response $response) {
    $metas = Meta::all();
		return $response->withJson(array(
		  'code' => 0,
      'datas' => $metas
    ));
	}

	public function getList(Request $request, Response $response){
    $params = $request->getQueryParams();
    if($params['type']=='product'){
      $data = Product::where('status','active')->get();
    }
    if ($params['type'] == 'article'){
      $data = Article::where('status','active')->get();
    }
    if ($params['type'] == 'collection'){
      $data = Collection::all();
    }
    if ($params['type'] == 'blog'){
      $data = Blog::all();
    }
    if ($params['type'] == 'page'){
      $data = Page::where('status', 'active')->get();
    }
    return $response->withJson(array(
      'code' => 0,
      'data' => $data
    ));
  }

  public function updateSetting(Request $request, Response $response) {
    $body = $request->getParsedBody();
    foreach ($body as $key=>$value){
      if (is_array($value)){
        $temp = json_encode($value);
        Meta::store($key,$temp);
        continue;
      }
      Meta::store($key,$value);
    }
    return $response->withJson(array(
      'code' => 0,
      'message' => 'Updated'
    ));
  }

  public function getImages(Request $request, Response $response) {
    $dir = ROOT . '/public/images';
    $files = scandir($dir);
    $files = array_diff($files, array('.', '..', __FILE__));
    return $this->view->render($response, 'admin/images', array(
      "images" => $files,
      "total" => count($files)
		));
  }

  public function getUploads(Request $request, Response $response) {
    $params = $request->getQueryParams();
    $perpage = 17;
    $page = $params['page'];
    $skip = ((int) $page - 1) * $perpage;
    $dir = ROOT . '/public/uploads/origin';
    $files = scandir($dir);
    $files = array_diff($files, array('.', '..', '.DS_Store', __FILE__));
    $files = array_reverse($files);
    $images = array_slice($files, $skip, $perpage);
    return $response->withJson([
      'code' => 0,
      'data' => $images,
      'total_page' => ceil(count($files)/$perpage),
      'page_number' => $page,
    ]);
  }

  public function removeImageUploads(Request $request, Response $response){
    $body = $request->getParsedBody();
    removeImage($body['name']);
    $path = ROOT . '/public/uploads/origin/';
    if (unlink($path.$body['name'])){
      return $response->withJson([
        'code' => 0,
        'message' => 'Deleted'
      ]);
    }
    return $response->withJson([
      'code' => -1,
      'message' => 'Erorr'
    ]);
  }

  public function removeImage(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $dir = ROOT . '/public/images/';
    $src = $dir . $body['img'];
    if(unlink($src)) {
      return $response->withJson([
        'code' => 0,
        'message' => 'Deleted'
      ]);
    }
    return $response->withJson([
      'code' => -1,
      'message' => 'Erorr'
    ]);
  }
}
?>
