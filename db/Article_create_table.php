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

$Schema->create('article', function (Blueprint $table) {
  $table->increments('id');
  $table->integer('blog_id');
  $table->string('title');
  $table->string('title_en')->nullable();
  $table->string('handle');
  $table->string('handle_en')->nullable();
  $table->text('description')->nullable();
  $table->text('description_en')->nullable();
  $table->text('content')->nullable();
  $table->text('content_en')->nullable();
  $table->string('image')->nullable();
  $table->string('status');
  $table->integer('view');
  $table->timestamps();
});
