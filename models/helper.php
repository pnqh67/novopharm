<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Connection as DB;

require_once(ROOT . '/controllers/helper.php');
require_once('Collection.php');
require_once('Product.php');
require_once('Blog.php');
require_once('Article.php');
require_once('Page.php');
require_once('User.php');
require_once('Permission.php');
require_once('Location.php');
require_once('Experience.php');

$GLOBALS['size'] = [100, 240, 480, 640, 1024, 2048];


function setMemcached($key, $value, $time = 30 * 24 * 60 * 60) {
  global $memcached;
  if ($memcached) {
    $memcached->set($key, $value, $time);
  }
}

function getSubmenu($menus) {
  foreach ($menus as $key => $menu) {
    $menu->submenu = 0;
    $submenu = Menu::where('status', '!=', 'delete')
      ->where('parent_id', $menu->id)
      ->get();
    if (count($submenu)) {
      $menu->submenu = $submenu;
      getSubmenu($menu->submenu);
    }
  }
  return 0;
}

function getSubmenuUser($menus) {
  foreach ($menus as $key => $menu) {
    $menu->submenu = 0;
    $submenu = Menu::where('status', 'active')
      ->where('parent_id', $menu->id)
      ->get();
    if (count($submenu)) {
      $menu->submenu = $submenu;
      getSubmenu($menu->submenu);
    }
  }
  return 0;
}

function getMemcached($key) {
  global $memcached;
  if ($memcached) return $memcached->get($key);
  return FALSE;
}

function clearAllMemcached() {
  global $memcached;
  $memcached->flush();
}

function getTitleFromHandle($handle) {
  $obj = Collection::where('handle', '=', $handle)->first();
  if ($obj) return $obj['title'];
  $obj = Product::where('handle', '=', $handle)->first();
  if ($obj) return $obj['title'];
  return '';
}

function convertHandle($handle, $from, $to) {
  if ($handle == '') return '';
  if ($handle == 'en' || $handle == 'vi') return $to;
  if ($from == $to) return $handle;

  if ($handle == 'blog' && $to == 'vi') return $handle = 'nhom-bai-viet';
  if ($handle == 'nhom-bai-viet' && $to == 'en') return $handle = 'blog';

  if ($handle == 'article' && $to == 'vi') return $handle = 'bai-viet';
  if ($handle == 'bai-viet' && $to == 'en') return $handle = 'article';

  if ($handle == 'page' && $to == 'vi') return $handle = 'trang-noi-dung';
  if ($handle == 'trang-noi-dung' && $to == 'en') return $handle = 'page';

  if ($handle == 'nhom-san-pham' && $to == 'en') return $handle = 'collection';
  if ($handle == 'collection' && $to == 'vi') return $handle = 'nhom-san-pham';

  if ($handle == 'product' && $to == 'vi') return $handle = 'san-pham';
  if ($handle == 'san-pham' && $to == 'en') return $handle = 'product';

  if ($handle == 'experience' && $to == 'vi') return $handle = 'kinh-nghiem-su-dung';
  if ($handle == 'kinh-nghiem-su-dung' && $to == 'en') return $handle = 'experience';

  if ($handle == 'acount' && $to == 'vi') return $handle = 'tai-khoan';
  if ($handle == 'tai-khoan' && $to == 'en') return $handle = 'acount';

  if ($handle == 'antibiotic' && $to == 'vi') return $handle = 'benh-ho-hap';
  if ($handle == 'benh-ho-hap' && $to == 'en') return $handle = 'antibiotic';

  if ($handle == 'antibiotic' && $to == 'vi') return $handle = 'khang-sinh';
  if ($handle == 'khang-sinh' && $to == 'en') return $handle = 'antibiotic';

  if ($handle == 'respiratory' && $to == 'vi') return $handle = 'benh-ho-hap';
  if ($handle == 'benh-ho-hap' && $to == 'en') return $handle = 'respiratory';

  if ($handle == 'order' && $to == 'vi') return $handle = 'quan-ly-don-hang';
  if ($handle == 'quan-ly-don-hang' && $to == 'en') return $handle = 'order';

  if ($handle == 'favorite' && $to == 'vi') return $handle = 'yeu-thich';
  if ($handle == 'yeu-thich' && $to == 'en') return $handle = 'favorite';

  if ($handle == 'cart' && $to == 'vi') return $handle = 'gio-hang';
  if ($handle == 'gio-hang' && $to == 'en') return $handle = 'cart';

  if ($handle == 'order-success' && $to == 'vi') return $handle = 'dat-hang-thanh-cong';
  if ($handle == 'dat-hang-thanh-cong' && $to == 'en') return $handle = 'order-success';

  if ($handle == 'checkout' && $to == 'vi') return $handle = 'kiem-tra-gio-hang';
  if ($handle == 'kiem-tra-gio-hang' && $to == 'en') return $handle = 'checkout';

  if ($handle == 'search' && $to == 'vi') return $handle = 'tim-kiem';
  if ($handle == 'tim-kiem' && $to == 'en') return $handle = 'search';

  $vi_field = 'handle';
  $field = $vi_field . ($to == 'vi' ? '' : '_' . $to);

  $obj = Collection::where('handle', '=', $handle)->orWhere('handle_en', '=', $handle)->first();
  if ($obj) {
    return $obj[$field] == '' ? $obj[$vi_field] : $obj[$field];
  }

  $obj = Blog::where('handle', '=', $handle)->orWhere('handle_en', '=', $handle)->first();
  if ($obj) {
    return $obj[$field] == '' ? $obj[$vi_field] : $obj[$field];
  }

  $obj = Article::where('handle', '=', $handle)->orWhere('handle_en', '=', $handle)->first();
  if ($obj) {
    return $obj[$field] == '' ? $obj[$vi_field] : $obj[$field];
  }

  $obj = Product::where('handle', '=', $handle)->orWhere('handle_en', '=', $handle)->first();
  if ($obj) {
    return $obj[$field] == '' ? $obj[$vi_field] : $obj[$field];
  }

  $obj = Page::where('handle', '=', $handle)->orWhere('handle_en', '=', $handle)->first();
  if ($obj) {
    return $obj[$field] == '' ? $obj[$vi_field] : $obj[$field];
  }
  return $handle;
}

