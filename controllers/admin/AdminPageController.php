<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Page.php");
require_once("../models/Seo.php");
require_once(ROOT . '/controllers/helper.php');
use ControllerHelper as Helper;

class AdminPageController extends AdminController {

  public function fetch(Request $request, Response $response) {
    $data = Page::where('status', '!=', 'delete')->orderBy('updated_at', 'desc')->get();
    return $this->view->render($response, 'admin/page', array(
      'data' => $data
    ));
  }

  public function create(Request $request, Response $response) {
    return $this->view->render($response, 'admin/page_create');
  }

  public function store(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $code = Page::store($body);
    if ($code) History::admin('create', 'page', $code, $body['title']);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function get(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $page = Page::find($id);
    if (!$page) return $response->withStatus(302)->withHeader('Location', '/404');
    return $this->view->render($response, 'admin/page_edit', array(
        'data' => $page
    ));
  }

  public function update(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $body = $request->getParsedBody();
    $code = Page::update($id, $body);
    if (!$code) History::admin('update', 'page', $id, $body['title']);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function delete(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $page = Page::find($id);
    $code = Page::remove($id);
    if (!$code) History::admin('delete', 'page', $id, $page->title);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

}

?>
