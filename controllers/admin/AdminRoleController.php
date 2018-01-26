<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once(ROOT . '/controllers/helper.php');
require_once("../models/Role.php");
require_once("../models/Permission.php");
use ControllerHelper as Helper;

class AdminRoleController extends AdminController {

  public function fetch(Request $request, Response $response) {
    $data = Role::all();
    return $this->view->render($response, 'admin/role', array(
      'data' => $data
    ));
  }

  public function create(Request $request, Response $response) {
    return $this->view->render($response, 'admin/role_create');
  }

  public function get(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $role = Role::find($id);
    if (!$role) return $response->withStatus(302)->withHeader('Location', '/404');
    $permission = Permission::where('role_id', $id)->groupBy('group')->get();
    return $this->view->render($response, 'admin/role_edit', array(
      'data' => $role,
      'permission' => $permission
    ));
  }

  public function store(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $code = Role::store($body['title']);
    if ($code) {
      History::admin('create', 'role', $code, $body['title']);
      $permission = $body['permission'];
      foreach ($permission as $key => $value) {
        if ($value['method'] && $value['endpoint']) {
          $data = [
            'group' => $value['group'],
            'method' => $value['method'],
            'endpoint' => $value['endpoint']
          ];
          Permission::store($code, $data);
        }
      }
    }
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function update(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $id = $request->getAttribute('id');
    $code = Role::update($id, $body['title']);
    if (!$code) {
      History::admin('update', 'role', $id, $body['title']);
      Permission::remove($id);
      $permission = $body['permission'];
      foreach ($permission as $key => $value) {
        if ($value['method'] && $value['endpoint']) {
          $data = [
            'group' => $value['group'],
            'method' => $value['method'],
            'endpoint' => $value['endpoint']
          ];
          Permission::store($id, $data);
        }
      }
    }
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function delete(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $role = Role::find($id);
    $code = Role::remove($id);
    if (!$code) History::admin('delete', 'role', $id, $role->title);
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function Permission(Request $request, Response $response) {
    $params = $request->getQueryParams();
    $role_id = $params['role_id'];
    $group = $params['group'];
    $data = Permission::where('role_id', $role_id)->where('group', $group)->select('method', 'endpoint')->get();
    return $response->withJson([
      'code' => 0,
      'data' => $data,
      'count' => count($data)
    ]);
  }
}

?>
