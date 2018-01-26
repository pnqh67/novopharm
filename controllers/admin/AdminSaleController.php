<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Sale.php");
require_once("../models/Product.php");
require_once("../models/SaleProduct.php");
require_once(ROOT . '/controllers/helper.php');
use ControllerHelper as Helper;

class AdminSaleController extends AdminController {

  public function fetch(Request $request, Response $response) {
    Sale::checkStatus();
    $sale = Sale::where('status', '!=', 'delete')->orderBy('id', 'desc')->get();
    return $this->view->render($response, 'admin/sale',array(
       'data' => $sale
    ));
  }

  public function create(Request $request, Response $response){
    $products = Product::select('id', 'title')->get();
    return $this->view->render($response, 'admin/sale_create',array(
        'products' =>$products
    ));
  }

  public function getProduct(Request $request, Response $response){
    $products = Product::select('id', 'title')->get();
    $result = Helper::response($products);
    return $response->withJson($result,200);
  }

  public function get(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sale = Sale::find($id);
    $products = Product::select('id', 'title')->get();
    $saleProduct = SaleProduct::where('sale_id', $id)->get();
    return $this->view->render($response, 'admin/sale_edit',array(
        'data' => $sale,
        'products' => $products,
        'saleProducts' => $saleProduct
    ));
  }

  public function store(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $code = Sale::store($body);
    if ($code) {
      foreach ($body['products'] as $product_id){
        SaleProduct::store($code, $product_id);
      }
      History::admin('create', 'sale', $code, $body['title']);
    }
    $result = Helper::response($code);
    return $response->withJson($result,200);
  }

  public function update(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $id = $request->getAttribute('id');
    $code = Sale::update($id, $body);
    if (!$code){
      SaleProduct::where('sale_id', $id)->delete();
      foreach ($body['products'] as $product_id){
        SaleProduct::store($id, $product_id);
      }
      History::admin('update', 'sale', $id, $body['title']);
    }
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function delete(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $sale = Sale::find($id);
    $code = Sale::remove($id);
    if (!$code) History::admin('delete', 'sale', $id, $sale->title);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }
}

?>
