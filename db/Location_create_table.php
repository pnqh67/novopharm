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

$Schema->create('location', function (Blueprint $table) {
    $table->increments('id');
    $table->string('region_id');
    $table->string('subregion_id');
    $table->string('name');
    $table->string('address');
    $table->string('phone_number');
    $table->string('status');
    $table->timestamps();
});
