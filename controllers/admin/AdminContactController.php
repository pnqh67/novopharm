<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Article.php");
require_once("../models/Blog.php");
require_once("../models/Contact.php");
require_once(ROOT . '/controllers/helper.php');
use ControllerHelper as Helper;

class AdminContactController extends AdminController {

  public function fetch(Request $request, Response $response) {
    $data = Contact::where('display_status','!=','delete')->get();
    return $this->view->render($response, 'admin/contact', [
      'data' => $data
    ]);
  }

  public function get(Request $request, Response $response) {
    $params = $request->getQueryParams();
    if ($params['id']){
      $data = Contact::where('display_status', '!=', 'delete')
        ->where('id',$params['id'])
        ->first();
      if ($data){
        return $this->view->render($response,'admin/contact_detail', array(
          'data' => $data
        ));
      }
    }
    if ($params['read'] == 1)
      $contacts = Contact::where('display_status','!=', 'delete')
        ->where('read_status', 'unread')
        ->get();
    if ($params['reply']==1)
      $contacts = Contact::where('display_status','!=', 'delete')
        ->where('reply_status', 'unreply')
        ->get();
    $type = $params['read']?'read':'reply';
    return $this->view->render($response, 'admin/contact_read_reply',array(
      'data' => $contacts,
      'type' => $type
    ));
  }

  public function store(Request $request, Response $response) {
    $data = $request->getParsedBody();
    $code = Contact::store($data);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function update(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $code = Contact::update($body['id'],$body['type_status'], $body['status']);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function updateStatus(Request $request, Response $response) {
    $body = $request->getParsedBody();
    if (is_array($body['arrId'])){
      foreach ($body['arrId'] as $id)
        Contact::update($id,$body['type_status'], $body['status']);
      return $response->withJson([
        'code'=> 0,
        'message' => 'Thành công'
      ]);
    }
    Contact::update($body['arrId'],$body['type_status'], $body['status']);
    return $response->withJson([
      'code'=> 0,
      'message' => 'Thành công'
    ]);
  }

  public function getDetail(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $data = Contact::where('status', '!=', 'delete')
      ->where('id',$id)
      ->first();
    if ($data){
      return $this->view->render($response,'admin/contact_detail', array(
        'data' => $data
      ));
    }
  }

  public function delete(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $code = Contact::delete($id);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }
}

?>
