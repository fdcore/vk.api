Vkontakte Api for PHP
======================

[НА РУССКОМ](README_RU.md)

Work with Vkontakte API for StandAlone application on php.

To work with api you need to perform several actions:

1. Create **"Standalone-app"** https://vk.com/editapp?act=create
2. Get access_token (see below)
3. Set **client_id** and **secret_key** application.


## Install

```
composer require fdcore/vk.api
```


## Usage

With composer

```php
<?php
include 'vendor/autoload.php';

use fdcore\vk.api\Vk;
$v = new Vk();
?>
```

Without

```php
<?php
include 'vk.php';
$v = new Vk();
?>
```

## Get access_token
Execute method get_code_token for get link. Go to link and get access app, after you redirected on blank page with access_token on hash url.

```php
include_once 'vk.php';

$v = new Vk(array(
	'client_id' => 12345, // (required) app id
	'secret_key' => 'XXXXXX', // (required) get on https://vk.com/editapp?id=12345&section=options
	'user_id' => 12345, // your user id on vk.com
	'scope' => 'wall', // scope access
	'v' => '5.35' // vk api version
));

$url = $v->get_code_token();

echo $url;
```

Variable **$url** it will contain a link for redirect to which you will be asked to log in and provide the right application, and then you are thrown on a blank page and the URL is access_token=**your token**.

## Execute Api

To receive necessary permissions during authorization, when the authorization window opens you need to pass scope parameter containing names of the required permissions separated by space or comma.

https://vk.com/dev/permissions

Example

```php
$config['secret_key'] = 'your secret key';
$config['client_id'] = 12345; // app client id
$config['user_id'] = 12345; // id current user (required
$config['access_token'] = 'your access token';
$config['scope'] = 'wall,photos,video'; // scope for get access token

$v = new Vk($config);

// example post to user wall
// Method info https://vk.com/dev/wall.post

$response = $v->api('wall.post', array(
    'message' => 'I testing API form https://github.com/fdcore/vk.api'
));

// or

$response = $v->wall->post(array(
    'message' => 'I testing API form https://github.com/fdcore/vk.api'
));
```

## Upload files

- Upload video **$v->upload_video()**
- Upload photo to wall **$v->upload_photo()**
- Upload documents **$v->upload_doc()**

### Example upload photo

For upload photo, use method **upload_photo()**.

Params:

- **$gid** - (*Intager*) (default is 0) id community, for upload photo.
- **$files** - (*Array*) path to file (example array('bear.jpg', 'vodka.jpg'))
- **$return_ids** - (*Bool*) (default is false) return files id's or complete strings for attach (example photo12345_6789) (photo - type, 12345 - user, 6789 - photo id)

```php
// upload photo on server
$attachments = $v->upload_photo(0, array('bear.jpg', 'vodka.jpg'), false);

// публикация на стене
$response = $v->wall->post(array(
    'message'=>'my cool photo',
    'attachments' => implode(',', $attachments)
  )
);
```

### Example upload video

```php
// embed from YouTube without upload

$attach_video = $v->upload_video(array(
   'link'=>'https://youtu.be/exAmqVtYbis',
   'title' => 'Hatsune Miku Project Diva 2nd Opening Full HD',
   'description' => "First Song: \"Kocchi Muite baby\" by ryo and kz",
   'wallpost' => 1
));

// upload video on VK.com
$attach_name = $v->upload_video(
   array('name' => 'Test video',
       'description' => 'My description',
       'wallpost' => 1,
       'group_id' => 0
   ), 'video.mp4'); // video.mp4 - full path to video file on server

```

### Example upload documents

```php
    $attach_doc_file = $v->upload_doc(0, 'funny.gif');

    if ( is_string($attach_doc_file) ) echo $attach_doc_file;
```
