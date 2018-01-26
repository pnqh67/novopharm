<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Experience extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'experience';

  public function fetch($page_number = 1, $perpage = 50) {
    $skip = ($page_number - 1) * $perpage;
    $data = Article::orderBy('updated_at', 'desc')->skip($skip)->take($perpage)->get();
    return $data;
  }

  public function store($data) {
    $item = Experience::where('title', $data['title'])->first();
    if ($item) return -1;
    $item = new Experience;
    $item->blog_id = $data['blog_id'] ? $data['blog_id'] : -1;
    $item->title = $data['title'];
    $item->title_en = $data['title_en'];
    $item->handle = $data['handle'];
    $item->handle_en = $data['handle_en'];
    $item->description = $data['description'];
    $item->description_en = $data['description_en'];
    $item->image = $data['image'] ? $data['image'] : '';
    $item->status = $data['status'];
    $item->link = $data['link'];
    $item->view = 0;
    $item->created_at = date('Y-m-d H:i:s');
    $item->updated_at = date('Y-m-d H:i:s');
    if($item->save()) return $item->id;
    return -3;
  }

  public function update($id, $data) {
    $item = Article::where('title', $data['title'])->where('id', '!=', $id)->first();
    if ($item) return -1;
    $item = Article::find($id);
    if (!$item) return -2;
    $item->blog_id = $data['blog_id'] ? $data['blog_id'] : -1;
    $item->title = $data['title'];
    $item->title_en = $data['title_en'];
    $item->handle = $data['handle'];
    $item->handle_en = $data['handle_en'];
    $item->image = $data['image'] ? $data['image'] : '';
    $item->description = $data['description'];
    $item->description_en = $data['description_en'];
    $item->status = $data['status'];
    $item->link = $data['link'];
    $item->updated_at = date('Y-m-d H:i:s');
    if($item->save()) return $item->id;
    return -3;
  }

  public function remove($id) {
    $item = Article::find($id);
    if (!$item) return -2;
    $item->status = 'delete';
    $item->save();
    return 0;
  }
}
