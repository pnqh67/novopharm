<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once("../models/Product.php");
require_once("../models/Variant.php");
require_once("../models/Slider.php");
require_once("../models/CollectionProduct.php");
require_once("../models/helper.php");
require_once("../models/Collection.php");
require_once("../models/Customer.php");

class ProductController extends Controller
{
  public function fetch(Request $request, Response $response) {
    $params = $request->getQueryParams();

    $page_number = $params['page']?$params['page']:1;
    $perpage = $params['perpage']?$params['perpage']:20;
    $skip = ($page_number - 1) * $perpage;

    $allProduct = Product::where('status', 'active');
    $total_pages = ceil(count($allProduct->get()) / $perpage);
    $products = $allProduct->skip($skip)->take($perpage)->get();
    foreach ($products as $product) {
      $product['variants'] = Variant::where('product_id', $product['id'])->get();
      $product['images'] = Variant::join('image', 'variant.id', '=', 'image.type_id')
        ->where('variant.product_id', $product['id'])
        ->get();
      $rel_prod_variant = Variant::where('product_id', $product['id'])->first();
      $product->display_discount = 0;
      if ($rel_prod_variant->price_compare && ($rel_prod_variant->price_compare > $rel_prod_variant->price)) {
        $product->percent = 0;
        $discount = $rel_prod_variant->price_compare - $rel_prod_variant->price;
        $product->variant_id = $rel_prod_variant->id;
        $product->percent = ($discount / $rel_prod_variant->price_compare) * 100;
        $product->percent = round($product->percent, 0) . '%';
        $product->price = $rel_prod_variant->price;
        $product->price_compare = $rel_prod_variant->price_compare;
        $product->display_discount = 1;
      } else {
        $product->variant_id = $rel_prod_variant->id;
        $product->price = $rel_prod_variant->price;
        $product->price_compare = $rel_prod_variant->price_compare;
      }
    }
    return $this->view->render($response, 'product', array(
      'products' => $products,
      'total_pages' => $total_pages,
      'page_number' => $page_number,
      'per_page' => $perpage
    ));
  }

  public function get(Request $request, Response $response) {
    $handle = $request->getAttribute('handle');
    $query = $request->getQueryParams();
    $product = Product::where('status', 'active')
      ->where('handle', $handle)
      ->orWhere('handle_en', $handle)
      ->first();
    if (!$product) {
      $this->view->render($response, '404');
      return $response->withStatus(404);
    }
    $product->view++;
    $product->save();
    $variants = Variant::where('product_id', $product->id)->get();
    $list_images = Variant::join('image', 'variant.id', '=', 'image.type_id')
      ->where('variant.product_id', $product->id)
      ->get();
    $product['variants'] = $variants;
    $product['images'] = $list_images;

    $product_collection = Product::join('collection_product', 'product.id', '=', 'collection_product.product_id')
      ->where('collection_product.product_id', $product->id)
      ->inRandomOrder()->first();
    $related_products = Product::join('collection_product', 'product.id', '=', 'collection_product.product_id')
      ->where('collection_product.collection_id', $product_collection->collection_id)
      ->where('product.id', '!=', $product->id)
      ->select('product.*')->take(6)->get();
    foreach ($related_products as &$related_product) {
      $rel_prod_variant = Variant::where('product_id', $related_product->id)->first();
      $related_product->display_discount = 0;
      if ($rel_prod_variant->price_compare && ($rel_prod_variant->price_compare > $rel_prod_variant->price)) {
        $related_product->percent = 0;
        $discount = $rel_prod_variant->price_compare - $rel_prod_variant->price;
        $related_product->variant_id = $rel_prod_variant->id;
        $related_product->percent = ($discount / $rel_prod_variant->price_compare) * 100;
        $related_product->percent = round($related_product->percent, 0) . '%';
        $related_product->price = $rel_prod_variant->price;
        $related_product->price_compare = $rel_prod_variant->price_compare;
        $related_product->display_discount = 1;
      } else {
        $related_product->variant_id = $rel_prod_variant->id;
        $related_product->price = $rel_prod_variant->price;
        $related_product->price_compare = $rel_prod_variant->price_compare;
      }
    }

    $GLOBALS['product_id'] = $product->id;
    if ($product->view) {
      if (isset($_SESSION['seen']) && !empty($_SESSION['seen'])) {
        if (!in_array($product->id, $_SESSION['seen'])) {
          array_push($_SESSION['seen'], $product->id);
        }
      } else $_SESSION['seen'] = array($product->id);
    }

    if (count($_SESSION['seen'])) {
      $product_seen = Product::where('status', 'active')
        ->where('id', '!=', $product->id)
        ->whereIn('id', $_SESSION['seen'])
        ->take(5)->get();
      foreach ($product_seen as $item) {
        $rel_prod_variant = Variant::where('product_id', $item->id)->first();
        $item->display_discount = 0;
        if ($rel_prod_variant->price_compare && ($rel_prod_variant->price_compare > $rel_prod_variant->price)) {
          $item->percent = 0;
          $discount = $rel_prod_variant->price_compare - $rel_prod_variant->price;
          $item->variant_id = $rel_prod_variant->id;
          $item->percent = ($discount / $rel_prod_variant->price_compare) * 100;
          $item->percent = round($item->percent, 0) . '%';
          $item->price = $rel_prod_variant->price;
          $item->price_compare = $rel_prod_variant->price_compare;
          $item->display_discount = 1;
        } else {
          $item->variant_id = $rel_prod_variant->id;
          $item->price = $rel_prod_variant->price;
          $item->price_compare = $rel_prod_variant->price_compare;
        }
      }
    }
    $product['comments'] = Comment::where('status', 'active')
      ->where('type', 'product')
      ->where('type_id', $product['id'])
      ->join('customer', 'comment.customer_id', '=', 'customer.id')
      ->select('comment.*', 'customer.name as name')
      ->get();
    $product['comments_count'] = count($product['comments']);
    return $this->view->render($response, 'product_detail', array(
      'product' => $product,
      'product_seen' => $product_seen,
      'related_products' => $related_products
    ));
  }

