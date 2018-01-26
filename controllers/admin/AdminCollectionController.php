<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once(ROOT . '/models/Collection.php');
require_once(ROOT . '/models/Product.php');
require_once(ROOT . '/models/Seo.php');
require_once(ROOT . '/models/History.php');
require_once(ROOT . '/controllers/helper.php');
use ControllerHelper as Helper;

class AdminCollectionController extends AdminController
{

  public function index(Request $request, Response $response){
    $data = Collection::where('status', '!=', 'delete')
      ->where('parent_id',-1)
      ->get();
    $this->getCollection($data);
//    return $response->withJson($data);
    return $this->view->render($response, 'admin/collection', array(
      'collections' => $data
    ));
  }

  private function getCollection($collections){
    foreach ($collections as $key => $collection) {
      $collection->subcollection = 0;
      $subcollection = Collection::where('status','!=','delete')
        ->where('parent_id', $collection->id)
        ->get();
      if (count($subcollection)) {
        $collection->subcollection = $subcollection;
        $this->getCollection($collection->subcollection);
      }
    }
    return 0;
  }

  public function create(Request $request, Response $response)
  {
    $collections = Collection::where('status', '!=', 'delete')->orderBy('breadcrumb', 'asc')->select('breadcrumb', 'id')->get();
    return $this->view->render($response, 'admin/collection_create', array(
      'collections' => $collections
    ));
  }

  public function show(Request $request, Response $response)
  {
    $id = $request->getAttribute('id');
    $data = Collection::find($id);
    if (!$data) return $response->withStatus(302)->withHeader('Location', '/404');
    $collections = Collection::where('status', '!=', 'delete')
      ->where('id', '!=', $id)
      ->orderBy('breadcrumb', 'asc')
      ->get();
    $products = Product::join('collection_product', 'collection_product.product_id', '=', 'product.id')
      ->where('collection_product.collection_id', $id)
      ->where('product.status', '!=', 'delete')
      ->groupBy('product.id')
      ->orderBy('product.updated_at', 'DESC')
      ->get();
    return $this->view->render($response, 'admin/collection_edit', array(
      'data' => $data,
      'collections' => $collections,
      'products' => $products
    ));
  }

  public function store(Request $request, Response $response)
  {
    $body = $request->getParsedBody();
    $arr = [
      'title' => $body['title'],
      'handle' => $body['handle']
    ];
    $checkNull = Helper::checkNull($arr);
    if ($checkNull) return $response->withJson($checkNull, 200);

    $code = Collection::store($body);
    if ($code) History::admin('create', 'collection', $code, $body['title']);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function update(Request $request, Response $response)
  {
    $id = $request->getAttribute('id');
    $body = $request->getParsedBody();
    $arr = [
      'title' => $body['title']
    ];
    $checkNull = Helper::checkNull($arr);
    if ($checkNull) return $response->withJson($checkNull, 200);

    $code = Collection::update($id, $body);
    if (!$code) History::admin('update', 'collection', $id, $body['title']);

    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function delete(Request $request, Response $response)
  {
    $id = $request->getAttribute('id');
    $collection = Collection::find($id);
    $code = Collection::remove($id);
    if (!$code) History::admin('delete', 'collection', $id, $collection->title);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function addMuch(Request $request, Response $response)
  {
    $body = $request->getParsedBody();
    foreach ($body['arrId'] as $productId) {
      foreach ($body['arrIdCollection'] as $collectionId) {
        CollectionProduct::storeInProduct($productId, $collectionId);
      }
    }
  }

  public function deleteMuch(Request $request, Response $response)
  {
    $body = $request->getParsedBody();
    foreach ($body['arrId'] as $productId) {
      foreach ($body['arrIdCollection'] as $collectionId) {
        CollectionProduct::deleteInProduct($productId, $collectionId);
      }
    }
  }

  public function sortProduct(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $product = Product::find($id);
    if ($product){
      $product->updated_at = date('Y-m-d H:i:s');
      if($product->save()) return 0;
      return -3;
    }
    return -1;
  }

  public function removeProduct(Request $request, Response $response){
    $body = $request->getParsedBody();
    $collection_product = CollectionProduct::where('collection_id',$body['collection_id'])
      ->where('product_id',$body['product_id'])
      ->first();
    if (!$collection_product) return -2;
    $collection_product->delete();
    return 0;
  }
}
