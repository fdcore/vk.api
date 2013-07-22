<?php
// http://vk.com/dev/wall.delete

ini_set('display_errors', true);
include('../vk.sa.api.php');
include('print_var.php');
include('lime.php');
include('config.php');


$v = new Vk($config);

// test case
$t = new lime_test(1);

$response = $v->api('wall.delete', array('post_id' => 0));

$t->isa_ok($response[0], 'integer', 'return 1 for success');
