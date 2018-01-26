<?php
session_start();

if (!isset($_SESSION['lang'])) $_SESSION['lang'] = "vi";
$langCode = $_SESSION['lang'];
$path = ROOT . "/languages/lang." . $langCode . ".php";
require_once($path);

function __($key) {
  global $lang;
  return $lang[$key];
}

function currentLang() {
  global $langCode;
  return $langCode;
}

function currentENV() {
  $env = 'development';
  if (getenv('ENV') && getenv('ENV') == 'production') {
    $env = 'production';
  }
  return $env;
}

function getThemeDir() {
  return $GLOBALS['config']['themeDir'];
}

function themeURI() {
  return '/themes/' . $GLOBALS['config']['themeDir'];
}

function staticURI() {
  return '/static';
}

function getMeta($key) {
  $meta = Meta::where('key', $key)->first();
  return $meta->value;
}

function getNameAttribute($id){
  $attribute = Attribute::find($id);
  return $attribute->name;
}

function fetchCart() {
  $cart = $_SESSION['cart'];
  $total = 0;
  foreach ($cart as $key => $value) {
    $variant = Variant::where('id', $value->variant_id)->first();
    $product = Product::where('id', $variant->product_id)->first();
    $image = Image::where('type', 'variant')->where('type_id', $variant->id)->first();
    $value->title = $product->title;
    $value->variant = $variant->title;
    $value->handle = $product->handle;
    $value->price = $variant->price;
    $value->variant_id = $variant->id;
    $value->image = $image->name;
    $value->subTotal = (int)$variant->price * (int)$value->quantity;
    $total += $value->subTotal;
  }
  $cart['total_price'] = $total;
  return $cart;
}

function getSlider($handle) {
  if (getMemcached($handle . '_slider')) return getMemcached($handle . '_slider');
  $temp = Slider::where('status', 'active')
    ->where('parent_id', '-1')
    ->where('handle', $handle)
    ->first();
  $slider = Slider::where('status', 'active')
    ->where('parent_id', $temp->id)
    ->get();
  setMemcached($handle . '_slider', $slider);
  return $slider;
}

function getMenu($handle) {
  if (getMemcached($handle . '_menu')) return getMemcached($handle . '_menu');
  $temp = Menu::where('status', 'active')
    ->where('handle', $handle)
    ->first();
  $menus = Menu::where('status', 'active')
    ->where('parent_id', $temp->id)
    ->get();
  getSubmenuUser($menus);
  setMemcached($handle . '_menu', $menus);
  return $menus;
}

function inInventory($productId) {
  $inventory = Variant::where('product_id', $productId)->select('inventory')->get();
  foreach ($inventory as $key => $value) {
    if ($value->inventory > 0) return 1;
  }
  return 0;
}

function listBlog() {
  $blogs = Blog::where('status', 'active')->get();
  return $blogs;
}

function listCollection() {
  $collections = Collection::where('status', 'active')->get();
  return $collections;
}

function listArticles($blogId, $pageNumber) {
  $perPage = 2;
  $skip = ($pageNumber - 1) * $perPage;
  $countArticles = Article::where('blog_id', $blogId)->count();
  $articles = Article::where('blog_id', $blogId)->skip($skip)->take($perPage)->orderBy('updated_at', 'desc')->get();
  $totalPages = ceil($countArticles / $perPage);
  $articles->total_pages = $totalPages;
  return $articles;
}

function getHotArticle($id) {
  $data = Article::where('id', '!=', $id)->orderBy('view', 'desc')->orderBy('updated_at', 'desc')->take(5)->get();
  return $data;
}

function getRelatedArticle($articleId) {
  $blogId = Article::join('blog_article', 'article.id', '=', 'blog_article.article_id')->where('blog_article.article_id', $articleId)->first();
  $related_article = Article::join('blog_article', 'article.id', '=', 'blog_article.article_id')->where('blog_article.blog_id', $blogId->blog_id)->where('blog_article.article_id', '!=', $articleId)->get();
  return $related_article;
}

function ddMMYYYY($datetime) {
  return date("d-m-Y", strtotime($datetime));
}

function datetime($datetime) {
  $time = strtotime($datetime);
  return date('d-m-Y H:i:s');
}

function getPriceFilter() {
  $price = Price::all();
  $arr = array();
  for ($i = 0; $i < count($price); $i++) {
    $obj = new stdClass();
    if (!$i) {
      $obj->title = 'Dưới ' . money($price[$i]['price']);
      $obj->value = '(<' . $price[$i]['price'] . ')';
    } else if ($i == count($price) - 1) {
      $obj->title = 'Trên ' . money($price[$i]['price']);
      $obj->value = '(>' . $price[$i]['price'] . ')';
    } else {
      $obj->title = 'Từ ' . money($price[$i]['price']) . ' - ' . money($price[$i + 1]['price']);
      $obj->value = '(' . $price[$i]['price'] . '-' . $price[$i + 1]['price'] . ')';
    }
    array_push($arr, $obj);
  }
  return $arr;
}

function countArr($arr) {
  return count($arr);
}


function money($money) {
  if ($money) return number_format($money) . 'đ';
  return 0;
}

function name() {
  $name = $_SESSION["name"];
  return $name;
}

function role() {
  return $_SESSION['role'];
}

function currentHost() {
  global $HOST;
  return $HOST;
}

