<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Permission extends Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    protected $table = 'permission';

    public function store($role_id, $data) {
      $item = new Permission;
      $item->role_id = $role_id;
      $item->group = $data['group'];
      $item->method = $data['method'];
      $item->endpoint = $data['endpoint'];
      $item->created_at = date('Y-m-d H:i:s');
      $item->updated_at = date('Y-m-d H:i:s');
      $item->save();
    }

    public function remove($role_id) {
      Permission::where('role_id', $role_id)->delete();
      return 0;
    }
}