  public function quickView(Request $request, Response $response) {
    $id = $request->getAttribute('id');

    $product = Product::find($id);

    $product->view++;
    $product->save();

    $variants = Variant::where('product_id', $product->id)->get();

    $list_images = Variant::join('image', 'variant.id', '=', 'image.type_id')->where('variant.product_id', $product->id)->get();

    if ($product->display) {
      if (isset($_SESSION['seen']) && !empty($_SESSION['seen'])) {
        if (!in_array($product->id, $_SESSION['seen'])) array_push($_SESSION['seen'], $product->id);
      } else $_SESSION['seen'] = [$product->id];
    }

    $product['variants'] = $variants;
    $product['images'] = $list_images;
    return $this->view->render($response, 'snippet/modal-quickview', array(
      'product' => $product
    ));
  }

  public function findProductVariant(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $product = Product::find($id);
    if (!$product) {
      return $response->withJson([
        'code' => -1,
        'message' => 'not found'
      ]);
    }

    $product->display_discount = 0;
    if ($product->price_compare && $product->price_compare > $product->price) {
      $product->percent = 0;
      $product->discount = $product->price_compare - $product->price;
      $product->percent = ($product->discount / $product->price_compare) * 100;
      $product->percent = round($product->percent, 0) . '%';
      $product->display_discount = 1;
    }

    $product->in_stock = FALSE;
    $collection_parent = CollectionProduct::where('product_id', $product->id)->join('collection', 'collection.id', '=', 'collection_product.collection_id')->where('collection.parent_id', '-1')->first();
    $collection_parent = Collection::find($collection_parent->id);
    $product->title = $collection_parent->title . ' ' . $product->title;
    $list_image = Image::getImage('product', $id);
    $product->list_image = $list_image;

    return $response->withJson([
      'code' => 0,
      'data' => $product
    ]);
  }

  public function getProductOfVariant(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $product = Product::where('product.status', 'active')
      ->join('collection_product', 'collection_product.product_id', '=', 'product.id')
      ->join('variant', 'variant.product_id', '=', 'product.id')
      ->where('collection_product.collection_id', $id)
      ->orderBy('product.updated_at', 'desc')
      ->select('product.*', 'variant.price', 'variant.price_compare', 'variant.id as variant_id')
      ->take(6)
      ->get();
    foreach ($product as $item) {
      $item->display_discount = 0;
      if ($item->price_compare && $item->price_compare > $item->price) {
        $item->percent = 0;
        $item->discount = $item->price_compare - $item->price;
        $item->percent = ($item->discount / $item->price_compare) * 100;
        $item->percent = round($item->percent, 0) . '%';
        $item->display_discount = 1;
      }

      $item->in_stock = FALSE;
      $list_image = Image::getImage('product', $item . id);
      $item->list_image = $list_image;
    }
    return $response->withJson([
      'code' => 0,
      'data' => $product
    ]);
  }
}

?>