function createHandle($str) {
  $str = trim($str);
  $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
  $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
  $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
  $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
  $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
  $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
  $str = preg_replace("/(đ)/", 'd', $str);
  $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
  $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
  $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
  $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
  $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
  $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
  $str = preg_replace("/(Đ)/", 'D', $str);
  $str = str_replace(' ', '-', $str);
  $str = str_replace('.', '-', $str);
  $str = strtolower($str);
  $str = preg_replace('/[^A-Za-z0-9-]+/', '-', $str);
  $str = str_replace('--', '-', $str);
  $str = str_replace('--', '-', $str);
  if (substr($str, -1) == '-') $str = substr($str, 0, -1);
  return $str;
}

function removeImage($name) {
  $path = ROOT . '/public/uploads/';
  unlink($path . $name);
  global $size;
  for ($i = 0; $i < count($size); $i++) {
    $img = convertImage($name, $size[$i]);
    unlink($path . $img);
  }
}

function uploadImage(Request $request, Response $response) {
  if (!is_dir(ROOT . '/public/uploads')) {
    mkdir(ROOT . '/public/uploads');
  }
  $path = ROOT . '/public/uploads/';
  if (!is_dir(ROOT . '/public/uploads/origin')) {
    mkdir(ROOT . '/public/uploads/origin');
  }
  $originPath = ROOT . '/public/uploads/origin/';
  $result = array();
  $total = count($_FILES['upload']['name']);
  global $size;
  for ($i = 0; $i < $total; $i++) {
    $tmp_name = $_FILES['upload']['tmp_name'][$i];
    $origin = $_FILES['upload']['name'][$i];
    $newName = time() . '_' . $origin;
    $newFilePath = $path . $newName;
    if (moveAndReduceSize($tmp_name, $newFilePath, 70)) {
      array_push($result, $newName);
      for ($j = 0; $j < count($size); $j++) {
        moveAndReduceSize($tmp_name, $newFilePath, 70, $size[$j]);
      }
      $img_2048 = convertImage($newFilePath, 2048);
      rename($img_2048, $newFilePath);
      copy($newFilePath, $originPath . $newName);
    }
  }
  if (count($result)) {
    return $response->withJson([
      'code' => 0,
      'data' => $result
    ]);
  }
  return $response->withJson([
    'code' => -1,
    'message' => 'error'
  ]);
}

