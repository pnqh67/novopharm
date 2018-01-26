<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Blog.php");
require_once("../models/Article.php");

class AntibioticController extends Controller {

  public function get(Request $request, Response $response)
  {
	  $handle = $request->getAttribute('handle');
	  $blog = Blog::where('handle', $handle)
		  ->where('type_blog', 'antibiotic')
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
      ->where('type_blog', 'antibiotic')
	    ->get();
	  return $this->view->render($response, 'blog', array(
      'title' => "Sử dụng kháng sinh khoa học",
      'blogs' => $blogs
    ));
  }
}

?>
