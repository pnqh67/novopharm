<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use forxer\Gravatar\Gravatar;

class SaleProduct extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'sale_product';

  public function store($saleId, $productId) {
    $sale = new SaleProduct;
    $sale->sale_id = $saleId;
    $sale->product_id = $productId;
    $sale->created_at = date('Y-m-d H:i:s');
    $sale->updated_at = date('Y-m-d H:i:s');
    if ($sale->save()) return $sale->id;
  }

  public function remove($id) {
    $saleproduct = SaleProduct::find($id);
    if(!$saleproduct) return -2;
    if($saleproduct->delete()) return $saleproduct->id;
    return -3;
  }
}
