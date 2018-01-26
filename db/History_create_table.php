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

$Schema->create('history', function (Blueprint $table) {
  $table->increments('id');
  $table->integer('user_id')->nullable();
  $table->string('user_name')->nullable();
  $table->string('user_type')->nullable(); //0 admin, 1 user
  $table->string('action')->nullable(); // create, edit, delete
  $table->string('type')->nullable(); // product, collection, article, ...
  $table->integer('type_id')->nullable();
  $table->string('type_title')->nullable();
  $table->timestamps();
});
