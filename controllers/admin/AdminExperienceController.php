<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Experience.php");
require_once("../models/Blog.php");
require_once(ROOT . '/controllers/helper.php');
use ControllerHelper as Helper;

class AdminExperienceController extends AdminController {

  public function index(Request $request, Response $response) {
    $data = Experience::where('status', '!=' , 'delete')->get();
    return $this->view->render($response, 'admin/experience', array(
      'data' => $data
    ));
  }

  public function get(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $article = Experience::find($id);
    if (!$article) return $response->withStatus(302)->withHeader('Location', '/404');
    $blogs = Blog::where('status', '!=', 'delete')
      ->where('type','experience')
      ->orderBy('title', 'asc')->get();
    return $this->view->render($response, 'admin/experience_edit', array(
      'data' => $article,
      'blogs' => $blogs
    ));
  }

  public function create(Request $request, Response $response) {
    $blogs = Blog::where('status', '!=', 'delete')
      ->where('type','experience')
      ->orderBy('title', 'asc')
      ->get();
    return $this->view->render($response, 'admin/experience_create', array(
      'blogs' => $blogs
    ));
  }

  public function store(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $code = Experience::store($body);
    if ($code) History::admin('create', 'experience', $code, $body['title']);
    $result = Helper::response($code);
    return $response->withJson($result, 200);

  }

  public function update(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $id = $request->getAttribute('id');
    $code = Article::update($id, $body);
    if (!$code) History::admin('update', 'article', $id, $body['title']);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function delete(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $article = Article::find($id);
    $code = Article::remove($id);
    if (!$code) History::admin('delete', 'article', $id, $article->title);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }
}

?>
