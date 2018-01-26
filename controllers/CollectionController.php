<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once("../models/Collection.php");
require_once("../models/CollectionProduct.php");
require_once("../models/Product.php");
require_once("../models/helper.php");

class CollectionController extends Controller
{

  public function get(Request $request, Response $response) {
    $handle = $request->getAttribute('handle');

    $params = $request->getQueryParams();

    $page_number = $params['page']?$params['page']:1;
    $perpage = $params['perpage']?$params['perpage']:20;
    $skip = ($page_number - 1) * $perpage;

    $collection = Collection::where('status', 'active')->where('handle', $handle)->first();
    if ($collection) {
      $collection['image_src'] = '/uploads/' . $collection['image'];
      $query = Product::join('collection_product', 'collection_product.product_id', '=', 'product.id')
        ->where('collection_product.collection_id', $collection['id'])
        ->where('product.status', 'active')
        ->groupBy('product.id');
      $all_products = $query->select('product.*')->get();
      $total_pages = ceil(count($all_products) / $perpage);

      $productVariant = $query->join('variant', 'product.id', '=', 'variant.product_id')
        ->select('product.*')
        ->orderBy('updated_at', 'DESC')
        ->skip($skip)->take($perpage)
        ->get();
      $collection['products'] = Product::getInfoProduct($productVariant);
      $collection['total_pages'] = $total_pages;
      $collection['page_number'] = $page_number;
      $collection['per_page'] = $perpage;
    }
    return $this->view->render($response, 'collection_detail', array(
      'collection' => $collection
    ));
  }

  public function show(Request $request, Response $response) {
    $handle = $request->getAttribute('link');
    if (substr($handle, -1) == '/') $handle = substr($handle, 0, -1);
    else{
      $params = explode('/',$handle);
      unset($params[0]);
      $handle = implode('/', $params);
    }
    $collection = Collection::where('status','active')->where('handle', $handle)->first();
    if (!$collection){
      $this->view->render($response, '404');
      return $response->withStatus(404);
    }

    $params = $request->getQueryParams();
    $page_number = $params['page']?$params['page']:1;
    $perpage = $params['perpage']?$params['perpage']:20;
    $skip = ($page_number - 1) * $perpage;

    $query = Product::join('collection_product', 'collection_product.product_id', '=', 'product.id')
      ->where('collection_product.collection_id', $collection->id)
      ->where('product.status', 'active')
      ->groupBy('product.id');

    $productVariant = $query->join('variant', 'variant.product_id', '=', 'product.id');

    $all_products = $query->select('product.*')->get();
    $total_pages = ceil(count($all_products) / $perpage);
    $products = $productVariant->orderBy('product.in_stock', 'desc')
      ->orderBy('product.updated_at', 'desc')
      ->skip($skip)->take($perpage)->get();
    $collection['total_pages'] = $total_pages;
    $collection['page_number'] = $page_number;
    $collection['per_page'] = $perpage;
    $collection['products'] = Product::getInfoProduct($products);
    return $this->view->render($response, 'collection_child', array(
      'collection' => $collection
    ));
  }

  public function search(Request $request, Response $response) {
    $params = $request->getQueryParams();
    $search = $params['q'];
    if (!$search) return $response->withStatus(302)->withHeader('Location', '/');
    $breadcrumb_collection = array();
    $obj = new stdClass();
    $obj->handle = '#';
    $obj->title = 'Tìm kiếm';
    array_push($breadcrumb_collection, $obj);

    $page_number = 1;
    if ($params['page']) $page_number = $params['page'];
    $perpage = 20;
    $skip = ($page_number - 1) * $perpage;
    $query = Product::where('product.title', 'LIKE', '%' . $search . '%')->where('product.status', 'active');
    $all_products = $query->select('product.*')->get();
    $total_pages = ceil(count($all_products) / $perpage);
    $products = $query->orderBy('product.in_stock', 'desc')->orderBy('product.updated_at', 'desc')->skip($skip)->take($perpage)->get();
    $products = Product::getInfoProduct($products);

    $list_brand = array();
    $list_material = array();
    $list_color = array();
    $list_size = array();
    $list_bag = array();

    $arr_temp_color = array();
    $arr_temp_special = array();

    foreach ($all_products as $key => $product) {
      if ($product->brand && !in_array($product->brand, $list_brand)) array_push($list_brand, $product->brand);
      if ($product->material && !in_array($product->material, $list_material)) array_push($list_material, $product->material);
      if ($product->size && !in_array($product->size, $list_size)) array_push($list_size, $product->size);
      if ($product->bag && !in_array($product->bag, $list_bag)) array_push($list_bag, $product->bag);
      $product_color = ProductColor::Join('color', 'product_color.color_id', '=', 'color.id')
        ->where('product_color.product_id', $product->id)->select('color.name as name', 'color.hex as hex')->get();
      foreach ($product_color as $key => $color) {
        if (!in_array($color->name, $arr_temp_color)) {
          $obj = new stdClass();
          $obj->name = $color->name;
          $obj->style = 'background-color: ' . $color->hex;
          array_push($list_color, $obj);
          array_push($arr_temp_color, $color->name);
        }
      }
    }
    sort($list_brand);
    return $this->view->render($response, 'collection', array(
      'title' => 'Tìm kiếm',
      'list_product' => $products,
      'list_brand' => $list_brand,
      'list_material' => $list_material,
      'list_color' => $list_color,
      'list_size' => $list_size,
      'list_bag' => $list_bag,
      'total_pages' => $total_pages,
      'page_number' => $page_number,
      'breadcrumb_title' => $search,
      'breadcrumb_collection' => $breadcrumb_collection,
    ));
  }

