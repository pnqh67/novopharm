<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Article.php");
require_once("../models/Blog.php");
require_once("../models/Seo.php");
require_once(ROOT . '/controllers/helper.php');
use ControllerHelper as Helper;

class AdminArticleController extends AdminController {

  public function fetch(Request $request, Response $response) {
    $data = Article::where('status', '!=', 'delete')->orderBy('updated_at', 'desc')->get();
    return $this->view->render($response, 'admin/article', array(
      'data' => $data
    ));
  }

  public function create(Request $request, Response $response) {
    $blogs = Blog::where('status', '!=', 'delete')
      ->where('type','article')
      ->orderBy('title', 'asc')->get();
    return $this->view->render($response, 'admin/article_create', array(
      'blogs' => $blogs
    ));
  }

  public function store(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $code = Article::store($body);
    if ($code) History::admin('create', 'article', $code, $body['title']);
    $result = Helper::response($code);
    return $response->withJson($result, 200);

  }

  public function get(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $article = Article::find($id);
    if (!$article) return $response->withStatus(302)->withHeader('Location', '/404');
    $blogs = Blog::where('status', '!=', 'delete')
      ->where('type','article')
      ->orderBy('title', 'asc')->get();
    return $this->view->render($response, 'admin/article_edit', array(
      'data' => $article,
      'blogs' => $blogs
    ));
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
