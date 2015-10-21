<?php

class WallTest extends PHPUnit_Framework_TestCase
{
    private $post_id = null;

    public function testSendPost()
    {
        global $v;

        try{
            
            $response = $v->api->wall->post(array(
                'owner_id' => $config['user_id'],
                'message' => 'Test Message',
            ));

            if(isset($response['post_id']))
                $this->post_id = $response['post_id'];

            $this->assertTrue(isset($response['post_id']));

        } catch(VkException $e){
            $this->assertFalse(TRUE);
        }

    }

    public function existsPost(){
        global $v;

        try{
            $response = $v->wall->getById( array('posts' => $config['user_id'].'_'.$this->$post_id ));

            $this->assertTrue(isset($response['items']));

        } catch(VkException $e){
            $this->assertFalse(TRUE);
        }
    }

    public testDeletePost(){
        global $v;

        try{

            $response = $v->wall->delete( array(
                'post'      => $this->$post_id,
                'owner_id'  => $config['user_id']
            ));

            $this->assertTrue(($response == 1));

        } catch(VkException $e){
            $this->assertFalse(TRUE);
        }
    }
}
