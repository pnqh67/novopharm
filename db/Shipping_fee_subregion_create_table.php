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

$Schema->create('shipping_fee_subregion', function (Blueprint $table) {
    $table->increments('id');
    $table->integer('subregion_id');
    $table->integer('shipping_fee_region_id');
    $table->integer('price');
    $table->timestamps();
});
