<?php

require('../vendor/autoload.php');
require('../framework/config.php');
define('ROOT', dirname(dirname(__FILE__)));

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

  $files = scandir('.');
  $files = array_diff($files, array('.', '..', __FILE__));
  foreach ($files as $file) {
    try {
      require_once($file);
    } catch (Exception $e) {
      echo $e;
    }
  }

  $capsule = new Capsule;
  $capsule->addConnection($config['db']);
  $capsule->setAsGlobal();
  $capsule->bootEloquent();
  $passwordHash = password_hash('admin@123', PASSWORD_DEFAULT);

  Capsule::insert('INSERT INTO ' . Capsule::getTablePrefix()
    . 'user (id, role_id, name, email, phone, password, random, created_at, updated_at) '
    . 'VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
    [
      1, 0, 'Admin', 'admin@gmail.com', '0123456789', $passwordHash,
      '', date('Y-m-d H:i:s'), date('Y-m-d H:i:s')
    ]
  );

  if (!file_exists(ROOT . '/public/uploads')) {
    mkdir(ROOT . '/public/uploads', 0755, true);
  }

  if (!file_exists(ROOT . '/public/uploads/origin')) {
    mkdir(ROOT . '/public/uploads/origin', 0755, true);
  }

  if (!file_exists(ROOT . '/public/images')) {
    mkdir(ROOT . '/public/images', 0755, true);
  }
