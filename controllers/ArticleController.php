<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once("../models/Article.php");
require_once("../models/History.php");
require_once("../models/Comment.php");

class ArticleController extends Controller
{
  public function fetch(Request $request, Response $response) {
    $params = $request->getQueryParams();

    $page_number = $params['page'] ? $params['page'] : 1;
    $perpage = $params['perpage'] ? $params['perpage'] : 20;
    $skip = ($page_number - 1) * $perpage;

    $allArticle = Article::where('status', 'active')->orderBy('created_at', 'desc');
    $total_pages = ceil(count($allArticle->get()) / $perpage);

    $articles = $allArticle->skip($skip)->take($perpage)->get();
    foreach ($articles as $article) {
      $article['url'] = '/article/' . $article['handle'];
    }
    return $this->view->render($response, 'article', array(
      'articles' => $articles,
      'total_pages' => $total_pages,
      'page_number' => $page_number + 0,
      'per_page' => $perpage
    ));
  }

  public function get(Request $request, Response $response) {
    $handle = $request->getAttribute('handle');
    $article = Article::where('status', 'active')
      ->where('handle', $handle)
      ->orWhere('handle_en', $handle)
      ->first();
    if (!$article) {
      $this->view->render($response, '404');
      return $response->withStatus(404);
    }
    $article->view = $article->view + 1;
    $article->save();
    $article['url'] = '/article/' . $article['handle'];
    $article['comments'] = Comment::where('status', 'active')
      ->where('type', 'article')
      ->where('type_id', $article['id'])
      ->join('customer', 'comment.customer_id', '=', 'customer.id')
      ->select('comment.*', 'customer.name as name')
      ->get();
    $article['comments_count'] = count($article['comments']);
    $related_articles = Article::where('blog_id', $article->blog_id)->get();
    return $this->view->render($response, 'article_detail', array(
      'article' => $article,
      'related_articles' => $related_articles
    ));
  }
}

?>
