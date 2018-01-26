<?php
require_once(ROOT . '/models/Product.php');
require_once(ROOT . '/models/Variant.php');
require_once(ROOT . '/models/Image.php');
require_once(ROOT . '/controllers/helper.php');
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use ControllerHelper as Helper;

class AdminVariantController extends AdminController {

  public function store (Request $request, Response $response) {
    $body = $request->getParsedBody();
    $option = $body['option'];
    $arrOption = explode('/',$option);
//    $arr = [
//      'product_id' => $body['product_id'],
//      'title' => $arrOption[0],
//      'price' => $body['price']?$body['price']:0
//    ];
//    $checkNull = Helper::checkNull($arr);
//    if ($checkNull) return $response->withJson($checkNull, 200);

    $code = Variant::store($body, $arrOption);
    if ($code) {
      foreach ($body['list_image'] as $key => $image) {
        Image::store($image, 'variant', $code);
        //Image::store($image, 'product', $body['product_id']);
      }
    }
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function update(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $body = $request->getParsedBody();
    $arr = [
      'product_id' => $body['product_id'],
      'title' => $body['title'],
      'price' => $body['price']
    ];
    $checkNull = Helper::checkNull($arr);
    if ($checkNull) return $response->withJson($checkNull, 200);

    $code = Variant::update($id, $body);
    if (!$code) {
      Image::where('type', 'variant')->where('type_id', $id)->delete();
      foreach ($body['list_image'] as $key => $image) {
        Image::store($image, 'variant', $id);
        //Image::store($image, 'product', $body['product_id']);
      }
    }
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function delete(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $code = Variant::remove($id);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function renderImageTinymce(Request $request, Response $response) {
    $dir = ROOT . '/public/images';
    $images = scandir($dir);
    array_shift($images);
    array_shift($images);
    return $this->view->render($response, 'admin/tinymce-upload', array(
      "title" => "Upload image",
      "images" => $images,
      "total" => count($images)
    ));
  }
}

?>
