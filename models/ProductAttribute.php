<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class ProductAttribute extends Illuminate\Database\Eloquent\Model {
    protected $table = 'product_attribute';

    public function getByProductId($productId) {
      $data = ProductAttribute::where('product_id', $productId)->get();
      return $data;
    }
    public function store($product_id, $attribute_id) {
        $temp = new ProductAttribute;
        $temp->product_id = $product_id;
        $temp->attribute_id = $attribute_id;
        $temp->created_at = date('Y-m-d H:i:s');
        $temp->updated_at = date('Y-m-d H:i:s');
        $temp->save();
    }

    public function storeProductAttribute($product_id, $attributes) {
      $item = ProductAttribute::where('product_id', $product_id)->first();
      if ($item) {
        ProductAttribute::where('product_id', $product_id)->delete();
      }
      foreach ($attributes as $key => $attribute_id) {
        $item = new ProductAttribute;
        $item->product_id = $product_id;
        $item->attribute_id = $attribute_id;
        $item->created_at = date('Y-m-d H:i:s');
        $item->updated_at = date('Y-m-d H:i:s');
        $item->save();
      }
      return 0;
    }
}