function currentUrl() {
  $link = $_SERVER['REQUEST_URI'];
  return $link;
}

function resize($image, $value) {
  $arr = explode('.', $image);
  $extension = end($arr);
  $new_image = str_replace('.' . $extension, '_' . $value . '.' . $extension, $image);
  return $new_image;
}

function concatString($str1, $str2) {
  return $str1 . $str2;
}

function getPathname($url) {
  $index = strpos($url, '?');
  if ($index !== FALSE) {
    $url = substr($url, 0, $index);
  }
  return $url;
}

function canonical($page = NULL, $total = NULL) {
  if (strpos(currentUrl(), 'brand=') || strpos(currentUrl(), 'color=') || strpos(currentUrl(), 'price=') || strpos(currentUrl(), 'orderBy=')) {
    $text = '<link rel="canonical" href="' . getPathname(HOST . currentUrl()) . '"/>';
  } else {
    $text = '<link rel="canonical" href="' . HOST . currentUrl() . '"/>';
    if ($page == 1 && $total > 1) $text = $text . '<link rel="next" href="' . HOST . currentUrl() . '?page=2"/>';
    else if ($page == $total && $total == 2) {
      $text = $text . '<meta name="robots" content="noindex,follow"/>';
      $text = $text . '<link rel="prev" href="' . getPathname(HOST . currentUrl()) . '"/>';
    } else if ($page < $total && $total > 2) {
      $text = $text . '<meta name="robots" content="noindex,follow"/>';
      $prev = $page - 1;
      $next = $page + 1;
      $text = $text . '<link rel="prev" href="' . getPathname(HOST . currentUrl()) . '?page=' . $prev . '"/>';
      $text = $text . '<link rel="next" href="' . getPathname(HOST . currentUrl()) . '?page=' . $next . '"/>';
    } else if ($page == $total && $total > 2) {
      $text = $text . '<meta name="robots" content="noindex,follow"/>';
      $prev = $page - 1;
      $text = $text . '<link rel="prev" href="' . getPathname(HOST . currentUrl()) . '?page=' . $prev . '"/>';
    }
  }
  return $text;
}

function fullUrl($link = NULL) {
  if (isset($link)) return HOST . '/' . $link;
  return HOST;
}

function livechat() {
  return getMeta('livechat');
}

function collectionIndex() {
  if (getMemcached('productIndex')) return getMemcached('productIndex');
  $data = array();
  for ($i = 1; $i < 4; $i++) {
    $obj = new stdClass();
    $obj->title = getMeta('index_collection_title_' . $i);
    $collection_id = getMeta('index_collection_id_' . $i);
    $products = Product::Join('collection_product', 'collection_product.product_id', '=', 'product.id')
      ->where('collection_product.collection_id', $collection_id)
      ->where('product.status', 'active')->where('product.in_stock', 1)
      ->orderBy('product.in_stock', 'desc')->orderby('product.updated_at', 'desc')
      ->select('product.*')->take(5)->get();
    $products = Product::getInfoProduct($products);
    $obj->products = $products;
    array_push($data, $obj);
  }
  setMemcached("productIndex", $data);
  return $data;
}

function getFirstHistory($type, $type_id) {
  $item = History::where('type', $type)->where('type_id', $type_id)->orderBy('id', 'asc')->first();
  return $item;
}

function getLastHistory($type, $type_id) {
  $item = History::where('type', $type)->where('type_id', $type_id)->orderBy('id', 'desc')->first();
  return $item;
}

function getCollectionChild($parent_id) {
  $item = Collection::where('parent_id', $parent_id)->get();
  return $item;
}

function getSEO($type, $type_id) {
  $item = Seo::where('type', $type)->where('type_id', $type_id)->first();
  return $item;
}

function region($id) {
  $item = Region::find($id);
  return $item->name;
}

function subRegion($id) {
  $item = SubRegion::find($id);
  return $item->name;
}

function uppercase($a) {
  if ($a) $a = strtoupper($a);
  return $a;
}

function ymd2dmy($date) {
  return date("d-m-Y", strtotime($date));
}

function getItemFromArrId($type, $arrId) {
  $arrId = json_decode($arrId);
  switch ($type) {
    case 'product':
      $datas = Product::find($arrId);
      foreach ($datas as $key => $value) {
        $firstVariant = Variant::where('product_id', $value['id'])->first();
        $value['display_discount'] = FALSE;
        $value['percent'] = 0;
        $value['price'] = $firstVariant['price'];
        $value['price_compare'] = $firstVariant['price_compare'];
        if ($firstVariant['price_compare'] && $firstVariant['price_compare'] > $firstVariant['price']) {
          $value['discount'] = $firstVariant['price_compare'] - $firstVariant['price'];
          $value['percent'] = ($value['discount'] / $firstVariant['price_compare']) * 100;
          $value['percent'] = round($value['percent'], 0) . '%';
          $value['display_discount'] = TRUE;
        }
      }
      break;
    case 'article':
      $datas = Article::find($arrId);
      break;
    case 'collection':
      $datas = Collection::find($arrId);
      break;
    case 'blog':
      $datas = Blog::find($arrId);
      break;
    case 'page':
      $datas = Page::find($arrId);
      break;
  }
  return $datas;
}

function checkSignin() {
  if ($_SESSION['login_user']) {
    return TRUE;
  }
  return false;
}
