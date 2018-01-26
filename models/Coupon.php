<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Coupon extends Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    protected $table = 'coupon';

    public function store($data) {
      $check = Coupon::where('code', $data['code'])->first();
      if($check) return -1;
      $item = new Coupon;
      $item->title = $data['title'];
      $item->title_en = $data['title_en'];
      $item->description = $data['description'];
      $item->description_en = $data['description_en'];
      $item->type = $data['type'];
      $item->value = $data['value'];
      $item->code = $data['code'];
      $item->usage_count = 0;
      $item->usage_left = $data['usage_left'];
      $item->min_value_order = $data['min_value_order'];
      $item->max_value_percent = $data['max_value_percent']?$data['max_value_percent']:NULL;
      $item->start_date = $data['start_date'];
      $item->end_date = $data['end_date'];
      $item->status = $data['status'];
      $item->created_at = date('Y-m-d H:i:s');
  	  $item->updated_at = date('Y-m-d H:i:s');
      if($item->save()) return $item->id;
      return -3;
    }

    public function update($id , $data) {
      $item = Coupon::find($id);
      if (!$item) return -2;
      $check = Coupon::where('code', $data['code'])->where('id', '!=', $id)->first();
      if ($check) return -1;
      $item->title = $data['title'];
      $item->title_en = $data['title_en'];
      $item->description = $data['description'];
      $item->description_en = $data['description_en'];
      $item->type = $data['type'];
      $item->value = $data['value'];
      $item->code = $data['code'];
      $item->usage_left = $data['usage_left'];
      $item->min_value_order = $data['min_value_order'];
      $item->max_value_percent = $data['max_value_percent']?$data['max_value_percent']:NULL;
      $item->start_date = $data['start_date'];
      $item->end_date = $data['end_date'];
      $item->status = $data['status'];
      $item->updated_at = date('Y-m-d H:i:s');
      if($item->save()) return 0;
      return -3;
    }

    public function remove($id){
      $item = Coupon::find($id);
      if (!$item) return -2;
      Coupon::where('id', $id)->update(['status' => 'delete']);
      return 0;
    }

    public function updateUsage($code) {
      $item = Coupon::where('code', $code)->first();
      if (!$item) return -2;
      $item->usage_left = $item->usage_left - 1;
      $item->usage_count = $item->usage_count + 1;
      if ($item->save()) return 0;
      return -3;
    }

    public function checkValidCoupon($total, $code) {
      $item = Coupon::where('code', $code)->where('status', 'active')->first();
      if (!$item) return -1; //coupon không tồn tại
      if (!$item->usage_left) return -2; //coupon hết lượt sử dụng
      if ($item->min_value_order > $total) return -3; //

//      $start_date = strtotime($item->start_date);
//      $end_date = strtotime($item->end_date);
      $current_date = date('Y-m-d');
      if ($current_date < $item->start_date) return -4; //chưa hiệu lực
      if ($current_date > $item->end_date) return -5;  //hết hạn
      return $item;
    }

    public function getCouponDiscount($total, $code) {
      $item = Coupon::where('code', $code)->where('status', 'active')->first();
      if (!$item) return -2;
      if ($item->type == 'value') return $item->value;
      $temp = floatval($total) * ($item->value / 100);
      if ($temp > $item->max_value_percent) return $item->max_value_percent;
      return $temp;
    }

    public function checkStatus() {
      $now = date('Y-m-d');
      Coupon::whereDate('end_date', '<', $now)->update(['status' => 'expried']);
      return 0;
    }
}
