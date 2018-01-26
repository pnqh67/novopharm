<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Cart extends Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    protected $table = 'cart';

    public function store($order_id, $quantity) {
//      $variant = Variant::where('id', $data->variant_id)->first();
      $cart = new Cart;
      $cart->order_id = $order_id;
      $cart->variant_id = -1;
      $cart->price = -1;
      $cart->quantity = $quantity;
      $cart->created_at = date('Y-m-d H:i:s');
      $cart->updated_at = date('Y-m-d H:i:s');
      $cart->save();
    }
}
