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

$Schema->create('shipping_fee_region', function (Blueprint $table) {
    $table->increments('id');
    $table->string('title');
    $table->string('type');
    $table->integer('price');
    $table->integer('from');
    $table->integer('to');
    $table->integer('region_id');
    $table->timestamps();
});
