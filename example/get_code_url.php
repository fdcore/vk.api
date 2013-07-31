<?php
ini_set('display_errors', true);
include('../vk.api.php');
include('print_var.php');
include('lime.php');
include('config.php');

$v = new Vk($config);



$links = array();

$links['code'] = $v->get_code_token('code');
$links['token'] = $v->get_code_token('token');

if(is_array($links) && count($links) > 0){
    foreach($links as $l) echo printf('<p><a href="%s" target="_blank">%s</a></p>', $l,$l);
}

print_var($links);

// test case
$t = new lime_test(2);

$t->isa_ok($links['code'], 'string','get_code_token(\'code\') returns a string');
$t->isa_ok($links['token'], 'string','get_code_token(\'token\') returns a string');