<?php
  use Slim\Container as ContainerInterface;
  use \Psr\Http\Message\ServerRequestInterface as Request;
  use \Psr\Http\Message\ResponseInterface as Response;

  class Page extends Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    protected $table = 'page';

    public function fetch($page_number = 1, $perpage = 50) {
      $skip = ($page_number - 1) * $perpage;
      $data = Page::where('status', '!=', 'delete')->orderBy('updated_at', 'desc')->skip($skip)->take($perpage)->get();
      return $data;
    }

    public function store($data) {
      $item = Page::where('title', $data['title'])->first();
      if ($item) return -1;
      $item = new Page;
      $item->title = $data['title'];
      $item->title_en = $data['title_en'];
      $item->handle = $data['handle'];
      $item->handle_en = $data['handle_en'];
      $item->image = $data['image'] ? renameOneImage($data['image'], $item->handle) : '';
      $item->description = $data['description'];
      $item->description_en = $data['description_en'];
      $item->content = $data['content'];
      $item->content_en = $data['content_en'];
      $item->status = $data['status'];
      $item->created_at = date('Y-m-d H:i:s');
      $item->updated_at = date('Y-m-d H:i:s');
      if($item->save()) return $item->id;
      return -3;
    }

    public function update($id, $data) {
      $item = Page::where('title', $data['title'])->where('id', '!=', $id)->first();
      if ($item) return -1;
      $item = Page::find($id);
      if (!$item) return -2;
      $item->title = $data['title'];
      $item->title_en = $data['title_en'];
      $item->handle = $data['handle'];
      $item->handle_en = $data['handle_en'];
      $item->image = $data['image'] ? renameOneImage($data['image'], $item->handle) : '';
      $item->description = $data['description'];
      $item->description_en = $data['description_en'];
      $item->content = $data['content'];
      $item->content_en = $data['content_en'];
      $item->status = $data['status'];
      $item->updated_at = date('Y-m-d H:i:s');
      if($item->save()) return $item->id;
      return -3;
    }

    public function remove($id) {
      $item = Page::find($id);
      if (!$item) return -2;
      $item->status = 'delete';
      $item->save();
      return 0;
    }
  }
?>
