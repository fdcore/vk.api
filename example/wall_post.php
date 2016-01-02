<?php

// http://vk.com/dev/wall.post

require '_connect_vk.php';

$attachments = $v->upload_photo(0, array('1737759.jpg'));

  // публикация на стене
  $response = $v->wall->post(array(
      'message'=>'test 1737759.jpg',
      'attachments' => implode(',', $attachments)
    )
  );
