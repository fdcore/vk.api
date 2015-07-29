<?php
/*
 * Class Vk
 * @author: Dmitriy Nyashkin
 * @link: https://github.com/fdcore/vk.api
 * @version: 2
 */

// Возвращаемые ошибки https://vk.com/dev/errors

class VkException extends Exception {};

class Vk{

    const API_VERSION = '5.35';
    const VERSION = '2.0'; // версия vk.api

    const CALLBACK_BLANK = 'https://oauth.vk.com/blank.html';
    const AUTHORIZE_URL = 'https://oauth.vk.com/authorize?client_id={client_id}&scope={scope}&redirect_uri={redirect_uri}&display={display}&v=5.24&response_type={response_type}';
    const GET_TOKEN_URL = 'https://oauth.vk.com/access_token?client_id={client_id}&client_secret={client_secret}&code={code}&redirect_uri={redirect_uri}';
    const METHOD_URL = 'https://api.vk.com/method/';


    public $secret_key = null;
    public $scope = array();
    public $client_id = null;
    public $access_token = null;
    public $owner_id = 0;

    /**
     * Это Конструктор (Кэп.)
     * Передаются параметры настроек
     * @param array $options
     */
    function __construct($options = array()){

        $this->scope[]='offline';

        if(count($options) > 0){
            foreach($options as $key => $value){
                if($key == 'scope' && is_string($value)){
                    $_scope = explode(',', $value);
                    $this->scope = array_merge($this->scope, $_scope);
                } else {
                    $this->$key = $value;
                }

            }
        }
    }

    /**
     * Выполнение вызова Api метода
     * @param string $method - метод, http://vk.com/dev/methods
     * @param array $vars - параметры метода
     * @return array - выводит массив данных или ошибку (но тоже в массиве)
     */
    public function api($method = '', array $vars = array()){

        $vars['v'] = self::API_VERSION;
        $vars['access_token'] = $this->access_token;

        $params = http_build_query($vars);

        $url = $this->http_build_query($method, $params);

        return (array)$this->call($url);
    }


    /**
     * Построение конечного URI для выхова
     * @param $method
     * @param string $params
     * @return string
     */
    private function http_build_query($method, $params = ''){
        return  self::METHOD_URL . $method . '?' . $params;
    }

    /**
     * Получить ссылка на запрос прав доступа
     *
     * @param string $type тип ответа (code - одноразовый код авторизации , token - готовый access token)
     * @return mixed
     */
    public function get_code_token($type="code"){

        $url = self::AUTHORIZE_URL;

        $scope = implode(',', $this->scope);

        $url = str_replace('{client_id}', $this->client_id, $url);
        $url = str_replace('{scope}', $scope, $url);
        $url = str_replace('{redirect_uri}', self::CALLBACK_BLANK, $url);
        $url = str_replace('{display}', 'page', $url);
        $url = str_replace('{response_type}', $type, $url);

        return $url;

    }

    public function get_token($code){

        $url = self::GET_TOKEN_URL;
        $url = str_replace('{code}', $code, $url);
        $url = str_replace('{client_id}', $this->client_id, $url);
        $url = str_replace('{client_secret}', $this->secret_key, $url);
        $url = str_replace('{redirect_uri}', self::CALLBACK_BLANK, $url);

        return $this->call($url);
    }

    private function call($url = ''){

        if(function_exists('curl_init')) $json = $this->curl_post($url); else $json = file_get_contents($url);

        $json = json_decode($json, true);

        // Произошла ошибка на стороне VK, коды ошибок тут https://vk.com/dev/errors
        if(isset($json['error'], $json['error']['error_msg'], $json['error']['error_code'])){

            throw new VkException($json['error']['error_msg'], $json['error']['error_code']);
        }

        if(isset($json['response'])) return $json['response'];

        return $json;
    }

    // @deprecated
    private function curl_get($url)
    {
        if(!function_exists('curl_init')) return false;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $tmp = curl_exec ($ch);
        curl_close ($ch);
        $tmp = preg_replace('/(?s)<meta http-equiv="Expires"[^>]*>/i', '', $tmp);
        return $tmp;
    }

