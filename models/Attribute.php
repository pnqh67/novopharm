<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Attribute extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'attribute';

  public function fetch($page =  1, $perpage = 100) {
    $skip = ($page - 1) * $perpage;
    $data = Attribute::where('parent_id', -1)->skip($skip)->take($perpage)->get();
    foreach ($data as $key => $value) {
      $id = $value->id;
      $child = Attribute::where('parent_id', $id)->where('parent_id', '!=', -1)->get();
      $value->child = 0;
      if (count($child)) $value->child = $child;
    }
    return $data;
  }

  public function get($id) {
    $data = Attribute::find($id);
    if ($data) return $data;
    return -2;
  }

  public function store($data) {
    if ($data['parent_id']) {
      $item = Attribute::where('name', $data['name'])->where('parent_id', '!=', -1)->first();
    } else {
      $item = Attribute::where('name', $data['name'])->where('parent_id', -1)->first();
    }
    if ($item) return -1;
    $item = new Attribute;
    $item->parent_id = $data['parent_id'] ? $data['parent_id'] : -1;
    $item->name = $data['name'];
    $item->name_en = $data['name_en'] ? $data['name_en'] : '';
    $item->option = $data['option'] ? $data['option'] : '';
    $item->option_en = $data['option_en'] ? $data['option_en'] : '';
    $item->created_at = date('Y-m-d H:i:s');
    $item->updated_at = date('Y-m-d H:i:s');
    if ($item->save()) return $item->id;
    return -3;
  }

  public function update($id, $data) {
    $item = Attribute::where('name', $data['name'])->where('id', '!=', $id)->first();
    if ($item) return -1;
    $item = Attribute::find($id);
    if (!$item) return -2;
    $item->name = $data['name'];
    $item->name_en = $data['name_en'] ? $data['name_en'] : '';
    $item->option = $data['option'] ? $data['option'] : '';
    $item->option_en = $data['option_en'] ? $data['option_en'] : '';
    $item->updated_at = date('Y-m-d H:i:s');
    if ($item->save()) return 0;
    return -3;
  }

  public function remove($id) {
    $attr = Attribute::find($id);
    if (!$attr) return -2;
    if ($attr->delete()) return 0;
    return -3;
  }

  public function listAttributeProduct() {
    $attribute = Attribute::where('parent_id', -1)->get();
    return $attribute;
  }

}
