<?php
namespace core\server;

class Http {
    //GET THE ULR OF THE RECEIVED REQUEST THAT USES THIS FRAMEWORK
    static function get_path():false|string {
        $request_uri = explode("?", $_SERVER["REQUEST_URI"])[0];
        $script_name = $_SERVER["SCRIPT_NAME"];

        $script_name_array = preg_split("/\//", $script_name, -1, PREG_SPLIT_NO_EMPTY);
        $request_uri_array = preg_split("/\//", $request_uri, -1, PREG_SPLIT_NO_EMPTY);
        
        if($script_name_array === false || $request_uri_array === false) return false;
        array_shift($script_name_array);

        for ($i=0; $i < count($script_name_array); $i++) { 
            array_shift($request_uri_array);
        }
        
        $path = implode("/", $request_uri_array);

        return urldecode($path);
    }
    //GET THE METHOD OF THE REQUEST RECEIVED
    static function get_request_method():string {
        return strtoupper($_SERVER["REQUEST_METHOD"]);
    }
    //GET CONTENT TYPE OF THE REQUEST
    static function get_contetn_type():string {
        $content_type = $_SERVER["CONTENT_TYPE"] ?? NULL;
        $accepts = [
            "json" => "/application\/json/i",
            "text" => "/text\/plain/i",
            "form-data" => "/multipart\/form-data/i",
            "urlencode" => "/application\/x-www-form-urlencoded/i",
        ];
        if (!$content_type) return "none";
        foreach ($accepts as $accept => $pattern) {
            $match = preg_match($pattern, $content_type);
            if(!$match) continue;
            return $accept;
        }
        return "none";
    }
    //GET STRING DATA FOR THE REQUEST
    static function get_request_data():string {
        return file_get_contents("php://input");
    }
    //CLEAN THE URL
    static function sanitize_url(string $url):string {
        $array = preg_split("/\//", $url, -1, PREG_SPLIT_NO_EMPTY);
        $implode = implode("/", $array);
        if($implode === "") return "/";
        return "/$implode/";
    }
}