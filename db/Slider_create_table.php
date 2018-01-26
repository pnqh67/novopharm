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

$Schema->create('slider', function (Blueprint $table) {
    $table->increments('id');
    $table->integer('parent_id');
    $table->string('title');
    $table->string('title_en')->nullable();
    $table->string('handle');
    $table->string('description')->nullable();
    $table->string('description_en')->nullable();
    $table->string('image')->nullable();
    $table->string('link')->nullable();
    $table->string('status');
    $table->timestamps();
});
