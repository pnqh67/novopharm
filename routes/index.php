<?php

use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once('../controllers/IndexController.php');
require_once('../controllers/CollectionController.php');
require_once('../controllers/ProductController.php');
require_once('../controllers/PageController.php');
require_once('../controllers/OrderController.php');
require_once('../controllers/ArticleController.php');
require_once('../controllers/BlogController.php');
require_once('../controllers/UserController.php');
require_once('../controllers/TestController.php');
require_once('../controllers/ImageController.php');
require_once('../controllers/CustomerController.php');
require_once('../controllers/CommentController.php');
require_once('../controllers/CouponController.php');
require_once('../controllers/ContactController.php');
require_once('../controllers/ExperienceController.php');
require_once('../controllers/LocationController.php');
require_once('../controllers/RespiratoryController.php');
require_once('../controllers/AntibioticController.php');
require_once('../models/helper.php');
require_once('../models/Statistic.php');

$app->group('/{lang:vi|en}', function () use ($app) {
  if (isset($_COOKIE["lang"])) {
    list($value, $expiry) = explode("|", $_COOKIE["lang"]);
  }
  $app->get('', function ($request, $response, $args) {
    $lang = $args['lang'];
    return $response->withStatus(302)->withHeader('Location', '/' . $lang . '/');
  });

  $app->get('/', '\IndexController:index');

//blog
  $app->get('/nhom-bai-viet/{handle}', '\BlogController:get');
  $app->get('/blog/{handle}', '\BlogController:get');
  $app->get('/nhom-bai-viet', '\BlogController:fetch');
  $app->get('/blog', '\BlogController:fetch');

//experience
  $app->get('/kinh-nghiem-su-dung', '\ExperienceController:fetch');
  $app->get('/experience', '\ExperienceController:fetch');

//location
  $app->get('/diem-ban', '\LocationController:fetch');
  $app->get('/diem-ban/chi-tiet', '\LocationController:get');

//article
  $app->get('/bai-viet/{handle}', '\ArticleController:get');
  $app->get('/article/{handle}', '\ArticleController:get');
  $app->get('/bai-viet', '\ArticleController:fetch');
  $app->get('/article', '\ArticleController:fetch');

//page
  $app->get('/trang-noi-dung/{handle}', '\PageController:get');
  $app->get('/page/{handle}', '\PageController:get');

//collection
  $app->get('/nhom-san-pham/{handle}', '\CollectionController:get');
  $app->get('/collection/{handle}', '\CollectionController:get');

//respiratory
  $app->get('/benh-ho-hap', '\RespiratoryController:fetch');
  $app->get('/respiratory', '\RespiratoryController:fetch');
  $app->get('/benh-ho-hap/{handle}', '\RespiratoryController:get');
  $app->get('/respiratory/{handle}', '\RespiratoryController:get');

//antibiotic
  $app->get('/khang-sinh', '\AntibioticController:fetch');
  $app->get('/antibiotic', '\AntibioticController:fetch');
  $app->get('/khang-sinh/{handle}', '\AntibioticController:get');
  $app->get('/antibiotic/{handle}', '\AntibioticController:get');

//product
  $app->get('/san-pham', '\ProductController:fetch');
  $app->get('/product', '\ProductController:fetch');
  $app->get('/san-pham/{handle}', '\ProductController:get');
  $app->get('/product/{handle}', '\ProductController:get');
  $app->get('/san-pham/xem-nhanh/{id}', '\ProductController:quickView');

//customer
  $app->get('/tai-khoan', '\CustomerController:get');
  $app->get('/account', '\CustomerController:get');

//order
  $app->get('/quan-ly-don-hang', '\CustomerController:order');
  $app->get('/order', '\CustomerController:order');

//favorite
  $app->get('/yeu-thich', '\CustomerController:favorite');
  $app->get('/favorite', '\CustomerController:favorite');

//cart
  $app->get('/gio-hang', '\OrderController:get');
  $app->get('/cart', '\OrderController:get');
  $app->get('/kiem-tra-gio-hang', '\OrderController:checkOut');
  $app->get('/checkout', '\OrderController:checkOut');
  $app->get('/order-success', '\OrderController:orderSuccess');
  $app->get('/dat-hang-thanh-cong', '\OrderController:orderSuccess');

//search
  $app->get('/tim-kiem', '\IndexController:search');
  $app->get('/search', '\IndexController:search');

//page not found
  $app->get('/404', '\PageController:PageNotFound');

//showroom
  $app->get('/showroom', '\IndexController:showroom');

//cong trinh tieu bieu
  $app->get('/cong-trinh-tieu-bieu', '\IndexController:congtrinh');

  $app->get('/{params:.*}', function ($request, $response, $args) {
    $langCode = $_SESSION['lang'];
    if ($langCode == 'en') {
      $from = 'vi';
      $to = 'en';
    } else if ($langCode == 'vi') {
      $from = 'en';
      $to = 'vi';
    }
    $link = $request->getAttribute('params');
    $params = explode('/', $link);
    if ($params[count($params) - 1] == '') array_pop($params);
    $newParams = [];
    foreach ($params as $key => $param) {
      $newParams[] = convertHandle($param, $from, $to);
    }
    $link = implode("/", $newParams);
    $PageCtrl = new PageController(new ContainerInterface);
    $CollectionCtrl = new CollectionController(new ContainerInterface);
    $request = $request->withAttribute('handle', $link);
    $result = $PageCtrl->show($request, $response);
    if ($result == -1) {
      $request = $request->withAttribute('link', $link);
      $CollectionCtrl->show($request, $response);
    }
  });
})->add(function ($request, $response, $next) {
  $route = $request->getAttribute('route');
  $lang = $route->getArgument('lang');
  $_SESSION['lang'] = $lang;
  setcookie('lang', $_SESSION['lang'], time() + (86400 * 30), '/');
  $params = $route->getArgument('params');
  $params = explode('/', $params);
  if ($params[count($params) - 1] == '') array_pop($params);
  $response = $next($request, $response);
  $_SESSION['href_user'] = $request->getUri()->getPath();
  if ($response->getStatusCode() == 404) {
    if (array_pop($params) == 'admin') return $response->withStatus(302)->withHeader('Location', '/admin/login');
  }
  return $response;
});

