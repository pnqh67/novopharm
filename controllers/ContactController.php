<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Contact.php");
require_once("../models/helper.php");
use ControllerHelper as Helper;

class ContactController extends Controller {

  public function store(Request $request, Response $response) {
    $data = $request->getParsedBody();
    $code = Contact::store($data);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function update(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $code = Contact::update($body['id'],$body['type_status'], $body['type']);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

}

?>
