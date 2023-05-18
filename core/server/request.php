<?php
namespace core\server;

use core\server\Query;
use core\server\Http;
use core\server\File;

class Request {
    private ?string $route_path = null;
    public ?string $request_path = null;
    public ?Query $query = null;
    public ?object $body = null;
    public ?object $params = null;
    public ?array $files = [];

    function __construct()
    {
        $this->query = new Query();
        $this->parse_body();
        $this->request_path = Http::sanitize_url( Http::get_path() );
    }
    //PARSE THE PLAIN DATA TO OBJECT PROPERTY
    private function parse_body() {
        $content_type = Http::get_contetn_type();
        if($content_type == "none") {
            $this->body = null;
            return;
        }
        if($content_type == "text") {
            $this->body = Http::get_request_data();
            return;
        }
        if($content_type == "json") {
            $this->body = json_decode( Http::get_request_data() );
            return;
        }
        if($content_type == "urlencode") {
            $this->body = (object) $this->parse_urlencode_to_body();
            return;
        }
        if($content_type == "form-data") {
            if(Http::get_request_method() === "POST") {
                $this->body = (object) $_POST;
                $this->parse_file_by_post();
                return;
            }
            $this->body = (object) $this->parse_form_data_to_body();
        }
    }
    //GET DATA OF THE FILE SEND BY POST
    private function parse_file_by_post():void {
        $files = [];
        foreach($_FILES as $key => $value) {
            if(is_array($value["name"])) {
                for ($i=0; $i < count($value["name"]); $i++) { 
                    $file = new File();
                    $file->name = $value["name"][$i];
                    $file->full_path = $value["full_path"][$i];
                    $file->type = $value["type"][$i];
                    $file->tmp_name = $value["tmp_name"][$i];
                    $file->error = (int) $value["error"][$i];
                    $file->size = (int) $value["size"][$i];
                    $files[$key][] = $file;
                }
                continue;
            }
            $files[$key] = $value;
        }
        $this->files = $files;
    }
    //PARSE DATA TO ARRAY RECIVED WITH CONTENT TYPE FROM-DATA
    private function parse_form_data_to_body():array {
        $plain_data = Http::get_request_data();
        preg_match('/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);
        $boundary = $matches[1];

        $a_blocks = preg_split("/-+$boundary/", $plain_data);
        array_pop($a_blocks);

        $body = [];
        
        foreach($a_blocks as $key => $value)
        {
            if(empty($value)) continue;

            if(preg_match("/content-type/i", $value)) {
                preg_match("/name=\"([^\"]+)\"/", $value, $match_name);
                preg_match("/filename=\"([\^]+)\"/", $value, $match_filename);
                preg_match("/content-type:\s+([^\n\r]+)/i", $value, $match_content_type);
                preg_match("/[^\n\r]*[\n\r]{4}([^\n\r].*)?[\n\r]{2}$/s", $value, $match_file_content);
                $name = $match_name[1];
                $file = new File(true);
                $file->name = $match_filename[1];
                $file->full_path = $match_filename[1];
                $file->type = $match_content_type[1];
                $file->error = 0;
                $file->size = strlen($match_file_content[1]);
                $file->tmp_name = $match_file_content[1];
                if(preg_match("/(\w+)\[\]/", $name, $match_name)) $this->files[$match_name[1]][] = $file;
                else $this->files[$name] = $file;
            }
            else {
                preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $value, $matches);
                $name = $matches[1];
                if(preg_match("/(\w+)\[\]/", $name, $match_name)) $body[$match_name[1]][] = $matches[2];
                else $body[$name] = $matches[2];
            }
        }
        return $body;
    }
    //PARSE DATA TO ARRAY RECIVED WITH CONTENT TYPE URLENCODED
    private function parse_urlencode_to_body():array {
        $plain_data = Http::get_request_data();
        $separated_by_props = preg_split("/&/", $plain_data, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($separated_by_props as $index => $value) {
            $array_prop_value = preg_split("/=/", $value, -1, PREG_SPLIT_NO_EMPTY);
            unset($separated_by_props[$index]);
            $separated_by_props[urldecode($array_prop_value[0])] = urldecode($array_prop_value[1]);
        }
        return $separated_by_props;
    }
    //GET PARAMS OF THE DINAMIC ROUTE PATH
    private function get_params (string $route_path, string $request_path):array {
        $params = [];
        if(preg_match_all("/:(\w+)\??/", $route_path, $matches)) {
            $replace = preg_replace(["/\//","/:(\w+)\??/"], ["\/", "([^\/]*)"], $route_path);
            preg_match("/$replace/",$request_path,$matches_a);
            foreach ($matches[1] as $key => $param_name) {
                $params[$param_name] = $matches_a[$key + 1 ];
            }
        }
        return $params;
    }
    function __set($name, $value)
    {
        $this->$name = $value;
        if($name === "route_path") $this->params = (object) $this->get_params($this->route_path, $this->request_path);
    }

}
