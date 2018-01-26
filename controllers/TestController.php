<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once('helper.php');
use ControllerHelper as Helper;

class TestController extends Controller {

  public function truyen() {
    createPermisson();
  }

  public function test(Request $request, Response $response){
    return $this->view->render($response,'test');
  }

  public function sendMail() {
    $to = 'pnqh67@gmail.com';
    $subject = 'Test Mail';
    $variables = array();
    $variables['user_name'] = 'Quý';
    $variables['user_email'] = 'quy@abcxyz.com';
    $variables['user_role'] = 'Admin';
    $variables['link_create_password'] = 'link';
    $template = file_get_contents('../framework/mail-template/forget-password.html');
    foreach ($variables as $key => $value) {
      $template = str_replace('{{'.$key.'}}', $value, $template);
    }
    PHPMailer($to, $subject, $template);
  }

}
?>
