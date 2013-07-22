<?php

// http://vk.com/dev/wall.post

ini_set('display_errors', true);
include('../vk.api.php');
include('print_var.php');
include('lime.php');
include('config.php');


$v = new Vk($config);

// test case
$t = new lime_test(2);

$response = $v->api('wall.post', array('message' => 'I testing API form https://github.com/fdcore/vk.api'));

print_var($response);

$t->isa_ok($response, 'array', 'json array response');
$t->isa_ok($response['post_id'], 'integer', 'return post_id');