function moveAndReduceSize($srcFilePath, $outFilePath, $quality, $size = NULL) {
  list($width, $height) = getimagesize($srcFilePath);
  if (isset($size) && $size) {
    $scale = min($size / $width, $size / $height);
    $newWidth = ceil($scale * $width);
    $newHeight = ceil($scale * $height);
    if ($width < $newWidth || $height < $newHeight) {
      $newWidth = $width;
      $newHeight = $height;
    }
    $outFilePath = convertImage($outFilePath, $size);
  } else {
    $newWidth = $width;
    $newHeight = $height;
  }
  $mime = mime_content_type($srcFilePath);
  $mime = strtolower($mime);
  $thumb = imagecreatetruecolor($newWidth, $newHeight);
  $support = TRUE;
  if ($mime == "image/jpeg") $source = imagecreatefromjpeg($srcFilePath);
  else if ($mime == "image/gif") $source = imagecreatefromgif($srcFilePath);
  else if ($mime == "image/png") {
    $source = imagecreatefrompng($srcFilePath);
    imagealphablending($thumb, FALSE);
    imagesavealpha($thumb, TRUE);
    $transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
    imagefilledrectangle($thumb, 0, 0, $newWidth, $newHeight, $transparent);
  } else $support = FALSE;
  if ($support) {
    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    if ($mime == "image/jpeg") imagejpeg($thumb, $outFilePath, $quality);
    else if ($mime == "image/png") imagepng($thumb, $outFilePath, floor(($quality - 1) / 10));
    else if ($mime == "image/gif") imagegif($thumb, $outFilePath);
  }
  return TRUE;
}

function convertImage($file, $size) {
  $temp = explode('.', $file);
  $extension = end($temp);
  $new = str_replace('.' . $extension, '_' . $size . '.' . $extension, $file);
  return $new;
}

function uploadImageTinymce(Request $request, Response $response) {
  $result = array();
  $total = count($_FILES['upload']['name']);
  for ($i = 0; $i < $total; $i++) {
    $tmp_name = $_FILES['upload']['tmp_name'][$i];
    $new_name = time() . '-' . $_FILES['upload']['name'][$i];
    $path = ROOT . '/public/images/' . $new_name;
    if ($tmp_name != "") {
      if (move_uploaded_file($tmp_name, $path)) {
        array_push($result, $new_name);
      }
    }
  }
  if ($result) {
    return $response->withJson(array(
      'code' => 0,
      'data' => $result
    ));
  }
  return $response->withJson(array(
    'code' => -1,
    'message' => 'Error'
  ));
}

function renameOneImage($image, $handle) {
  $arr = explode('.', $image);
  $ext = end($arr);
  $name = str_replace('.' . $ext, '', $image);
  $path = ROOT . '/public/uploads/';
  $origin = ROOT . '/public/uploads/origin/';
  rename($path . $name . '.' . $ext, $path . $handle . '.' . $ext);
  rename($origin . $name . '.' . $ext, $origin . $handle . '.' . $ext);
  global $size;
  for ($i = 0; $i < count($size); $i++) {
    rename($path . $name . '_' . $size[$i] . '.' . $ext, $path . $handle . '_' . $size[$i] . '.' . $ext);
  }
  return $handle . '.' . $ext;
}

function renameListImage($list_image, $handle) {
  $new_list_image = array();
  $count = 0;
  foreach ($list_image as $key => $image) {
    if ($image) {
      $arr = explode('.', $image);
      $ext = end($arr);
      $name = str_replace('.' . $ext, '', $image);
      $path = ROOT . '/public/uploads/';
      $count++;
      global $size;
      if ($count > 1) {
        rename($path . $name . '.' . $ext, $path . $handle . '-' . $count . '.' . $ext);
        for ($i = 0; $i < count($size); $i++) {
          rename($path . $name . '_' . $size[$i] . '.' . $ext, $path . $handle . '-' . $count . '_' . $size[$i] . '.' . $ext);
        }
        array_push($new_list_image, $handle . '-' . $count . '.' . $ext);
      } else {
        rename($path . $name . '.' . $ext, $path . $handle . '.' . $ext);
        for ($i = 0; $i < count($size); $i++) {
          rename($path . $name . '_' . $size[$i] . '.' . $ext, $path . $handle . '_' . $size[$i] . '.' . $ext);
        }
        array_push($new_list_image, $handle . '.' . $ext);
      }
    }
  }
  return $new_list_image;
}

