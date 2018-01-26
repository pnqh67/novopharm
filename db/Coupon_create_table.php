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

$Schema->create('coupon', function (Blueprint $table) {
    $table->increments('id');
    $table->string('title');
    $table->string('title_en')->nullable();
    $table->text('description')->nullable();
    $table->text('description_en')->nullable();
    $table->string('code');
    $table->string('type'); // value, percent
    $table->integer('max_value_percent')->nullable();
    $table->integer('value');
    $table->integer('min_value_order');
    $table->integer('usage_count');
    $table->integer('usage_left');
    $table->date('start_date');
    $table->date('end_date');
    $table->string('status');
    $table->timestamps();
});
