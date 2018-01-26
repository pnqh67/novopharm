<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Menu.php");
require_once(ROOT . '/models/Collection.php');
require_once(ROOT . '/controllers/helper.php');
use ControllerHelper as Helper;

class AdminMenuController extends AdminController {

	public function getListMenu(Request $request, Response $response) {
		$type = $request->getAttribute('type');
		if ($type == 'collection') $data = Collection::where('status','active')->orderBy('breadcrumb', 'asc')->get();
		else if ($type == 'blog') $data = Blog::where('status','active')->orderBy('title', 'desc')->get();
		else if ($type == 'article') $data = Article::where('status','active')->orderBy('title', 'desc')->get();
		else if ($type == 'page') $data = Page::where('status','active')->orderBy('title', 'desc')->get();
		return $response->withJson([
			'code' => 0,
			'data' => $data
		]);
	}

	public function index(Request $request, Response $response) {
		$menu = Menu::where('status','!=','delete')
			->where('parent_id','-1')
			->get();
		$blog = Blog::where('status','active')->get();
		$article = Article::where('status','active')->get();
    $page = Page::where('status','active')->get();
    $collection = Collection::where('status','active')->orderBy('breadcrumb', 'asc')->get();
		return $this->view->render($response, 'admin/menu', [
			'data' => $menu,
      'collection' => $collection,
			'blog' => $blog,
			'article' => $article,
      'page' => $page
		]);
	}

	public function get(Request $request, Response $response) {
    $handle = $request->getAttribute('handle');
    $temp = Menu::where('status','!=','delete')
      ->where('handle',$handle)
      ->first();
    $childMenu = Menu::where('status', '!=','delete')
      ->where('parent_id',$temp->id)
      ->get();
    getSubmenu($childMenu);
    $blog = Blog::where('status','active')->get();
    $article = Article::where('status','active')->get();
    $page = Page::where('status','active')->get();
    $collection = Collection::where('status','active')->orderBy('breadcrumb', 'asc')->get();
    return $this->view->render($response, 'admin/childMenu', [
    	'parentMenu' => $temp,
      'data' => $childMenu,
      'collection' => $collection,
      'blog' => $blog,
      'article' => $article,
      'page' => $page
    ]);
  }

	public function store(Request $request, Response $response) {
		$body = $request->getParsedBody();
		$code = Menu::store($body);
		$result = Helper::response($code);
		return $response->withJson($result, 200);
	}

	public function update(Request $request, Response $response) {
		$id = $request->getAttribute('id');
		$body = $request->getParsedBody();
		$code = Menu::update($id, $body);
		$result = Helper::response($code);
		return $response->withJson($result, 200);
	}

	public function delete (Request $request, Response $response) {
		$id = $request->getAttribute('id');
		$code = Menu::remove($id);
		$result = Helper::response($code);
		return $response->withJson($result, 200);
	}

	public function deleteArr (Request $request, Response $response) {
		$body = $request->getParsedBody();
		foreach ($body['arrId'] as $value){
      Menu::remove($value);
    }
		return $response->withJson(0, 200);
	}

	public function getMenu(Request $request, Response $response)	{
		$id = $request->getAttribute('id');
		$menu = Menu::find($id);
		if ($menu) {
			return $response->withJson([
				'code' => 0,
				'data' => $menu
			]);
		}
	}
}

?>
