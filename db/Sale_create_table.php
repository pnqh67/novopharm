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

$Schema->create('sale', function (Blueprint $table) {
  $table->increments('id');
  $table->string('title');
  $table->string('title_en');
  $table->text('description')->nullable();
  $table->text('description_en')->nullable();
  $table->string('type')->nullable();
  $table->integer('value')->nullable();
  $table->date('start_date')->nullable();
  $table->date('end_date')->nullable();
  $table->string('status')->nullable();
  $table->timestamps();
});
