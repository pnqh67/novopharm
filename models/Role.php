<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Role extends Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    protected $table = 'role';

    public function store($title) {
      $item = Role::where('title', $title)->first();
      if ($item) return -1;
      $item = new Role;
      $item->title = $title;
      $item->created_at = date('Y-m-d H:i:s');
      $item->updated_at = date('Y-m-d H:i:s');
      $item->save();
      return $item->id;
    }

    public function update($id, $title) {
      $item = Role::where('title', $title)->where('id', '!=', $id)->first();
      if ($item) return -1;
      $item = Role::find($id);
      if (!$item) return -2;
      $item->title = $title;
      $item->updated_at = date('Y-m-d H:i:s');
      $item->save();
      return 0;
    }

    public function remove($id) {
      $item = Role::find($id);
      if (!$item) return -2;
      $check = User::where('role_id', $id)->first();
      if ($check) return -1;
      $item->delete();
      Permission::where('role_id', $id)->delete();
      return 0;
    }
}
