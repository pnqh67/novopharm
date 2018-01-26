<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Location extends Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    protected $table = 'location';

    public function store($region_id, $subregion_id, $data) {
      $item = new Location;
      $item->region_id = $region_id;
      $item->subregion_id = $subregion_id;
      $item->name = $data['name'];
      $item->phone_number = $data['phone'];
      $item->address = $data['address'];
      $item->status = 'active';
      $item->created_at = date('Y-m-d H:i:s');
  	  $item->updated_at = date('Y-m-d H:i:s');
      if($item->save()) return $item->id;
      return -3;
    }

    public function update($id , $data) {
      $item = Location::find($id);
      if (!$item) return -2;
      $item->region_id = $data['region_id'];
      $item->subregion_id = $data['subregion_id'];
      $item->name = $data['name'];
      $item->phone_number = $data['phone'];
      $item->address = $data['address'];
      $item->status = 'active';
      $item->updated_at = date('Y-m-d H:i:s');
      if($item->save()) return 0;
      return -3;
    }

    public function remove($id){
      $item = Coupon::find($id);
      if (!$item) return -2;
      Location::where('id', $id)->update(['status' => 'delete']);
      return 0;
    }
}
