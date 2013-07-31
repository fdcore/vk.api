<?php

// http://vk.com/dev/upload_files?f=%D0%97%D0%B0%D0%B3%D1%80%D1%83%D0%B7%D0%BA%D0%B0%20%D0%B4%D0%BE%D0%BA%D1%83%D0%BC%D0%B5%D0%BD%D1%82%D0%BE%D0%B2

ini_set('display_errors', true);
include('../vk.api.php');
include('print_var.php');
include('lime.php');
include('config.php');

// нужны права docs

$v = new Vk($config);
// test case
$t = new lime_test(2);

// получаем имя аттача (string)
$attach_doc_file = $v->upload_doc(0, 'iZKE4JdP4Q0mT.jpg');

print_var($attach_doc_file);

if($attach_doc_file){

    $response = $v->api('wall.post', array('message' => 'тестирую api документов', 'attachments' => $attach_doc_file));

    print_var($response);

} else $response = false;

$t->isa_ok($response, 'array', 'json array response');
$t->isa_ok($response['post_id'], 'integer', 'return post_id');