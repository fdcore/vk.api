<?php
// http://vk.com/dev/wall.delete

require '_connect_vk.php';

$response = $v->api('wall.delete', array('post_id' => 0));
