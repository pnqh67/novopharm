<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class History extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'history';

  function user($data) {
    $item = new History;
    $item->user_id = $data['user_id'];
    $item->user_name = $data['user_name'];
    $item->user_type = 'user';
    $item->action = $data['action'];
    $item->type = $data['type'];
    $item->type_id = $data['type_id'];
    $item->type_title = $data['type_title'];
    $item->created_at = date('Y-m-d H:i:s');
    $item->updated_at = date('Y-m-d H:i:s');
    $item->save();
    return 0;
  }

  public function admin($action, $type, $type_id, $type_title = '') {
    $item = new History;
    $item->user_id = $_SESSION['user_id'];
    $item->user_name = $_SESSION['name'];
    $item->user_type = 'admin';
    $item->action = $action;
    $item->type = $type;
    $item->type_id = $type_id;
    $item->type_title = $type_title;
    $item->created_at = date('Y-m-d H:i:s');
    $item->updated_at = date('Y-m-d H:i:s');
    $item->save();
    return 0;
  }
}
