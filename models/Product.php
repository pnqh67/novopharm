<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Connection as DB;

class Product extends Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    protected $table = 'product';

    public function store($data) {
      $item = Product::where('title', $data['title'])->first();
      if ($item) return -1;
      $item = new Product;
      $item->title = $data['title'];
      $item->title_en = $data['title_en'];
      $item->handle = $data['handle'] ? $data['handle'] : createHandle($data['title']);
      $item->handle_en = $data['handle_en'] ? $data['handle_en'] : $data['handle'];
      $item->featured_image = $data['featured_image'];
      $item->description = $data['description'];
      $item->description_en = $data['description_en'];
      $item->content = $data['content'];
      $item->content_en = $data['content_en'];
      $item->stock = 0;
      $item->in_stock = 0;
      $item->inventory_management = (int) $data['inventory_management'] ? 1 : 0;
      $item->view = 0;
      $item->sell = 0;
      $item->status = $data['status'];
      foreach ($data['arrOption'] as $index=>$value){
        $item['option_' . ($index + 1)] = $value;
      }
      $item->created_at = date('Y-m-d H:i:s');
      $item->updated_at = date('Y-m-d H:i:s');
      if ($item->save()) return $item->id;
      return -3;
    }

    public function update($id, $data) {
      $item = Product::where('title', $data['title'])->where('id', '!=', $id)->first();
      if ($item) return -1;
      $item = Product::find($id);
      if (!$item) return -2;
      $item->title = $data['title'];
      $item->title_en = $data['title_en'];
      $item->handle = $data['handle'] ? $data['handle'] : createHandle($data['title']);
      $item->handle_en = $data['handle_en'] ? $data['handle_en'] : $data['handle'];
      $item->featured_image = $data['featured_image'];
      $item->description = $data['description'];
      $item->description_en = $data['description_en'];
      $item->content = $data['content'];
      $item->content_en = $data['content_en'];
      $item->status = $data['status'];
      $item->inventory_management = (int) $data['inventory_management'] ? 1 : 0;
      $item->updated_at = date('Y-m-d H:i:s');
      if ($item->save()) return 0;
      return -3;
    }

    public function remove($id) {
      Product::where('id', $id)->update(['status' => 'delete']);
      return 0;
    }

    public function getInfoProduct($products) {
      foreach ($products as $key => $value) {
        $firstVariant = Variant::where('product_id', $value['id'])->first();
  			$value['display_discount'] = false;
        $value['percent'] = 0;
        $value['variant_id'] = $firstVariant['id'];
        $value['price'] = $firstVariant['price'];
        $value['price_compare'] = $firstVariant['price_compare'];
        if($firstVariant['price_compare'] && $firstVariant['price_compare'] > $firstVariant['price']) {
          $value['discount'] = $firstVariant['price_compare'] - $firstVariant['price'];
          $value['percent'] = ($value['discount'] / $firstVariant['price_compare']) * 100;
          $value['percent'] = round($value['percent'], 0) .'%';
          $value['display_discount'] = true;
  			}
  		}
      return $products;
    }

    public function getRelatedProducts($id) {
      return Product::Join('collection_product', 'collection_product.product_id', '=', 'product.id')
      ->where('collection_product.collection_id', $collection_id_related)
      ->where('product.status', 'active')->where('product.id', '!=', $product->id)->select('product.*')->where('product.in_stock', 1)->orderBy('product.updated_at', 'desc')->take(6)->get();
    }

    public function updateSell($id, $quantity) {
      $product = Product::find($id);
      if($product) {
        $sell = (int) $quantity;
        if($product->sell) $sell = $product->sell + $sell;
        $product->sell = $sell;
        $product->save();
      }
    }

    public function updateView($id) {
      $product = Product::find($id);
      if($product) {
        $view = 1;
        if($product->view) $view = $product->view + 1;
        $product->view = $view;
        $product->save();
      }
    }

    public function updateStock($id) {
      $sum = Variant::where('product_id', $id)->where('status', 'active')->sum('inventory');
      Product::where('id', $id)->update(['stock' => $sum]);
      return 0;
    }


  }