function rotate($input) {
  header('Content-type: image/jpeg');
  if (file_exists($input)) {
    try {
      $source = imagecreatefromjpeg($input);
      $degrees = 90;
      $rotate = imagerotate($source, $degrees, 0);
      imagejpeg($rotate, $input);
    } catch (Exception $e) {
    }
  }
  imagedestroy($source);
  imagedestroy($rotate);
}

function SiteMap() {
  $path = ROOT . '/public/';
  $sitemap = new Sitemap(HOST);
  $filename = 'sitemap_products_1';
  unlink($path . $filename . 'xml');
  $sitemap->setPath($path);
  $sitemap->setFilename($filename);
  $sitemap->addItem('/', '1.0', 'Daily');
  $Product = Product::where('status', 'active')->orderBy('updated_at', 'desc')->get();
  foreach ($Product as $key => $product) {
    $sitemap->addItem('/san-pham/' . $product['handle'], '1.0', 'Daily', $product['updated_at']);
  }
  $sitemap->createSitemapIndex($path, 'Today');
  unlink($path . $filename . '-index.xml');

  $sitemap = new Sitemap(HOST);
  $filename = 'sitemap_collections_1';
  unlink($path . $filename . 'xml');
  $sitemap->setPath($path);
  $sitemap->setFilename($filename);
  $sitemap->addItem('/', '1.0', 'Daily');
  $collections = Collection::all();
  foreach ($collections as $key => $collection) {
    $link = str_replace(',', '/', $collection['link']);
    $sitemap->addItem('/' . $link, '0.9', 'Daily', $collection['updated_at']);
  }
  $sitemap->createSitemapIndex($path, 'Today');
  unlink($path . $filename . '-index.xml');

  $sitemap = new Sitemap(HOST);
  $filename = 'sitemap_news_1';
  unlink($path . $filename . 'xml');
  $sitemap->setPath($path);
  $sitemap->setFilename($filename);
  $sitemap->addItem('/', '1.0', 'Daily');
  $articles = Article::where('display', 1)->get();
  foreach ($articles as $key => $article) {
    $sitemap->addItem('/article/' . $article['handle'] . '-' . $article['id'], '0.9', 'Daily', $article['updated_at']);
  }
  $sitemap->createSitemapIndex($path, 'Today');
  unlink($path . $filename . '-index.xml');
}

function createSitemap() {
  SiteMap();
  global $HOST;
  $domtree = new DOMDocument('1.0', 'UTF-8');
  $domtree->preserveWhiteSpace = FALSE;
  $domtree->formatOutput = TRUE;
  /* Create attribute */
  $domAttribute = $domtree->createAttribute('xmlns');
  /* Value for the created attribute */
  $domAttribute->value = 'http://www.sitemaps.org/schemas/sitemap/0.9';

  /* Create the root element of the xml tree */
  $xmlRoot = $domtree->createElement("sitemapindex");
  $xmlRoot->appendChild($domAttribute);
  /* Append it to the document created */
  $xmlRoot = $domtree->appendChild($xmlRoot);

  $sitemaps = array('products', 'collections', 'news');
  $prefix = '/sitemap_';
  $extension = '_1.xml';
  foreach ($sitemaps as $key => $sitemap) {
    $currentSitemap = $domtree->createElement("sitemap");
    $currentSitemap = $xmlRoot->appendChild($currentSitemap);
    $currentSitemap->appendChild($domtree->createElement('loc', $HOST . $prefix . $sitemap . $extension));
  }

  $xml_out = $domtree->saveXML($domtree->documentElement);
  $file = fopen(ROOT . '/public/sitemap.xml', "w");
  if (fwrite($file, $xml_out) !== FALSE) {
    $link = HOST . '/sitemap.xml';
    echo "Success: <a href=" . $link . ">" . $link . "</a>";
  } else echo "An error occured, please try again later";
  fclose($file);
}

function updateStock($product_id) {
  $product = Product::find($product_id);
  if (!$product->inventory_management) {
    $product->in_stock = 1;
  } else {
    $check = Variant::where('product_id', $product_id)->where('inventory', '>', 0)->count();
    $product->in_stock = $check ? 1 : 0;
  }
  $product->save();
}

