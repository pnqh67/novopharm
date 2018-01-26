<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use forxer\Gravatar\Gravatar;

class ProductTag extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'product_tag';

  public function store($product_id, $tag) {
    $items = ProductTag::where('product_id', $product_id)->get();
    $status = 0;
    foreach ($items as $item){
        if ($item['tag_name'] == $tag)
            $status = 1;
    }
    if (!$status){
        $product_tag = new ProductTag;
        $product_tag->product_id = $product_id;
        $product_tag->tag_name = $tag;
        $product_tag->created_at = date('Y-m-d H:i:s');
        $product_tag->updated_at = date('Y-m-d H:i:s');
        if ($product_tag->save()) return $product_tag->id;
        return -3;
    }
  }

  public function deleteOneTag($product_id, $tag){
      $items = ProductTag::where('product_id',$product_id)->get();
      foreach ($items as $item){
          if ($item['tag_name'] == $tag){
              ProductTag::find($item['id'])->delete();
          }
      }
  }

  public function storeProductTag($product_id, $tags) {
    $item = ProductTag::where('product_id', $product_id)->first();
    if ($item) {
      ProductTag::where('product_id', $product_id)->delete();
    }
    foreach ($tags as $key => $tag_name) {
      $item = new ProductTag;
      $item->tag_name = $tag_name;
      $item->product_id = $product_id;
      $item->created_at = date('Y-m-d H:i:s');
      $item->updated_at = date('Y-m-d H:i:s');
      $item->save();
    }
    return 0;
  }

  public function remove($id) {
    $product_tag = ProductTag::where('product_id', $id)->get();
    if(!$product_tag) return -2;
    if($product_tag->destroy()) return 0;
    return -3;
  }

  public function findViaProducId($id){
      $tag_id = ProductTag::where('product_id',$id)->select('tag_id')->get();
      if ($tag_id) return $tag_id;
  }
}
