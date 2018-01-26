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

$Schema->create('brand', function (Blueprint $table) {
    $table->increments('id');
    $table->string('name');
    $table->string('name_en');
    $table->string('handle');
    $table->string('handle_en');
    $table->text('description')->nullable();
    $table->text('description_en')->nullable();
    $table->string('image')->nullable();
    $table->boolean('highlight')->nullable();
    $table->boolean('display')->nullable();
    $table->timestamps();
});