    private function curl_post($url){

        if(!function_exists('curl_init')) return false;

        $param = parse_url($url);

        if( $curl = curl_init() ) {

            curl_setopt($curl, CURLOPT_URL, $param['scheme'].'://'.$param['host'].$param['path']);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $param['query']);
            $out = curl_exec($curl);

            curl_close($curl);

            return $out;
        }

        return false;
    }
    /**
     * @param array $options
     */
    public function set_options($options = array()){

        if(count($options) > 0){
            foreach($options as $key => $value){
                if($key == 'scope' && is_string($value)){
                    $_scope = explode(',', $value);
                    $this->scope = array_merge($this->scope, $_scope);
                } else {
                    $this->$key = $value;
                }

            }
        }

    }

    /**
     * @param bool $gid
     * @param array $files
     * @return array|bool
     */
    public function upload_photo($gid = false, $files = array(), $return_ids = false){

        if(count($files) == 0) return false;
        if(!function_exists('curl_init')) return false;

        $data_json = $this->api('photos.getWallUploadServer', array('group_id'=> intval($gid)));

        if(!isset($data_json['upload_url'])) return false;

        $temp = array_chunk($files, 4);

        $files = array();
        $attachments = array();

        foreach ($temp[0] as $key => $data) {
            $path = realpath($data);

            if($path){
              $files['file' . ($key+1)] = (class_exists('CURLFile', false)) ? new CURLFile(realpath($data)) : '@' . realpath($data);
            }
        }

        $upload_url = $data_json['upload_url'];

        $ch = curl_init($upload_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $files);

        $upload_data = json_decode(curl_exec($ch), true);

        $upload_data['group_id'] = intval($gid);

        $response = $this->api('photos.saveWallPhoto', $upload_data);

        if(count($response) > 0){

            foreach($response as $photo){

                if($return_ids) $attachments[] = $photo['id']; else $attachments[] = 'photo'.$photo['owner_id'].'_'.$photo['id'];
            }
        }

        return $attachments;

    }

    /**
     * Заливка документа (например GIF файл)
     *
     * @param bool $gid
     * @param $file
     * @return bool|string
     */
    public function upload_doc($gid = false, $file){

        if(!is_string($file)) return false;
        if(!function_exists('curl_init')) return false;

        $data_json = $this->api('docs.getUploadServer', array('gid'=> intval($gid)));

        var_dump($data_json);

        if(!isset($data_json['upload_url'])) return false;

        $attachment = false;

        $path = realpath($file);

        if(!$path) return false;

        $files['file'] = (class_exists('CURLFile', false)) ? new CURLFile($file) : '@' . $file;

        $upload_url = $data_json['upload_url'];

        $ch = curl_init($upload_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $files);

        $upload_data = json_decode(curl_exec($ch), true);

        $response = $this->api('docs.save', $upload_data);

        if(count($response) > 0){

            foreach($response as $photo){

                $attachment = 'doc'.$photo['owner_id'].'_'.$photo['did'];
            }
        }

        return $attachment;

    }

    /**
     *
     * Заливка видео
     *
     * http://vk.com/dev/video.save
     *
     * @param array $options
     * @param bool $file
     * @return bool|string
     */
    public function upload_video($options = array(), $file = false){

        if(!is_array($options)) return false;
        if(!function_exists('curl_init')) return false;

        $data_json = $this->api('video.save', $options);

        if(!isset($data_json['upload_url'])) return false;

        $attachment = 'video'.$data_json['owner_id'].'_'.$data_json['video_id'];

        $upload_url = $data_json['upload_url'];
        $ch = curl_init($upload_url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");

        // если указан файл то заливаем его отправкой POST переменной video_file
        if($file && file_exists($file)){
            //@todo надо протестировать заливку
            $path = realpath($file);

            if(!$path) return false;

            $files['video_file'] = (class_exists('CURLFile', false)) ? new CURLFile($file) : '@' . $file;

            curl_setopt($ch, CURLOPT_POSTFIELDS, $files);
            curl_exec($ch);

        // иначе просто обращаемся по адресу (ну надо так!)
        } else {

            curl_exec($ch);
        }

        return $attachment;

    }

}