  public function filter(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $collection_id = $body['collection_id'];
    $search = $body['search'];
    $url = $body['url'];
    $page_number = 1;
    $perpage = 20;
    if ($body['page']) $page_number = $body['page'];
    $skip = ($page_number - 1) * $perpage;
    $query = Product::where('product.status', 'active')->where('product.price', '>', 0)->where('in_stock', 1);

    if ($collection_id) $query = $query->join('collection_product', 'product.id', '=', 'collection_product.product_id')->where('collection_product.collection_id', $collection_id);
    else if ($search) {
      $query = $query->where('product.title', 'LIKE', '%' . $search . '%');
    }
    $check_sort = FALSE;
    if ($url) {
      $arr_item = explode('&', $url);
      foreach ($arr_item as $key => $item) {
        if (strpos($item, 'size') !== FALSE) {
          $arr_size = str_replace('size=', '', $item);
          $arr_size = explode('+', $arr_size);
          $GLOBALS['arr_size'] = $arr_size;
          $query = $query->where(function ($q) {
            global $arr_size;
            foreach ($arr_size as $key => $size) {
              $q = $q->orWhere('product.size', $size);
            }
          });
        } else if (strpos($item, 'brand') !== FALSE) {
          $arr_brand = str_replace('brand=', '', $item);
          $arr_brand = explode('+', $arr_brand);
          $GLOBALS['arr_brand'] = $arr_brand;
          $query = $query->where(function ($q) {
            global $arr_brand;
            foreach ($arr_brand as $key => $brand) {
              $q = $q->orWhere('product.brand', 'LIKE', '%' . $brand . '%');
            }
          });
        } else if (strpos($item, 'bag') !== FALSE) {
          $arr_bag = str_replace('bag=', '', $item);
          $arr_bag = explode('+', $arr_bag);
          $GLOBALS['arr_bag'] = $arr_bag;
          $query = $query->where(function ($q) {
            global $arr_bag;
            foreach ($arr_bag as $key => $bag) {
              $q = $q->orWhere('product.bag', 'LIKE', '%' . $bag . '%');
            }
          });
        } else if (strpos($item, 'material') !== FALSE) {
          $arr_material = str_replace('material=', '', $item);
          $arr_material = explode('+', $arr_material);
          $GLOBALS['arr_material'] = $arr_material;
          $query = $query->where(function ($q) {
            global $arr_material;
            foreach ($arr_material as $key => $material) {
              $q = $q->orWhere('product.material', 'LIKE', '%' . $material . '%');
            }
          });
        } else if (strpos($item, 'price=') !== FALSE) {
          $price = str_replace('price=', '', $item);
          $price = str_replace('(', '', $price);
          if (strpos($price, '-') !== FALSE) {
            $price = explode('-', $price);
            $query = $query->whereBetween('product.price', [$price[0], $price[1]]);
          } else if (strpos($price, '<')) {
            $price = substr($price, 1);
            $query = $query->where('product.price', '<', $price);
          } else if (strpos($price, '>')) {
            $price = substr($price, 1);
            $query = $query->where('product.price', '>', $price);
          }
        } else if (strpos($item, 'sort') !== FALSE) {
          $arr_sort = str_replace('sort=', '', $item);
          $arr_sort = explode(':', $arr_sort);
          $query = $query->orderBy($arr_sort[0], $arr_sort[1]);
          $check_sort = TRUE;
        }
      }
      if (!$check_sort) $query->orderBy('product.in_stock', 'desc')->orderBy('product.updated_at', 'desc');
    } else $query = $query->orderBy('product.in_stock', 'desc')->orderBy('product.updated_at', 'desc');

    $all = $query->distinct()->get();
    $products = $query->groupBy('product.id')->skip($skip)->take($perpage)->get();
    $total_pages = ceil(count($all) / (int)$perpage);
    if (count($products)) {
      $products = Product::getInfoProduct($products);
      return $this->view->render($response, 'collection_filter', [
        'products' => $products,
        'page_number' => $page_number,
        'total_pages' => $total_pages
      ]);
    }
    return 'empty';
  }
}
?>
