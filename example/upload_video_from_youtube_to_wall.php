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

$attach_video = $v->upload_video(array(
    'link'=>'http://www.youtube.com/watch?v=5ZeA4AMrcd8',
    'title' => 'Tasogare Otome X Amnesia / OST (Nika Lenina Russian Version)',
    'description' => "Трек оригинал: Hiiragi Nao - Requiem
Аранжировка: Аlex Makshanov
Перевод: Evafan
Работа со звуком: AnsverITO
Вокал: Nika Lenina
Видео: Sadzurami",
    'wallpost' => 1
    ));

$response = $v->api('wall.post', array('message' => 'тестирую api', 'attachments' => $attach_video));

print_var($response);

$t->isa_ok($response, 'array', 'json array response');
$t->isa_ok($response['post_id'], 'integer', 'return post_id');