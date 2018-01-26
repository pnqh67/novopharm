<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Container as ContainerInterface;

require_once('view.php');

class AdminController {
  protected $ci;

  public function __construct(ContainerInterface $ci) {
    $this->ci = $ci;
    $detect = new Mobile_Detect;
    $deviceType = 'desktop';
    if ($detect->isMobile()){
      $deviceType = 'phone';
    }
    if ($detect->isTablet()) {
      $deviceType = 'tablet';
    }
    $themeDir = getThemeDir();
    $path =  ROOT . '/views/';

    $this->view = new View(array(
    	'path' => $path,
      'device' => $deviceType,
      'layout' => 'admin'
    ));
  }
}


function assignRestController($app, $endpoint, $controller) {
  $container = $app->getContainer();
  $app->get("/$endpoint", $controller . ':fetch');
  $app->get("/$endpoint/create", $controller . ':create');
  $app->get("/$endpoint/{id}", $controller . ':get');
  $app->post("/$endpoint", $controller . ':store');
  $app->put("/$endpoint/{id}", $controller . ':update');
  $app->delete("/$endpoint/{id}", $controller . ':delete');
}
