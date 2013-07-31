vk.api
======

Работа с API Вконтакте для StandAlone приложений на языке php.

Для работы с api вам необходимо выполнить несколько действий:

1. Создать "Standalone-приложение" https://vk.com/editapp?act=create
2. Получить access_token (об этом ниже)
3. Классу нужно передать client_id приложения и секретный ключ который вам даётся при создании приложения

## Получение access_token

Выполним метод get_code_token для получения ссылки которая вернёт нам code

```php
	include('vk.api.php');
	$v = new Vk($config);
	$url = $v->get_code_token();
	
	echo $url;
```

Переменная $url будет содержать ссылку при переходе на которую вас попросят авторизоваться и предоставить права приложению, после чего вас перекинут на пустую страницу и в URL будет code=<нужный код>.

Для получения токена и owner_id выполните метод get_token
	
	$response = $v->get_token('ff604712f10b2b6ac0');
	
	var_dump($response);
	
## Выполнение Api

Для выполнения определённых Api вам необходимы на это права, для этого при создании токена нужно указать нужные scope.

```php
	$config['secret_key'] = 'ваш секретный ключ приложения';
	$config['client_id'] = 0; // номер приложения
	$config['user_id'] = 0; // id текущего пользователя (не обязательно)
	$config['access_token'] = 'ваш токен доступа';
	$config['scope'] = 'wall,photos,video'; // права доступа к методам (для генерации токена)
	
	$v = new Vk($config);
	
	// пример публикации сообщения на стене пользователя
	// значения массива соответствуют значениям в Api https://vk.com/dev/wall.post
	
	$response = $v->api('wall.post', array('message' => 'I testing API form https://github.com/fdcore/vk.api'));
```

## Заливка файлов

Для заливки файлов в данный момент есть 3 метода:

- Загрузка видеозаписей **$v->upload_video()**
- Загрузка фотографий на стену пользователя **$v->upload_photo()**
- Загрузка документов **$v->upload_doc()**

### Пример загрузки фото

```php
    // загрузка фото на сервер
    $attachments = $v->upload_photo(0, array('4b67bhWrc4g.jpg', 'n52W2BdXdYE.jpg'));

    // публикация на стене
    $response = $v->api('wall.post', array('message'=>'я публикую фотографии', 'attachments' => implode(',', $attachments)));

```

### Пример загрузки видео

```php

   // встраивание видео с YouTube без заливки

   $attach_video = $v->upload_video(array(
       'link'=>'http://www.youtube.com/watch?v=5ZeA4AMrcd8',
       'title' => 'Tasogare Otome X Amnesia / OST (Nika Lenina Russian Version)',
       'description' => "Трек оригинал: Hiiragi Nao - Requiem",
       'wallpost' => 1
   ));

   // заливка видео на VK.com

   $attach_name = $v->upload_video(
       array('name' => 'Fadoo Sama',
           'description' => 'AMV',
           'wallpost' => 1,
           'group_id' => 0
       ), '04975.Fadoo-Sama-DUALITY.amvnews.ru.mp4');

```

### Пример загрузки документа

```php
    $attach_doc_file = $v->upload_doc(0, 'iZKE4JdP4Q0mT.jpg');

    if ( is_string($attach_doc_file) ) echo $attach_doc_file;

```