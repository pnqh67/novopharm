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

$Schema->create('customer', function (Blueprint $table) {
  $table->increments('id');
  $table->string('name');
  $table->string('email');
  $table->string('phone')->nullable();
  $table->string('password')->nullable();
  $table->string('address')->nullable();
  $table->string('region')->nullable();
  $table->string('subregion')->nullable();
  $table->string('random')->nullable();
  $table->string('favorite_product')->nullable();
  $table->timestamps();
});
