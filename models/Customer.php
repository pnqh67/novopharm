<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Customer extends Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    protected $table = 'customer';

    public function store($data) {
      $customer = new Customer;
      $random = randomString(50);
      $customer->name = $data['name'];
      $customer->phone = $data['phone'];
      $customer->email = $data['email'];
      $customer->password = password_hash($data['password'],PASSWORD_DEFAULT);
      $customer->address = $data['address'];
      $customer->region = $data['region'];
      $customer->subregion = $data['subregion'];
      $customer->random = $random;
      $customer->created_at = date('Y-m-d H:i:s');
      $customer->updated_at = date('Y-m-d H:i:s');
      if($customer->save()) return $customer->id;
    }

    public function update($data, $email) {
      $customer = Customer::where('email', $email)->first();
      if($customer) {
        $customer->name = $data['name'];
        $customer->email = $email;
        $customer->address = $data['address'];
        $customer->password = password_hash($data['password'],PASSWORD_DEFAULT);
        $customer->region = $data['region'];
        $customer->subregion = $data['subregion'];
        $customer->updated_at = date('Y-m-d H:i:s');
        $customer->save();
        return $customer->id;
      }
    }

    public function createOrUpdate($data) {
      $item = Customer::where('phone', $data['phone'])->first();
      if ($item) {
        $item->name = $data['name'];
        $item->phone = $data['phone'];
        $item->email = $data['email'];
        $item->password = password_hash(randomString(8),PASSWORD_DEFAULT);
        $item->address = $data['address'];
        $item->region = '';
        $item->subregion = '';
        $item->updated_at = date('Y-m-d H:i:s');
        $item->save();
      } else {
        $item = new Customer;
        $item->name = $data['name'];
        $item->phone = $data['phone'];
        $item->email = $data['email'];
        $item->password = password_hash(randomString(8),PASSWORD_DEFAULT);
        $item->address = $data['address'];
        $item->region = '';
        $item->subregion = '';
        $item->created_at = date('Y-m-d H:i:s');
        $item->updated_at = date('Y-m-d H:i:s');
        $item->save();
      }
      return $item->id;
    }

    public function delete($id){
        $customer = Customer::find($id);
        if(!$customer) return -2;
        if($customer->delete()) return $customer->id;
        return -3;
    }
}