function smartSearch(Request $request, Response $response) {
  $query = $request->getQueryParams();
  $products = Product::where('title', 'LIKE', '%' . $query['q'] . '%')->where('status', 'active')->skip(0)->take(5)->orderBy('updated_at', 'desc')->get();
  if (count($products)) {
    return $response->withJson(array(
      "code" => 0,
      "data" => $products
    ));
  }
  return $response->withJson(array(
    "code" => -1,
    "message" => "Product not available"
  ));
}

function checkInventoryManagent($variant_id, $quantity) {
  $variant = Variant::find($variant_id);
  $product = Product::find($variant->product_id);
  if (!$product->inventory_management) return TRUE;
  if ($variant->inventory >= $quantity) return TRUE;
  return FALSE;
}

function getSubRegion(Request $request, Response $response) {
  $query = $request->getQueryParams();
  $region_id = $query['region_id'];
  $subregion = SubRegion::where('region_id', $region_id)->orderBy('name', 'asc')->get();
  $price_suburban = Meta::where('key', 'price_suburban')->first();
  $price_suburban = (int)$price_suburban->value;
  $price_urban = Meta::where('key', 'price_urban')->first();
  $price_urban = (int)$price_urban->value;
  foreach ($subregion as $key => $value) {
    $value->shipping_price = $price_suburban;
    if ($value->urban) $value->shipping_price = $price_urban;
  }
  return $response->withJson(array(
    'code' => 0,
    'data' => $subregion
  ));
}

function rotateImage(Request $request, Response $response) {
  $query = $request->getQueryParams();
  $file = $query['filename'];
  $path = ROOT . '/public/uploads/';
  $input = $path . $file;
  rotate($input);

  global $size;
  for ($i = 0; $i < count($size); $i++) {
    $img = convertImage($file, $size[$i]);
    rotate($path . $img);
  }
  return convertImage($file, $size[0]);
}

function PHPMailer($to, $subject, $body) {
  $mail = new PHPMailer();
  include ROOT . '/framework/phpmailer.php';
  $mail->IsSMTP();
  $mail->SMTPDebug = TRUE;
  $mail->SMTPAuth = TRUE;
  $mail->Host = $STMP_HOST;
  $mail->Username = $STMP_USERNAME;
  $mail->Password = $STMP_PASSWORD;
  $mail->SMTPSecure = $STMP_SECURE;
  $mail->Port = $STMP_PORT;
  $mail->setFrom($STMP_USERNAME, 'Admin');
  $mail->addAddress($to);
  $mail->isHTML(TRUE);
  $mail->Subject = $subject;
  $mail->msgHTML($body);
  $mail->AltBody = '';
  $mail->CharSet = "UTF-8";

  if (!$mail->send()) return $mail->ErrorInfo;
  return TRUE;
}

function randomString($length = 50) {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $charactersLength = strlen($characters);
  $random = '';
  for ($i = 0; $i < $length; $i++) {
    $random .= $characters[rand(0, $charactersLength - 1)];
  }
  return $random;
}

function sendEmailUser($id) {
  $user = User::find($id);
  if (!$user) return FALSE;
  $to = $user->email;
  $subject = 'THÔNG TIN TÀI KHOẢN NGƯỜI DÙNG';
  $variables = array();
  $variables['website'] = 'EYE SOLUTION';
  $variables['user_name'] = $user->name;
  $variables['user_email'] = $user->email;
  $variables['user_role'] = ($user->role == 'admin') ? 'Administrator' : 'User';
  $variables['link_create_password'] = HOST . '/user/' . $user->random;
  $body = file_get_contents(ROOT . '/framework/mail-template/register-admin.html');
  foreach ($variables as $key => $value) {
    $body = str_replace('{{' . $key . '}}', $value, $body);
  }
  PHPMailer($to, $subject, $body);
}

function sendEmailForgotPassword($email) {
  $user = User::where('email', $email)->first();
  if (!$user) return FALSE;
  $to = $email;
  $subject = 'THÔNG TIN TÀI KHOẢN NGƯỜI DÙNG';
  $variables = array();
  $variables['website'] = 'EYE SOLUTION';
  $variables['user_name'] = $user->name;
  $variables['user_email'] = $email;
  $variables['user_role'] = ($user->role == 'admin') ? 'Administrator' : 'User';
  $variables['link_create_password'] = HOST . '/user/' . $user->random;
  $body = file_get_contents(ROOT . '/framework/mail-template/forget-password.html');
  foreach ($variables as $key => $value) {
    $body = str_replace('{{' . $key . '}}', $value, $body);
  }
  PHPMailer($to, $subject, $body);
}


