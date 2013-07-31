<?php
ini_set('display_errors', true);
include('../vk.api.php');
include('print_var.php');
include('lime.php');
include('config.php');

$v = new Vk($config);

$response = $v->get_token('c413608d4f8a0ffc7b');

print_var($response);