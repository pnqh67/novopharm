<?php

require('../vendor/autoload.php');
require('../framework/config.php');

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;


$capsule = new Capsule;
$capsule->addConnection($config['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

date_default_timezone_set('Asia/Ho_Chi_Minh');

$Schema = $capsule->schema();

$Schema->create('contact', function (Blueprint $table) {
  $table->increments('id');
  $table->string('name')->nullable();
  $table->string('phone')->nullable();
  $table->string('email')->nullable();
  $table->text('content')->nullable();
  $table->string('read_status'); //unread, read
  $table->string('reply_status'); //unreply, reply
  $table->string('display_status'); //active, inactive, delete
  $table->timestamps();
});
