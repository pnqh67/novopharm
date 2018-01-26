<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Comment.php");
use ControllerHelper as Helper;

class CommentController extends Controller {

  public function store(Request $request, Response $response) {
    $body = $request->getParsedBody();
    if (!isset($_SESSION['login_user'])){
    	return $response->withJson([
    		'code' => -1,
		    'message' => 'Chưa đăng nhập'
	    ]);
    }
    $code = Comment::store($body);
    $result = Helper::response($code);
    return $response->withJson($result,200);
  }

}

?>
