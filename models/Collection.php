<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Collection extends Illuminate\Database\Eloquent\Model {

  public $timestamps = false;
  protected $table = 'collection';

  public function listAll() {
    $data = Collection::where('status', '!=', 'delete')->orderBy('title', 'asc')->get();
    return $data;
  }

  public function store($data) {
    $item = Collection::where('title', $data['title'])->first();
    if ($item) return -1;
    $item = new Collection;
    $item->parent_id = $data['parent_id'] ? $data['parent_id'] : -1;
    $item->title = $data['title'];
    $item->title_en = $data['title_en'] ? $data['title_en'] : '';
    $item->handle = $data['handle'] ? $data['handle'] : createHandle($data['title']);
    $item->handle_en = $data['handle_en'] ? $data['handle_en'] : createHandle($data['title_en']);
    $item->breadcrumb = $data['title'];
    if ($data['parent_id']) {
      $parent = Collection::find($data['parent_id']);
      $item->breadcrumb = $parent->breadcrumb . '/' . $data['title'];
    }
    $item->description = $data['description'];
    $item->description_en = $data['description_en'];
    $item->content = $data['content'];
    $item->content_en = $data['content_en'];
    $item->image = $data['image'] ? renameOneImage($data['image'], 'collection_' . $item->handle) : '';
    $item->status = $data['status'];
    $item->created_at = date('Y-m-d H:i:s');
    $item->updated_at = date('Y-m-d H:i:s');
    if ($item->save()) return $item->id;
    return -3;
  }

  public function update($id, $data) {
    $item = Collection::find($id);
    if (!$item) return -2;
    $check = Collection::where('id', '!=', $id)->where('title', $data['title'])->first();
    if ($check) return -1;
    $item->parent_id = $data['parent_id'] ? $data['parent_id'] : -1;
    $item->title = $data['title'];
    $item->title_en = $data['title_en'] ? $data['title_en'] : '';
    $item->handle = $data['handle'] ? $data['handle'] : createHandle($data['title']);
    $item->handle_en = $data['handle_en'] ? $data['handle_en'] : createHandle($data['title_en']);
    $item->breadcrumb = $data['title'];
    if ($data['parent_id']) {
      $parent = Collection::find($data['parent_id']);
      $item->breadcrumb = $parent->breadcrumb . '/' . $data['title'];
    }
    $item->description = $data['description'];
    $item->description_en = $data['description_en'];
    $item->content = $data['content'];
    $item->content_en = $data['content_en'];
    $item->image = $data['image'] ? renameOneImage($data['image'], 'collection_' . $item->handle) : '';
    $item->status = $data['status'];
    $item->updated_at = date('Y-m-d H:i:s');
    if ($item->save()) return 0;
    return -3;
  }

  public function remove($id) {
    $item = Collection::find($id);
    if (!$item) return -2;
    Collection::where('id', $id)->update(['status' => 'delete']);
    Collection::where('parent_id', $id)->update(['parent_id' => -1]);
    return 0;
  }
}
