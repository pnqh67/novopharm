<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once("../models/Experience.php");
require_once("../models/Blog.php");
require_once("../models/History.php");
require_once("../models/Comment.php");

class ExperienceController extends Controller
{
  public function get(Request $request, Response $response)
  {
    $handle = $request->getAttribute('handle');
    $blog = Blog::where('handle', $handle)
      ->where('type', 'experience')
      ->where('status', 'active')->first();
    if ($blog) {
      $blog['articles'] = Article::where('blog_id', $blog['id'])->where('status', 'active')->get();
      $blog['article_count'] = count($blog['articles']);
      $blog['url'] = '/nhom-bai-viet/' . $blog['handle'];
    }
    return $this->view->render($response, 'blog_detail', array(
      'blog' => $blog
    ));
  }
  public function fetch(Request $request, Response $response) {
    $blogs = Blog::where('status','active')
      ->where('type', 'experience')
      ->get();
    foreach ($blogs as $item){
      $item['experiences'] = Experience::where('status', 'active')
        ->where('blog_id', $item['id'])
        ->get();
    }
    return $this->view->render($response, 'experience', array(
      'blogs' => $blogs
    ));
  }
}

?>
