<?php

require '../vk.php'; //

$config['secret_key'] = ''; // ваш секретный ключ приложения
$config['client_id'] = 0; // (обязательно) получить тут https://vk.com/apps?act=manage где ID приложения = client_id
$config['user_id'] = 0; // ваш номер пользователя в вк
$config['access_token'] = ''; // ваш токен доступа
$config['scope'] = 'wall,photos,friends,groups';  // права доступа

$v = new Vk($config);
