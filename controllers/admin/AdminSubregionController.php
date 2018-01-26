<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/SubRegion.php");
require_once(ROOT . '/controllers/helper.php');
use ControllerHelper as Helper;

class AdminSubregionController extends AdminController {
    public function subRegion(Request $request, Response $response){
        $id = $request->getAttribute('id');
        $subRegion = SubRegion::where('region_id', $id)->get();
        $this->view->render($response,'admin/snippet/modal-subregion',array(
            'regionid' => $id,
            'subregion' => $subRegion
        ));
    }

}

?>
