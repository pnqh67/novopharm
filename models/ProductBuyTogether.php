<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use forxer\Gravatar\Gravatar;

class ProductBuyTogether extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'product_buy_together';

  public function store($data, $product_id, $product_title) {
    $item = ProductBuyTogether::where('product_id',$product_id)->get();
    if ($item){
      foreach ($item as $element){
        if ($element['product_buy_together_id']==$data['id']){
          $element->product_title = $product_title;
          $element->product_buy_together_id = $data['id'];
          $element->product_buy_together_title = $data['title'];
          $element->price_sale = $data['price_sale'];
          $element->promotion = $data['promotion'];
          $element->status = $data['status'];
          $element->updated_at = date('Y-m-d H:i:s');
          $element->save();
          return -1;
        }
      }
    }
    $item = new ProductBuyTogether;
    $item->product_id = $product_id;
    $item->product_title = $product_title;
    $item->product_buy_together_id = $data['id'];
    $item->product_buy_together_title = $data['title'];
    $item->price_sale = $data['price_sale'];
    $item->promotion = $data['promotion'];
    $item->status = $data['status'];
    $item->created_at = date('Y-m-d H:i:s');
    $item->updated_at = date('Y-m-d H:i:s');
    if ($item->save()) return $item->id;
    return -3;
  }

  public function update($id, $data) {
    $item = ProductBuyTogether::find($id);
    if (!$item) return -2;
    $item->product_id = $data['product_id'];
    $item->product_title = $data['product_title'];
    $item->product_buy_together_id = $data['product_buy_together_id'];
    $item->product_buy_together_title = $data['product_buy_together_title'];
    $item->price_sale = $data['price_sale'];
    $item->promotion = $data['promotion'];
    $item->status = $data['status'];
    $item->updated_at = date('Y-m-d H:i:s');
    if ($item->save()) return 0;
    return -3;
  }

  public function remove($id) {
    $item = ProductBuyTogether::find($id);
    if(!$item) return -2;
    $item->delete();
    return 0;
  }

}
