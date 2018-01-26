<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$adminDir = ROOT . '/controllers/admin/';
$files = scandir($adminDir);
$files = array_diff($files, array('.', '..', __FILE__));
foreach ($files as $file) {
  try {
    require_once($adminDir . $file);
  } catch (Exception $e) {
    error_log($e);
  }
}

require_once('../controllers/TestController.php');
require_once("../models/User.php");

$role = $_SESSION['role'];

$app->get("/admin", function ($req, $res){
  if(isset($_SESSION['href'])) return $res->withStatus(302)->withHeader('Location', $_SESSION['href']);
  return $res->withStatus(302)->withHeader('Location', '/admin/login');
});
$app->get('/admin/login', '\AdminUserController:getlogin');

$app->group('/admin', function() use($app) {

  $app->get("/", function ($req, $res){
    $href = $_SESSION['href'];
    return $res->withStatus(302)->withHeader('Location', $href);
  });
  $app->get('/logout', '\AdminUserController:getLogout');

  $app->post('/api/uploadImage', 'uploadImage');
  $app->post('/api/uploadImageTinymce', 'uploadImageTinymce');

  //Dashboard
  $app->get('/dashboard', '\AdminDashboardController:fetch');
  $app->get('/dashboard/getcontact', '\AdminDashboardController:getContact');
  $app->get('/dashboard/getrevenue/{numOfDays}', '\AdminDashboardController:getRevenue');
  $app->get('/dashboard/getvisit/{numOfDays}', '\AdminDashboardController:getVisit');
  $app->get('/dashboard/history', '\AdminDashboardController:fetchHistory');

	$app->get('/view/setting', '\AdminDashboardController:settingView');


  //Product
  // $app->get('/product', '\AdminProductController:fetch');
  // $app->post('/product', '\AdminProductController:store');
  // $app->get('/product/create', '\AdminProductController:create');
  // $app->get('/product/{id}', '\AdminProductController:show');
  // $app->put('/product/{id}', '\AdminProductController:update');
  // $app->delete('/product/{id}', '\AdminProductController:delete');
  //
  //
   $app->post('/product/tag', '\AdminTagController:store');
   $app->put('/product/tag', '\AdminTagController:update');
   $app->delete('/product/tag', '\AdminTagController:deleteList');

    //
  // $app->get('/product/updateStock', '\AdminProductController:updateStock');

  //product buy together
  $app->get('/product_buy_together','\AdminProductBuyTogetherController:fetch');
  $app->post('/product_buy_together','\AdminProductBuyTogetherController:store');
  $app->get('/product_buy_together/create','\AdminProductBuyTogetherController:create');
  $app->get('/product_buy_together/{id}', '\AdminProductBuyTogetherController:show');
  $app->put('/product_buy_together', '\AdminProductBuyTogetherController:update');
  $app->delete('/product_buy_together/{id}', '\AdminProductBuyTogetherController:delete');

  $app->post('/seo', '\AdminProductController:SEO');


  //coupon
  $app->get('/coupon', '\AdminCouponController:fetch');
  $app->get('/coupon/create', '\AdminCouponController:create');
  $app->post('/coupon', '\AdminCouponController:store');
  $app->get('/coupon/{id}', '\AdminCouponController:get');
  $app->put('/coupon/{id}', '\AdminCouponController:update');
  $app->delete('/coupon/{id}', '\AdminCouponController:delete');

  //variant
  $app->post('/variant', '\AdminVariantController:store');
  $app->get('/variant/{id}', '\AdminVariantController:show');
  $app->put('/variant/{id}', '\AdminVariantController:update');
  $app->delete('/variant/{id}', '\AdminVariantController:delete');

  //collection
  $app->get('/collection', '\AdminCollectionController:index');
  $app->get('/collection/create', '\AdminCollectionController:create');
  $app->get('/collection/{id}', '\AdminCollectionController:show');
  $app->post('/collection/sortProduct/{id}', '\AdminCollectionController:sortProduct');
  $app->delete('/collection/removeProduct', '\AdminCollectionController:removeProduct');
  $app->post('/collection', '\AdminCollectionController:store');
  $app->put('/collection/{id}', '\AdminCollectionController:update');
  $app->delete('/collection/{id}', '\AdminCollectionController:delete');

  //experience
  $app->get('/experience', 'AdminExperienceController:index');
  $app->get('/experience/create', 'AdminExperienceController:create');
  $app->get('/experience/{id}', '\AdminExperienceController:get');
  $app->put('/experience/{id}', '\AdminExperienceController:update');
  $app->post('/experience', '\AdminExperienceController:store');

  //attribute
  $app->get('/attribute', '\AdminAttributeController:index');
  $app->post('/attribute', '\AdminAttributeController:store');
  $app->get('/attribute/{id}', '\AdminAttributeController:get');
  $app->put('/attribute/{id}', '\AdminAttributeController:update');
  $app->delete('/attribute/{id}', '\AdminAttributeController:delete');

  //price
  $app->get('/price', '\AdminPriceController:index');
  $app->post('/price', '\AdminPriceController:store');
  $app->put('/price/{id}', '\AdminPriceController:update');
  $app->delete('/price/{id}', '\AdminPriceController:delete');

  //Order
  $app->get('/order', '\AdminOrderController:index');
  $app->get('/order/search', '\AdminOrderController:search');
  $app->get('/order/{id}', '\AdminOrderController:show');
  $app->put('/order/{id}', '\AdminOrderController:update');

  //Customer
  $app->get('/customer', '\AdminCustomerController:fetch');
  $app->get('/customer/create', '\AdminCustomerController:create');
  $app->get('/customer/{id}/order', '\AdminCustomerController:showOrder');
  $app->get('/customer/export', '\AdminCustomerController:export');

  //comment
  $app->get('/comment', '\AdminCommentController:fetch');
  $app->get('/comment/{type}', '\AdminCommentController:get');
  $app->put('/comment/{id}', '\AdminCommentController:update');
  $app->delete('/comment/{id}', '\AdminCommentController:delete');

  //contact
  $app->get('/contact', '\AdminContactController:fetch');
  $app->get('/contact/get', '\AdminContactController:get');
  $app->post('/contact', '\AdminContactController:store');
//  $app->put('/contact/{id}', '\AdminContactController:update');
  $app->put('/contact/updateStatus', '\AdminContactController:updateStatus');
  $app->delete('/contact/{id}', '\AdminContactController:delete');

  //sale
  $app->get('/sale','\AdminSaleController:fetch');
  $app->post('/sale','\AdminSaleController:store');
  $app->get('/sale/create','\AdminSaleController:create');
  $app->get('/sale/{id}','\AdminSaleController:get');
  $app->post('/sale/getproduct','\AdminSaleController:getProduct');
  $app->put('/sale/{id}','\AdminSaleController:update');
  $app->delete('/sale/{id}','\AdminSaleController:delete');

  //shipping
  $app->get('/shipping_fee', '\AdminShippingFeeController:fetch');
  $app->post('/shipping_fee', '\AdminShippingFeeController:store');
  $app->post('/shipping_fee/fee/{id}', '\AdminShippingFeeController:shippingfee');
  $app->get('/shipping_fee/create', '\AdminShippingFeeController:create');
  $app->get('/shipping_fee/edit/{regionid}', '\AdminShippingFeeController:edit');
  $app->get('/shipping_fee/{id}', '\AdminShippingFeeController:loaddata');
  $app->put('/shipping_fee/{id}', '\AdminShippingFeeController:update');
  $app->delete('/shipping_fee/{id}', '\AdminShippingFeeController:delete');

  //subregion
  $app->post('/subregion/{id}', '\AdminSubregionController:subRegion');

  //slider
  $app->get('/slider', '\AdminSliderController:index');
  $app->get('/slider/create', '\AdminSliderController:create');
  $app->get('/slider/{id}', '\AdminSliderController:get');
  $app->post('/slider', '\AdminSliderController:store');
  $app->put('/slider/{id}', '\AdminSliderController:update');

  //menu
  $app->get('/menu', '\AdminMenuController:index');
  $app->get('/menu/{id}', '\AdminMenuController:getMenu');
  $app->get('/menu/child/{handle}', '\AdminMenuController:get');
  $app->post('/menu', '\AdminMenuController:store');
  $app->delete('/menu/{id}', '\AdminMenuController:delete');
  $app->delete('/menu', '\AdminMenuController:deleteArr');
  $app->put('/menu/{id}', '\AdminMenuController:update');
  $app->get('/menu/list-menu/{type}', '\AdminMenuController:getListMenu');

  //setting
  $app->get('/setting', '\AdminSettingController:setting');
  $app->get('/getSetting', '\AdminSettingController:getSetting');
	$app->get('/setting/getList', '\AdminSettingController:getList');
	$app->put('/setting', '\AdminSettingController:updateSetting');

	//location
  $app->get('/location','\AdminLocationController:index');
  $app->get('/location/{id}','\AdminLocationController:get');
  $app->put('/location/{id}','\AdminLocationController:update');
  $app->post('/location','\AdminLocationController:store');
  $app->get('/location/getSubregion/{id}','\AdminLocationController:getSubregion');

  $app->get('/user/history', '\AdminUserController:history');
  $app->put('/api/user/changePassword', '\AdminUserController:changePassword');

  $app->get('/images', '\AdminSettingController:getImages');
  $app->get('/uploads', '\AdminSettingController:getUploads');
  $app->delete('/api/images/remove', '\AdminSettingController:removeImage');

  $app->get('/api/uploads', '\AdminSettingController:getUploads');
  $app->post('/api/removeUploads', '\AdminSettingController:removeImageUploads');
  $app->post('/api/updateStatus', 'updateStatus');

  $app->get('/api/checkHandle', 'checkHandle');

  $app->get('/api/rotate', 'rotateImage');
  $app->get('/api/tinymce/images', '\AdminProductController:renderImageTinymce');

  $app->post('/api/collection/addMuch', '\AdminCollectionController:addMuch');
  $app->delete('/api/collection/deleteMuch', '\AdminCollectionController:deleteMuch');

  $app->get('/error','\AdminUserController:error');

  $app->get('/api/getChildAttribute','\AdminAttributeController:getChildAttribute');

  assignRestController($app, 'blog', 'AdminBlogController');
  assignRestController($app, 'page', 'AdminPageController');
  assignRestController($app, 'product', 'AdminProductController');
  assignRestController($app, 'article', 'AdminArticleController');
  assignRestController($app, 'role', 'AdminRoleController');
  assignRestController($app, 'user', 'AdminUserController');
  $app->get('/api/permission', '\AdminRoleController:Permission');

})->add(function ($request, $response, $next) {
  if(session_status() == PHP_SESSION_NONE) session_start();
  if(isset($_SESSION['login'])) {
    $user = User::find($_SESSION['user_id']);
    if(!$user) {
      unset($_SESSION['login']);
      unset($_SESSION['user_id']);
      unset($_SESSION['email']);
      unset($_SESSION['name']);
      unset($_SESSION['role']);
      return $response->withStatus(302)->withHeader('Location', '/admin/login');
    }
  }

  if(!isset($_SESSION['login'])) {
    $_SESSION['href'] = $request->getUri()->getPath();
    return $response->withStatus(302)->withHeader('Location', '/admin/login');
  }

  //TODO: Phan quyen

  $method = $request->getMethod();
  $uri = $request->getUri();

  if (!checkPermission($user, $method, $uri)) {
    return $response->withStatus(403)->withHeader('Location', '/admin/error');
  }

  return $next($request, $response);
});