function checkHandle(Request $request, Response $response) {
  $params = $request->getQueryParams();
  $handle = $params['handle'];
  $type = $params['type'];
  $id = $params['id'];
  $parent_id = $params['parent_id'];
  if ($type == 'product') {
    $query = Product::where('handle', $handle);
  } else if ($type == 'collection') {
    if (isset($parent_id) && $parent_id) {
      $parent = Collection::find($parent_id);
      $handle = $parent->handle . '/' . $handle;
    }
    $query = Collection::where('handle', $handle);
    $temp = $query->first();
  } else if ($type == 'blog') {
    $query = Blog::where('handle', $handle);
  } else if ($type == 'article') {
    $query = Article::where('handle', $handle);
  } else if ($type == 'page') {
    $query = Page::where('handle', $handle);
  }
  if (isset($id) && $id) $query = $query->where('id', '!=', $id);
  if ($query->first()) return $handle . '-1';
  return $handle;
}

function updateStatus(Request $request, Response $response) {
  $result = $request->getParsedBody();
  $type = $result['type'];
  $arrId = $result['arrId'];
  $status = $result['status'];
  switch ($type) {
    case 'product':
      Product::whereIn('id', $arrId)->update(['status' => $status]);
      break;
    case 'article':
      Article::whereIn('id', $arrId)->update(['status' => $status]);
      break;
    case 'collection':
      Collection::whereIn('id', $arrId)->update(['status' => $status]);
      break;
    case 'coupon':
      Coupon::whereIn('id', $arrId)->update(['status' => $status]);
      break;
    case 'sale':
      Sale::whereIn('id', $arrId)->update(['status' => $status]);
      break;
    case 'product_buy_together':
      ProductBuyTogether::whereIn('id', $arrId)->update(['status' => $status]);
      break;
    case 'contact':
      Contact::whereIn('id', $arrId)->update(['status' => $status]);
      break;
    case 'blog':
      Blog::whereIn('id', $arrId)->update(['status' => $status]);
      break;
    case 'page':
      Page::whereIn('id', $arrId)->update(['status' => $status]);
      break;
    case 'comment':
      Comment::whereIn('id', $arrId)->update(['status' => $status]);
      break;
    case 'contact':
      Contact::whereIn('id', $arrId)->update(['status' => $status]);
      break;
    case 'shipping_fee':
      ShippingFeeRegion::whereIn('id', $arrId)->update(['status' => $status]);
      break;
    case 'order':
      Order::whereIn('id', $arrId)->update(['status' => $status]);
      break;
    case 'slider':
      Slider::whereIn('id', $arrId)->update(['status' => $status]);
      break;
    case 'menu':
      Menu::whereIn('id', $arrId)->update(['status' => $status]);
      break;
    case 'location':
      Location::whereIn('id', $arrId)->update(['status' => $status]);
      break;
    case 'experience':
      Experience::whereIn('id', $arrId)->update(['status' => $status]);
      break;
  }
}

function checkPermission($user, $method, $uri) {
  if (!$user->role_id) return 1;
  $index = strpos($uri, '/admin');
  $endpoint = substr($uri, $index + 6, strlen($uri) - 1);
  $check = User::join('role', 'role.id', '=', 'user.role_id')->join('permission', 'permission.role_id', '=', 'role.id')
    ->where('user.id', $user->id)->where('permission.method', $method)->where('permission.endpoint', $endpoint)->first();
  if ($check) return 1;
  return 0;
}

function multi_explode($delimiters = array(), $string = '') {
  foreach ($delimiters as $delimiter) {
    $array = explode($delimiter, $string);
  }
  return $array;
}

function explodeX($delimiters, $string) {
  $return_array = Array($string); // The array to return
  $d_count = 0;
  while (isset($delimiters[$d_count])) // Loop to loop through all delimiters
  {
    $new_return_array = Array();
    foreach ($return_array as $el_to_split) // Explode all returned elements by the next delimiter
    {
      $put_in_new_return_array = explode($delimiters[$d_count], $el_to_split);
      foreach ($put_in_new_return_array as $substr) // Put all the exploded elements in array to return
      {
        $new_return_array[] = $substr;
      }
    }
    $return_array = $new_return_array; // Replace the previous return array by the next version
    $d_count++;
  }
  return $return_array; // Return the exploded elements

}