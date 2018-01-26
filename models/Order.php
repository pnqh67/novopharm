<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use GuzzleHttp\Client;

class Order extends Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    protected $table = 'order';

    public function store($customer_id, $data, $subTotal, $total) {
      $item = new Order;
      $item->customer_id = $customer_id;
      $item->payment_method = 'COD';
      $item->shipping_price = '';
      $item->discount = '';
      $item->subtotal = $subTotal;
      $item->total = $total;
      $item->notes = $data['notes']?$data['notes']:'';
      $item->order_status = 'new';
      $item->payment_status = 0;
      $item->shipping_status = 0;
      $item->reason_cancel = '';
      $item->status = 'active';
      $item->created_at = date('Y-m-d H:i:s');
      $item->updated_at = date('Y-m-d H:i:s');
      $item->save();
      return $item->id;
    }

    public function update($id, $data) {
      $item = Order::find($id);
      if (!$item) return -2;
      $item->order_status = $data['order_status'];
      $item->payment_status = (int) $data['payment_status'];
      $item->shipping_status = (int) $data['shipping_status'];
      $item->reason_cancel = $data['reason_cancel'];
      $item->updated_at = date('Y-m-d H:i:s');
      $item->save();
      return 0;
    }

    public function sumOrder($day){
        $today = date_create(date('Y-m-d'));
        $sub = date_sub($today, date_interval_create_from_date_string($day . " days"));
        $revenue = Order::where('order_status','done')
            ->where('updated_at','>',$sub)
            ->selectRaw('DATE_FORMAT(updated_at,"%d-%m") as date')
            ->selectRaw('sum(subtotal) as sum')
            ->groupBy('updated_at')
            ->get();
        return $revenue;
    }

    public function countOrder($day){
        $today = date_create(date('Y-m-d'));
        $sub = date_sub($today, date_interval_create_from_date_string($day . " days"));
        $revenueCount = Order::where('order_status','done')
            ->whereDate('updated_at','>',$sub)
            ->count();
        $revenueTotal = Order::where('order_status','done')
            ->whereDate('updated_at','>',$sub)
            ->sum('subtotal');
        $data['count'] = $revenueCount;
        $data['total'] = $revenueTotal;
        return $data;
    }

}
