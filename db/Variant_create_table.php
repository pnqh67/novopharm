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

$Schema->create('variant', function (Blueprint $table) {
    $table->increments('id');
    $table->integer('product_id');
    $table->string('option_1')->nullable()->default(''); //title child attribute
    $table->string('option_2')->nullable()->default('');
    $table->string('option_3')->nullable()->default('');
    $table->string('option_4')->nullable()->default('');
    $table->string('option_5')->nullable()->default('');
    $table->string('option_6')->nullable()->default('');
    $table->integer('price');
    $table->integer('price_compare')->nullable();
    $table->integer('inventory')->nullable();
    $table->string('status')->nullable();
    $table->timestamps();
});
