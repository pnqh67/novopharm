<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Order.php");
require_once("../models/Contact.php");
require_once("../models/Product.php");
require_once("../models/Article.php");
require_once("../models/Statistic.php");
require_once(ROOT . '/controllers/helper.php');
use ControllerHelper as Helper;

class AdminDashboardController extends AdminController{

    public function fetch(Request $request, Response $response){
        $data['orderCount'] = Order::all()->count();
        $data['contactCount'] = Contact::where('display_status','!=','delete')->count();
        $data['productCount'] = Product::where('status','!=','delete')->count();
        $data['articleCount'] = Article::where('status','!=','delete')->count();
        $data['contact'] = Contact::contactNew();
        $data['history'] = History::orderBy('created_at','desc')->take(10)->get();
        return $this->view->render($response, 'admin/dashboard', array(
            'data' => $data
        ));
    }

    public function fetchHistory(Request $request, Response $response){
        $data = History::orderBy('created_at','desc')->get();
        return $this->view->render($response, 'admin/history',array(
            'data' => $data
        ));
    }

    public function getRevenue(Request $request, Response $response){
        $numOfDays = $request->getAttribute('numOfDays');
        $revenue['sumOrder'] = Order::sumOrder($numOfDays);
        $revenue['countTotal'] = Order::countOrder($numOfDays);
        $result = Helper::response($revenue);
        return $response->withJson($result,200);
    }

    public function getVisit(Request $request, Response $response){
        $numOfDays = $request->getAttribute('numOfDays');
        $visit = Statistic::sumVisit($numOfDays);
        $result = Helper::response($visit);
        return $response->withJson($result,200);
    }

    public function settingView(Request $request, Response $response){
	    $themeDir = getThemeDir();
	    $path =  ROOT . '/public/themes/' . $themeDir . '/views/';
        $view  = new View([
          'path' => $path,
          'device' => 'desktop',
          'layout' => 'theme',
          'engine' => ''
        ]);
        return $view->render($response, "setting");
    }
}

?>
