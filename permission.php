<?php


$PERMISSIONS = [];
$PERMISSIONS[] = 'order';
$PERMISSIONS['order']['list'] = [
  'method' => 'GET',
  'endpoint' => '/product'
];

// foreach($PERMISSIONS as $group) {
//   foreach($group as $permission) {
//
//   }
// }
//
//
//
//
// Table: Permission
// Table: Permission Group
