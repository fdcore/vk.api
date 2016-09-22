<?php

require 'vendor/autoload.php';

class SimpleTest extends PHPUnit_Framework_TestCase
{
    private $config = [];

    function __construct(){
      $config['secret_key'] = 'mysecretkey'; // ваш секретный ключ приложения
      $config['client_id'] = 1234567; // (обязательно) получить тут https://vk.com/apps?act=manage где ID приложения = client_id
      $config['user_id'] = 1234567; // ваш номер пользователя в вк
      $config['access_token'] = 'mycooltoken'; // ваш токен доступа
      $config['scope'] = 'wall,photos,friends,groups';  // права доступа

      $this->config = $config;

      $this->v = new Vk($config);
    }

    public function testGetAccessToken(){

        $access_token_url = urldecode($this->v->get_code_token('token'));

        $this->assertTrue(isset($access_token_url));
        $this->assertTrue($access_token_url == 'https://oauth.vk.com/authorize?response_type=token&client_id='.$this->config['client_id'].'&redirect_uri=https://oauth.vk.com/blank.html&display=page&version=5.53&scope=offline,'.$this->config['scope']);

        $code_url = urldecode($this->v->get_code_token('code'));

        $this->assertTrue(isset($code_url));
        $this->assertTrue($code_url == 'https://oauth.vk.com/authorize?response_type=code&client_id='.$this->config['client_id'].'&redirect_uri=https://oauth.vk.com/blank.html&display=page&version=5.53&scope=offline,'.$this->config['scope']);

    }

    public function testBasicCall(){

      $this->v->set_options(array('debug' => true, 'access_token' => 'mycooltoken'));

      $url = $this->v->wall->post(array(
          'owner_id' => $this->config['user_id'],
          'message' => 'DEMO',
      ));

      $this->assertTrue($url == 'https://api.vk.com/method/wall.post?owner_id='.$this->config['user_id'].'&message=DEMO&v=5.53&access_token=mycooltoken');

      // вариант вызова 2

      $url2 = $this->v->api('wall.post', array(
          'owner_id' => $this->config['user_id'],
          'message' => 'DEMO',
      ));

      $this->assertTrue($url2 == 'https://api.vk.com/method/wall.post?owner_id='.$this->config['user_id'].'&message=DEMO&v=5.53&access_token=mycooltoken');
    }

   public function testRealCall(){

          $this->v->set_options(array('debug' => false, 'access_token' => ''));

          $response = $this->v->groups->getMembers(array(
              'group_id' => 1,
          ));

          $this->assertTrue($response != false);
          $this->assertTrue(isset($response['count']));

    }

}
