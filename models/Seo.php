<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Seo extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'seo';

  public function createOrUpdate($type, $type_id, $data) {
    $item = Seo::where('type', $type)->where('type_id', $type_id)->first();
    if ($item) {
      $item->g_meta_title = $data['g_meta_title'];
      $item->g_meta_title_en = $data['g_meta_title_en'];
      $item->g_meta_description = $data['g_meta_description'];
      $item->g_meta_description_en = $data['g_meta_description_en'];
      $item->g_meta_keyword = $data['g_meta_keyword'];
      $item->g_meta_keyword_en = $data['g_meta_keyword_en'];
      $item->g_meta_robots = $data['g_meta_robots'];

      $item->f_meta_title = $data['f_meta_title'];
      $item->f_meta_title_en = $data['f_meta_title_en'];
      $item->f_meta_description = $data['f_meta_description'];
      $item->f_meta_description_en = $data['f_meta_description_en'];
      $item->f_image = $data['f_image'];
      $item->type = $type;
      $item->type_id = $type_id;
      $item->updated_at = date('Y-m-d H:i:s');
      $item->save();
      return 0;
    } else {
      $item = new Seo;
      $item->g_meta_title = $data['g_meta_title'];
      $item->g_meta_title_en = $data['g_meta_title_en'];
      $item->g_meta_description = $data['g_meta_description'];
      $item->g_meta_description_en = $data['g_meta_description_en'];
      $item->g_meta_keyword = $data['g_meta_keyword'];
      $item->g_meta_keyword_en = $data['g_meta_keyword_en'];
      $item->g_meta_robots = $data['g_meta_robots'];

      $item->f_meta_title = $data['f_meta_title'];
      $item->f_meta_title_en = $data['f_meta_title_en'];
      $item->f_meta_description = $data['f_meta_description'];
      $item->f_meta_description_en = $data['f_meta_description_en'];
      $item->f_image = $data['f_image'];
      $item->type = $type;
      $item->type_id = $type_id;
      $item->created_at = date('Y-m-d H:i:s');
      $item->updated_at = date('Y-m-d H:i:s');
      $item->save();
      return $item->id;
    }
    return -3;
  }

  public function store($type, $type_id, $data) {
    $item = Seo::where('type', $type)->where('type_id', $type_id)->first();
    if (!$item) {
      $item = new Seo;
      $item->g_meta_title = $data['g_meta_title'];
      $item->g_meta_title_en = $data['g_meta_title_en'];
      $item->g_meta_description = $data['g_meta_description'];
      $item->g_meta_description_en = $data['g_meta_description_en'];
      $item->g_meta_keyword = $data['g_meta_keyword'];
      $item->g_meta_keyword_en = $data['g_meta_keyword_en'];
      $item->g_meta_robots = $data['g_meta_robots'];

      $item->f_meta_title = $data['f_meta_title'];
      $item->f_meta_title_en = $data['f_meta_title_en'];
      $item->f_meta_description = $data['f_meta_description'];
      $item->f_meta_description_en = $data['f_meta_description_en'];
      $item->f_image = $data['f_image'];
      $item->type = $type;
      $item->type_id = $type_id;
      $item->created_at = date('Y-m-d H:i:s');
      $item->updated_at = date('Y-m-d H:i:s');
      $item->save();
      return 0;
    }
    return -3;
  }

  public function update($type, $type_id, $data) {
    $item = Seo::where('type', $type)->where('type_id', $type_id)->first();
    if (!$item) return -2;
    $item->g_meta_title = $data['g_meta_title'];
    $item->g_meta_title_en = $data['g_meta_title_en'];
    $item->g_meta_description = $data['g_meta_description'];
    $item->g_meta_description_en = $data['g_meta_description_en'];
    $item->g_meta_keyword = $data['g_meta_keyword'];
    $item->g_meta_keyword_en = $data['g_meta_keyword_en'];
    $item->g_meta_robots = $data['g_meta_robots'];

    $item->f_meta_title = $data['f_meta_title'];
    $item->f_meta_title_en = $data['f_meta_title_en'];
    $item->f_meta_description = $data['f_meta_description'];
    $item->f_meta_description_en = $data['f_meta_description_en'];
    $item->f_image = $data['f_image'];
    $item->type = $type;
    $item->type_id = $type_id;
    $item->updated_at = date('Y-m-d H:i:s');
    $item->save();
    return 0;
  }

  public function get($type, $type_id) {
    $item = Seo::where('type', $type)->where('type_id', $type_id)->first();
    return $item;
  }

  public function remove($type, $type_id) {
    $item = Seo::where('type', $type)->where('type_id', $type_id)->first();
    if (!$item) return -2;
    $item->delete();
    return 0;
  }
}
