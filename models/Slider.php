<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Slider extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'slider';

  public function store($data) {
    $item = Slider::where('title', $data['title'])->first();
    if ($item) return -1;
    $item = new Slider;
    $item->parent_id = $data['parent_id'];
    $item->title = $data['title'];
    $item->title_en = $data['title_en'];
    $item->handle = $data['handle'];
    $item->description = $data['description'];
    $item->description_en = $data['description_en'];
    $item->image = $data['image'];
    $item->link = $data['link'];
    $item->status = 'active';
    $item->created_at = date('Y-m-d H:i:s');
    $item->updated_at = date('Y-m-d H:i:s');
    if($item->save()) return $item->id;
    return -3;
  }

  public function update($data, $id) {
    $item = Slider::find($id);
    if (!$item) return -2;
    $item->parent_id = $data['parent_id'];
    $item->title = $data['title'];
    $item->title_en = $data['title_en'];
    $item->handle = $data['handle'];
    $item->description = $data['description'];
    $item->description_en = $data['description_en'];
    $item->image = $data['image'];
    $item->link = $data['link'];
    $item->status = 'active';
    $item->updated_at = date('Y-m-d H:i:s');
    if($item->save()) return $item->id;
    return -3;
  }


}
