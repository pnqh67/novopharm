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

$Schema->create('comment', function (Blueprint $table) {
    $table->increments('id');
    $table->integer('parent_id'); //0 hoặc id
    $table->integer('customer_id');
    $table->text('content');
    $table->string('type'); //bai viet hoặc san pham
    $table->integer('type_id'); //id cua bai viet or san pham
    $table->string('status'); //active, inactive, delete
    $table->timestamps();
});
