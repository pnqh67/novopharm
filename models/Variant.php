<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Variant extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'variant';

  public function store($data, $arrOption) {
    $item = new Variant;
    $item->product_id = $data['product_id'];
    foreach ($arrOption as $index=>$value){
      $item['option_' . ($index + 1)] = $value;
    }
    $item->price = $data['price']?$data['price']:0;
    $item->price_compare = $data['price_compare'];
    $item->inventory = $data['inventory'] ? $data['inventory'] : 0;
    $item->status = 'active';
    $item->created_at = date('Y-m-d H:i:s');
    $item->updated_at = date('Y-m-d H:i:s');
    if ($item->save()) return $item->id;
    return -3;
  }

  public function update($id, $data) {
    $item = Variant::find($id);
    if (!$item) return -2;
    $item->title = $data['title'];
    $item->title_en = $data['title_en'];
    $item->price = $data['price'];
    $item->price_compare = $data['price_compare'];
    $item->inventory = $data['inventory'] ? $data['inventory'] : 0;
    $item->updated_at = date('Y-m-d H:i:s');
    if ($item->save()) return 0;
    return -3;
  }

  public function remove($id) {
    $item = Variant::where('id', $id)->update(['status' => 'delete']);
    return 0;
  }
}
