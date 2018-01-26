<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Image extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'image';

  public function getImage($type, $type_id) {
    $image = Image::where('type', $type)->where('type_id', $type_id)->get();
    return $image;
  }

  public function store($name, $type, $type_id) {
    $item = Image::where('name', $name)->where('type', $type)->where('type_id', $type_id)->first();
    if (!$item) {
      $image = new Image;
      $image->name = $name;
      $image->type = $type;
      $image->type_id = $type_id;
      $image->created_at = date('Y-m-d H:i:s');
      $image->updated_at = date('Y-m-d H:i:s');
      if ($image->save()) return 0;
      return -3;
    }
    return -1;
  }

  public function remove($type_id) {
    $images = Image::where('type_id', $type_id)->get();
    if (count($images)) return -2;
    foreach ($images as $key => $value) {
      removeImage($value->name);
      Image::where('id', $value->id)->delete();
    }
    return 0;
  }

  public function removeImage($id, $type_id) {
    $image = Image::where('type_id', $type_id)->where('id', $id)->first();
    if (count($image)) return -2;
    removeImage($image->name);
    if(Image::where('type_id', $type_id)->where('id', $id)->delete()) {
      return 0;
    }
    return -3;
  }
}
