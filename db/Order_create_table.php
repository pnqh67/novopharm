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

$Schema->create('order', function (Blueprint $table) {
    $table->increments('id');
    $table->integer('customer_id');
    $table->string('payment_method');
    $table->integer('shipping_price');
    $table->integer('discount');
    $table->string('order_status'); // new, confirm, done, cancel, return
    $table->integer('payment_status'); // 0, 1
    $table->integer('shipping_status'); // 0, 1, 2
    $table->integer('subtotal');
    $table->integer('total');
    $table->text('notes')->nullable();
    $table->text('reason_cancel')->nullable();
    $table->string('status'); //active, inactive, delete
    $table->timestamps();
});
