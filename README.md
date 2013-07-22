vk.api
======

Работа с API Вконтакте для StandAlone приложений на языке php.

Для работы с api вам необходимо выполнить несколько действий:

1. Создать "Standalone-приложение" https://vk.com/editapp?act=create
2. Получить access_token (об этом ниже)
3. Классу нужно передать client_id приложения и секретный ключ который вам даётся при создании приложения

## Получение access_token

Выполним метод get_code_token для получения ссылки которая вернёт нам code

	include('vk.api.php');
	$v = new Vk($config);
	$url = $v->get_code_token();
	
	echo $url;
	
Переменная $url будет содержать ссылку при переходе на которую вас попросят авторизоваться и предоставить права приложению, после чего вас перекинут на пустую страницу и в URL будет code=<нужный код>.

Для получения токена и owner_id выполните метод get_token
	
	$response = $v->get_token('ff604712f10b2b6ac0');
	
	var_dump($response);
	
# Выполнение Api

Для выполнения определённых Api вам необходимы на это права, для этого при создании токена нужно указать нужные scope.

	$config['secret_key'] = 'ваш секретный ключ приложения';
	$config['client_id'] = 0; // номер приложения
	$config['user_id'] = 0; // id текущего пользователя (не обязательно)
	$config['access_token'] = 'ваш токен доступа';
	$config['scope'] = 'wall,photos,video'; // права доступа к методам (для генерации токена)
	
	$v = new Vk($config);
	
	// пример публикации сообщения на стене пользователя
	// значения массива соответствуют значениям в Api https://vk.com/dev/wall.post
	
	$response = $v->api('wall.post', array('message' => 'I testing API form https://github.com/fdcore/vk.api'));