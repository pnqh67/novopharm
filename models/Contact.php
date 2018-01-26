<?php
  use Slim\Container as ContainerInterface;
  use \Psr\Http\Message\ServerRequestInterface as Request;
  use \Psr\Http\Message\ResponseInterface as Response;

  class Contact extends Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    protected $table = 'contact';

    public function store($data) {
      $contact = new Contact;
      $contact->name = $data['name'];
      $contact->phone = $data['phone'];
      $contact->email = $data['email'];
      $contact->content = $data['content'];
      $contact->read_status = 'unread';
      $contact->reply_status = 'unreply';
      $contact->display_status = 'active';
      $contact->created_at = date('Y-m-d H:i:s');
      $contact->updated_at = date('Y-m-d H:i:s');
      if($contact->save()) return $contact->id;
      return -3;
    }

    public function update($id, $type_status, $status) {
      $contact = Contact::where('display_status','!=','delete')
        ->where('id', $id)
        ->first();
      if(!$contact) return -2;
      switch ($type_status){
        case 'read':
          if ($status == 'read') $contact->read_status = 'read';
          else $contact->read_status = 'unread';
          break;
        case 'reply':
          if ($status == 'reply') {
            $contact->reply_status = 'reply';
            $contact->read_status = 'read';
          }
          else $contact->reply_status = 'unreply';
          break;
        case 'display':
          if ($status== 'delete') $contact->display_status = 'delete';
          elseif($status == 'inactive') $contact->display_status = 'inactive';
          else $contact->display_status = 'active';
          break;
      }
      $contact->updated_at = date('Y-m-d H:i:s');
      if($contact->save()) return 0;
      return -3;
    }

    public function contactNew(){
      $read = Contact::where('display_status','active')
        ->where('read_status','unread')
        ->take(10)
        ->get();
      $reply = Contact::where('display_status','active')
        ->where('reply_status','unreply')
        ->take(10)
        ->get();
      $data['read'] = $read;
      $data['reply'] = $reply;
      return $data;
    }
  }
?>
