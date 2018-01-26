<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once("../models/helper.php");

class IndexController extends Controller
{

  public function index(Request $request, Response $response) {
    return $this->view->render($response, 'index');
  }

  public function showroom(Request $request, Response $response) {
    return $this->view->render($response, 'showroom');
  }

  public function congtrinh(Request $request, Response $response) {
    return $this->view->render($response, 'congtrinh');
  }

  public function search(Request $request, Response $response) {
    return $this->view->render($response, 'search');
  }

  public function searchAPI(Request $request, Response $response) {
    $result = [];
    $params = $request->getQueryParams();
    $filter = $params['filter'];
    $regexSplit = '/([\w?%<*=*>]+)([&&]+)*/s';
    $tempArrData = preg_split($regexSplit, $filter, -1, PREG_SPLIT_DELIM_CAPTURE);
    $arrData = [];
    foreach ($tempArrData as $index => $item) {
      if (!empty($item)) {
        array_push($arrData, $item);
      }
    }
    $type = $params['type'] ? $params['type'] : 'product';
    if ($params['filter']) {
      $regex = '/([^<*=*>IN!]*)([<*=*>!IN]{1,2}|[*=*!]{1,2})([^*].*)/m';
      foreach ($arrData as $item) {
        if ($item == '&&' || $item == '||') {
          array_push($result, $item);
        } else {
          $temp = preg_split($regex, $item, -1, PREG_SPLIT_DELIM_CAPTURE);
          unset($temp[0]);
          unset($temp[4]);
          array_push($result, $temp);
        }
      }
      switch ($type) {
        case 'article':
        case 'page':
        case 'blog':
        case 'collection':
          $data = $type::where(function ($query) use ($result) {
            foreach ($result as $key => $item) {
              if ($item != '&&') {
                switch ($item[2]) {
                  case '*=':
                    $query->where($item[1], 'like', $item[3] . '%');
                    break;
                  case '=*':
                    $query->where($item[1], 'like', '%' . $item[3]);
                    break;
                  case '**':
                    $query->where($item[1], 'like', '%' . $item[3] . '%');
                    break;
                  case 'IN':
                    $query->whereIn($item[1], $item[3]);
                    break;
                  default:
                    $query->where($item[1], '=', $item[3]);
                }
              }
            }
          })->get();
          break;
        case 'product':
          $data = $type::where(function ($query) use ($result) {
            foreach ($result as $key => $item) {
              if ($item != '&&') {
                switch ($item[2]) {
                  case '*=':
                    $query->where($item[1], 'like', $item[3] . '%');
                    break;
                  case '=*':
                    $query->where($item[1], 'like', '%' . $item[3]);
                    break;
                  case '**':
                    $query->where($item[1], 'like', '%' . $item[3] . '%');
                    break;
                  case 'IN':
                    $query->whereIn($item[1], $item[3]);
                    break;
                  default:
                    $query->where($item[1], $item[2], $item[3]);
                }
              }
            }
          })->get();
      }
      if (count($data)) {
        return $response->withJson(array(
          "code" => 0,
          "message" => "success",
          "data" => $data
        ));
      }
      return $response->withJson(array(
        'code' => -1,
        'message' => 'Not found'
      ));
      return $response->withJson($result);
    }
    if ($params['q']) {
      $query = $params['q'];
      switch ($type) {
        case 'article':
        case 'page':
        case 'blog':
        case 'collection':
          $data = $type::where('title', 'like', '%' . $query . '%')
            ->orWhere('title_en', 'like', '%' . $query . '%')
            ->orWhere('handle', 'like', '%' . $query . '%')
            ->orWhere('handle_en', 'like', '%' . $query . '%')
            ->orWhere('content', 'like', '%' . $query . '%')
            ->orWhere('content_en', 'like', '%' . $query . '%')
            ->get();
          break;
        default:
          $data = $type::where('title', 'like', '%' . $query . '%')
            ->orWhere('title_en', 'like', '%' . $query . '%')
            ->orWhere('handle', 'like', '%' . $query . '%')
            ->orWhere('handle_en', 'like', '%' . $query . '%')
            ->get();
      }
      if (count($data)) {
        return $response->withJson(array(
          "code" => 0,
          "message" => "success",
          "data" => $data
        ));
      }
      return $response->withJson(array(
        'code' => -1,
        'message' => 'Not found'
      ));
    }
    if ($params['type']) {
      $data = $params['type']::all();
      if (count($data)) {
        return $response->withJson(array(
          "code" => 0,
          "message" => "success",
          "data" => $data
        ));
      }
      return $response->withJson(array(
        'code' => -1,
        'message' => 'Not found'
      ));
    }
    return $response->withJson(array(
      'code' => -1,
      'message' => 'Wrong syntax'
    ));
  }

}

?>
