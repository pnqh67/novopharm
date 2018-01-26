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

$Schema->create('product', function (Blueprint $table) {
    $table->increments('id');
    $table->string('title')->nullable();
    $table->string('title_en')->nullable();
    $table->string('handle')->nullable();
    $table->string('handle_en')->nullable();
    $table->string('featured_image')->nullable();
    $table->boolean('inventory_management')->nullable();
    $table->boolean('in_stock')->nullable();
    $table->text('description')->nullable();
    $table->text('description_en')->nullable();
    $table->text('content')->nullable();
    $table->text('content_en')->nullable();
    $table->integer('sell')->nullable();
    $table->integer('stock')->nullable();
    $table->integer('view')->nullable();
    $table->string('status')->nullable();
    $table->integer('option_1')->nullable()->default(0); //id parent attribute
    $table->integer('option_2')->nullable()->default(0);
    $table->integer('option_3')->nullable()->default(0);
    $table->integer('option_4')->nullable()->default(0);
    $table->integer('option_5')->nullable()->default(0);
    $table->integer('option_6')->nullable()->default(0);
    $table->timestamps();
});
