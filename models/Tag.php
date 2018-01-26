<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use forxer\Gravatar\Gravatar;

class Tag extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'tag';

  public function store($name) {
    $tag = Tag::where('name', $name)->first();
    if ($tag) return -1;
    $tag = new Tag;
    $tag->name = $name;
    $tag->created_at = date('Y-m-d H:i:s');
    $tag->updated_at = date('Y-m-d H:i:s');
    if ($tag->save()) return $tag->id;
    return 0;
  }

  public function storeListTags($tags) {
    foreach ($tags as $key => $tag_name) {
      $item = Tag::where('name', $tag_name)->first();
      if (!$item) {
        $item = new Tag;
        $item->name = $tag_name;
        $item->created_at = date('Y-m-d H:i:s');
        $item->updated_at = date('Y-m-d H:i:s');
        $item->save();
      }
    }
    return 0;
  }

  public function remove($id) {
    $tag = Tag::find($id);
    if(!$tag) return -2;
    if($tag->delete()) return $tag->id;
    return -3;
  }
}
