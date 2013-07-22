<?php
ini_set('display_errors', true);
include('../vk.api.php');
include('print_var.php');
include('lime.php');
include('config.php');


$v = new Vk($config);

// test case
$t = new lime_test(1);

$response = $v->get_code_token();
print_var($response);


$t->isa_ok($response, 'string','strtolower() returns a string');