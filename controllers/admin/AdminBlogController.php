<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Blog.php");
require_once(ROOT . '/controllers/helper.php');
use ControllerHelper as Helper;

class AdminBlogController extends AdminController {

  public function create(Request $request, Response $response) {
    return $this->view->render($response, 'admin/blog_create');
  }

  public function store(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $code = Blog::store($body);
    if ($code) History::admin('create', 'blog', $code, $body['title']);
    $result = Helper::response($code);
		return $response->withJson($result, 200);
  }

  public function fetch(Request $request, Response $response) {
    $data = Blog::where('status', '!=', 'delete')->orderBy('updated_at', 'desc')->get();
    return $this->view->render($response, 'admin/blog', array(
      'data' => $data
    ));
  }

  public function get(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $blog = Blog::find($id);
    if (!$blog) return $response->withStatus(302)->withHeader('Location', '/404');
    return $this->view->render($response, 'admin/blog_edit', array(
      'data' => $blog
    ));
  }

  public function update(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $id = $request->getAttribute('id');
    $code = Blog::update($id, $body);
    if (!$code) History::admin('update', 'blog', $id, $body['title']);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function delete(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $blog = Blog::find($id);
    $code = Blog::remove($id);
    if (!$code) History::admin('delete', 'blog', $id, $blog->title);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

}

?>