$app->get('/api/location/{region_id}', '\LocationController:getSubregion');

//search API
$app->get('/api/search', '\IndexController:searchAPI');

//comment
$app->post('/api/comment', '\CommentController:store');

//coupon
$app->post('/api/couponCheck', '\CouponController:check');

//contact
$app->post('/api/contact', '\ContactController:store');

//favorite
$app->post('/api/favorite', '\CustomerController:favoriteStore');
$app->put('/api/favorite', '\CustomerController:favoriteDelete');

//customer
$app->get('/customer/{random}', '\CustomerController:createPassword');
$app->get('/api/signout', '\CustomerController:signout');
$app->post('/api/signup', '\CustomerController:signup');
$app->post('/api/signin', '\CustomerController:signin');
$app->post('/api/changePassword', '\CustomerController:changePassword');
$app->post('/api/changeForgotPassword', '\CustomerController:changeForgotPassword');
$app->post('/api/changeInformation', '\CustomerController:changeInformation');
$app->post('/api/forgotPassword', '\CustomerController:forgotPassword');

//image
$app->get('/api/image', '\ImageController:get');

//order
$app->post('/api/order', '\OrderController:order');
$app->get('/cart/getInfo', '\OrderController:getInfoCart');
$app->get('/products/{handle}', '\OrderController:getProduct');
$app->post('/cart/add', '\OrderController:addToCart');
$app->post('/cart/change', '\OrderController:changeCart');
$app->post('/cart/addNote', 'OrderController:addNote');
$app->post('/cart/clear', '\OrderController:clearCart');
//$app->post('/api/order', '\OrderController:store');
$app->get('/api/region', 'getSubRegion');

//create sitemap
$app->get('/api/website/sitemap', 'createSitemap');


$app->post('/api/filter', '\CollectionController:filter');
$app->get('/api/san-pham/search', 'smartSearch');
$app->get('/user/forgotpassword', '\UserController:forgotPassword');
$app->get('/user/{random}', '\UserController:createPassword');
$app->post('/api/user/login', '\UserController:checkLogin');
$app->put('/api/user/password', '\UserController:updatePassword');
$app->get('/api/user/checkEmail', '\UserController:resetPassword');
$app->get('/api/san-pham/variant/{id}', '\ProductController:findProductVariant');
$app->get('/api/variant/{id}', '\ProductController:getProductOfVariant');

$app->get('/pixel/visit', function ($request, $response, $args) {
  $data = [
    'user_agent' => $_SERVER['HTTP_USER_AGENT']
  ];
  Statistic::createOrUpdate($data);
  return $response->withStatus(200)->withHeader('Content-type', 'image/png')->write($img);
});

$app->get('/language/{lang}', function ($request, $response) {
  $lang = $request->getAttribute('lang');
  $_SESSION['lang'] = $lang;
  setcookie('lang', $_SESSION['lang'], time() + (86400 * 30), '/');
  $query = $request->getQueryParams();
  $link = $query['link'];

  unset($query['link']);

  $handles = explode('/', $link);
  $from = $handles[1];
  $to = $lang;
  $newHandles = [];
  foreach ($handles as $key => $handle) {
    $newHandles[] = convertHandle($handle, $from, $to);
  }
  if (count($query) > 0) {
    $parent_collection = $query['parent-collection-id'] ? "?parent-collection-id=" . $query['parent-collection-id'] : '';
    $collection = $query['collection-id'] ? "&collection-id=" . $query['collection-id'] : '';
    $variant = $query['variant-id'] ? ($query['parent-collection-id'] ? "&variant-id=" . $query['variant-id'] : "?variant-id=" . $query['variant-id']) : '';
    $link = implode('/', $newHandles) . $parent_collection . $collection . $variant;
  } else {
    $link = implode('/', $newHandles);
  }
  return $response->withStatus(302)->withHeader('Location', $link);
});

$app->get('/{params:.*}', function ($request, $response) {
  if ($_COOKIE['lang']) {
    $_SESSION['lang'] = $_COOKIE['lang'];
  } else {
    $_SESSION['lang'] = 'vi';
  }
  $langCode = $_SESSION['lang'];
  if ($langCode == 'en') {
    $from = 'vi';
    $to = 'en';
  } else if ($langCode == 'vi') {
    $from = 'en';
    $to = 'vi';
  }
  $link = $request->getAttribute('params');
  $params = explode('/', $link);
  if ($params[count($params) - 1] == '') array_pop($params);
  $newParams = [];
  foreach ($params as $key => $param) {
    $newParams[] = convertHandle($param, $from, $to);
  }
  $link = implode("/", $newParams);

  return $response->withStatus(302)->withHeader('Location', '/' . $langCode . "/" . $link);
});
//Redirect to 404
$app->group('/{link}', function () use ($app) {
  $app->get('', function ($request, $response, $args) {
    return $response->withStatus(302)->withHeader('Location', '/404');
  });
});
