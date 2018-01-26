<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/ProductBuyTogether.php");
require_once("../models/Product.php");
require_once(ROOT . '/controllers/helper.php');
use ControllerHelper as Helper;

class AdminProductBuyTogetherController extends AdminController {

  public function fetch(Request $request, Response $response) {
    $productBuyTogether = ProductBuyTogether::where('status', '!=', 'delete')->get();
    return $this->view->render($response, 'admin/product_buy_together',array(
      'data' => $productBuyTogether
    ));
  }

  public function create(Request $request, Response $response){
    $param = $request->getQueryParams();
    $productId = $param['id_product_main'];
    $product = Product::select('id','title','title_en')->get();
    return $this->view->render($response,'admin/product_buy_together_new',array(
        'products' => $product,
        'productId' => $productId?$productId:0
    ));
  }

  public function show(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $product = Product::select('id','title','title_en')->get();
    $productBuyTogether = ProductBuyTogether::find($id);
    return $this->view->render($response,'admin/product_buy_together_edit',array(
        'data' => $product,
        'productBuyTogether' => $productBuyTogether
    ));
  }

  public function store(Request $request, Response $response) {
    $data = $request->getParsedBody();
    $product_id = $data['product_id'];
    $product = Product::find($product_id);
    $product_title = $product->title;
    $data = $data['data'];
    foreach ($data as $item){
      ProductBuyTogether::store($item, $product_id, $product_title);
    }
    return $response->withJson(0, 200);
  }

  public function update(Request $request, Response $response) {
    $data = $request->getParsedBody();
    $product_id = $data['product_id'];
    $product = Product::find($product_id);
    $product_title = $product->title;
//    foreach ($data as $item){
//      ProductBuyTogether::update($id, $data);
//    }
    return $response->withJson(0, 200);
  }

  public function delete(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $code = ProductBuyTogether::remove($id);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }
}

?>
