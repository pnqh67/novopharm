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

$Schema->create('seo', function (Blueprint $table) {
  $table->increments('id');
  $table->text('g_meta_title')->nullable();
  $table->text('g_meta_title_en')->nullable();
  $table->text('g_meta_description')->nullable();
  $table->text('g_meta_description_en')->nullable();
  $table->text('g_meta_keyword')->nullable();
  $table->text('g_meta_keyword_en')->nullable();
  $table->text('g_meta_robots')->nullable();
  $table->text('f_meta_title')->nullable();
  $table->text('f_meta_title_en')->nullable();
  $table->text('f_meta_description')->nullable();
  $table->text('f_meta_description_en')->nullable();
  $table->text('f_image')->nullable();
  $table->string('type')->nullable();
  $table->integer('type_id')->nullable();
  $table->timestamps();
});
