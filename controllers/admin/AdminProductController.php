<?php
require_once(ROOT . '/models/Product.php');
require_once(ROOT . '/models/Attribute.php');
require_once(ROOT . '/models/Collection.php');
require_once(ROOT . '/models/CollectionProduct.php');
require_once(ROOT . '/models/Seo.php');
require_once(ROOT . '/controllers/helper.php');
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Connection as DB;
use ControllerHelper as Helper;

class AdminProductController extends AdminController {

  public function fetch(Request $request, Response $response) {
    $data = Product::where('status', '!=', 'delete')->orderBy('id', 'desc')->get();
    $tags = Tag::orderBy('name', 'asc')->get();
    $collections = Collection::where('status', '!=', 'delete')
      ->where('parent_id',-1)
      ->get();
    $this->getCollection($collections);
    return $this->view->render($response, 'admin/product', [
      'data' => $data,
      'collections' => $collections,
      'tags' => $tags
    ]);
  }

  public function create(Request $request, Response $response) {
    $attributes = Attribute::listAttributeProduct();
    $tags = Tag::orderBy('name', 'asc')->get();
    $products = Product::where('status','!=','delete')->get();
    $collections = Collection::where('status', '!=', 'delete')
      ->where('parent_id',-1)
      ->get();
    $this->getCollection($collections);
    return $this->view->render($response, 'admin/product_create', array(
      'collections' => $collections,
      'attributes' => $attributes,
      'tags' => $tags,
      'products' => $products
    ));
  }

  public function get(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $data = Product::find($id);
    $productBuyTogether = ProductBuyTogether::where('product_id', $id)->where('status','!=','delete')->get();
    if (!$data) return $response->withStatus(302)->withHeader('Location', '/404');

    $collections = Collection::where('status', '!=', 'delete')->orderBy('breadcrumb', 'asc')->get();
    $attributes = Attribute::listAttributeProduct();
    $tags = Tag::orderBy('name', 'asc')->get();

    $variants = Variant::where('product_id', $id)->where('status', 'active')->get();
    foreach ($variants as $key => $variant) {
      $variant->list_image = Image::getImage('variant', $variant->id);
    }
    $data->variants = $variants;

    $listOptionVariant = [];
    for ($i=1; $i<=6; $i++){
       $temp = Variant::distinct()->where('product_id', $id)->where('status', 'active')->pluck('option_'.$i)->toArray();
       if ($temp != ['']) array_push($listOptionVariant,$temp);
    }

    $products = Product::where('status','!=','delete')->get();

    $data->collection_id = CollectionProduct::where('product_id', $id)->pluck('collection_id')->toArray();
    $data->tag = ProductTag::where('product_id', $id)->pluck('tag_name')->toArray();
    if (count($data->tag)) $data->tag = implode(',', $data->tag);
    return $this->view->render($response, 'admin/product_edit', array(
      'data' => $data,
      'productBuyTogethers' => $productBuyTogether,
      'collections' => $collections,
      'attributes' => $attributes,
      'tags' => $tags,
      'products' => $products,
      'listOption' => $listOptionVariant
    ));
  }

  public function store (Request $request, Response $response) {
    $body = $request->getParsedBody();
    $arr = [
      'title' => $body['title'],
      'handle' => $body['handle']
    ];
    $checkNull = Helper::checkNull($arr);
    if ($checkNull) {
      return $response->withJson($checkNull, 200);
    }
    $code = Product::store($body);
    if ($code) {
      if (count($body['collections'])) CollectionProduct::storeCollectionProduct($code, $body['collections']);
      if (count($body['tags'])) {
        Tag::storeListTags($body['tags']);
        ProductTag::storeProductTag($code, $body['tags']);
      }
      History::admin('create', 'product', $code, $body['title']);
    }
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function update(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $body = $request->getParsedBody();
    $arr = [
      'title' => $body['title'],
      'handle' => $body['handle']
    ];
    $checkNull = Helper::checkNull($arr);
    if ($checkNull) {
      return $response->withJson($checkNull, 200);
    }
    $code = Product::update($id, $body);
    if (!$code) {
      CollectionProduct::where('product_id', $id)->delete();
      ProductAttribute::where('product_id', $id)->delete();
      ProductTag::where('product_id', $id)->delete();
      if (count($body['collections'])) CollectionProduct::storeCollectionProduct($id, $body['collections']);
      if (count($body['attributes'])) ProductAttribute::storeProductAttribute($id, $body['attributes']);
      if (count($body['tags'])) {
        Tag::storeListTags($body['tags']);
        ProductTag::storeProductTag($id, $body['tags']);
      }
      History::admin('update', 'product', $id, $body['title']);
    }
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function delete(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $product = Product::find($id);
    $code = Product::remove($id);
    if (!$code) History::admin('delete', 'product', $id, $product->title);
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

  public function SEO(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $code = Seo::createOrUpdate($body['type'], $body['type_id'], $body['data']);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function updateStock(Request $request, Response $response) {
    $params = $request->getQueryParams();
    $product_id = $params['product_id'];
    $code = Product::updateStock($product_id);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
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
}

?>
