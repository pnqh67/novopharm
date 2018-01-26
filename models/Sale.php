<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use forxer\Gravatar\Gravatar;

class Sale extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'sale';

  public function store($data) {
    $item = Sale::where('title', $data['title'])->first();
    if ($item) return -1;
    $item = new Sale;
    $item->title = $data['title'];
    $item->title_en = $data['title_en'];
    $item->description = $data['description'];
    $item->description_en = $data['description_en'];
    $item->type = $data['type'];
    $item->value = $data['value'];
    $item->start_date = $data['start_date'];
    $item->end_date = $data['end_date'];
    $item->status = $data['status'];
    $item->created_at = date('Y-m-d H:i:s');
    $item->updated_at = date('Y-m-d H:i:s');
    if ($item->save()) return $item->id;
    return -3;
  }

  public function update($id, $data) {
    $item = Sale::find($id);
    if (!$item) return -2;
    $item->title = $data['title'];
    $item->title_en = $data['title_en'];
    $item->description = $data['description'];
    $item->description_en = $data['description_en'];
    $item->type = $data['type'];
    $item->value = $data['value'];
    $item->start_date = $data['start_date'];
    $item->end_date = $data['end_date'];
    $item->status = $data['status'];
    $item->updated_at = date('Y-m-d H:i:s');
    if ($item->save()) return 0;
    return -3;
  }

  public function remove($id) {
    $sale = Sale::find($id);
    if(!$sale) return -2;
    Sale::where('id', $id)->update(['status' => 'delete']);
    return 0;
  }

  public function checkValidSale($product_id) {
    $sale_product = SaleProduct::where('product_id', $product_id)->first();
    $item = Sale::where('status', 'active')
      ->where('id', $sale_product->sale_id)
      ->first();
    if (!$item) return -1;
    $current_date = date('Y-m-d');
    if ($current_date < $item->start_date) return -2;
    if ($current_date > $item->end_date) return -3;
    return $item;
  }

  public function getSaleDiscount($item, $price) {
    if ($item->type == 'value') return $item->value;
    $temp = floatval($price) * ($item->value / 100);
    return $temp;
  }

  public function checkStatus() {
    $now = date('Y-m-d');
    Sale::whereDate('end_date', '<', $now)->update(['status' => 'expried']);
    return 0;
  }

}
