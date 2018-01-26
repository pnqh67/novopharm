<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once("../models/Page.php");

class PageController extends Controller
{

  public function get(Request $request, Response $response) {
    $handle = $request->getAttribute('handle');
    $page = Page::where('status', 'active')
      ->where('handle', $handle)
      ->orWhere('handle_en', $handle)
      ->first();
    if (!$page) {
      $this->view->render($response, '404');
      return $response->withStatus(404);
    }
    $page['url'] = '/page/' . $page['handle'];
    return $this->view->render($response, 'page_detail', array(
      'page' => $page
    ));
  }

  public function show(Request $request, Response $response) {
    $handle = $request->getAttribute('handle');
    $page = Page::where('handle', $handle)
      ->orWhere('handle_en', $handle)
      ->first();
    if (!$page) {
      return -1;
    }
    $this->view->render($response, 'page', array(
      'page' => $page
    ));
    return $response->withStatus(302)->withHeader('Location', '/'. $handle);
  }

  public function PageNotFound(Request $request, Response $response) {
    $this->view->render($response, '404');
    return $response->withStatus(404);
  }
}

?>
