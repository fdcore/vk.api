<?php

// http://vk.com/dev/wall.post

require '_connect_vk.php';

$response = $v->wall->post(array('message' => 'Тестирую API из https://github.com/fdcore/vk.api'));

print_r($response);
