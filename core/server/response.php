<?php
namespace core\server;

use Exception;

class Response {
    function status(int $code): Response {
        http_response_code($code);
        return $this;
    }
    function text(string $text) {
        header("Content-Type: text/plain");
        echo $text;
    }
    function json(mixed $value){
        header("Content-Type: application/json");
        echo json_encode($value);
    }
    function html(string $html_text){
        header("Content-Type: text/html");
        echo $html_text;
    }
    function view_html(string $html_path) {
        header("Content-Type: text/html");
        readfile($html_path);
    }
    function send_file(string $file_path, string $replace_filename = null, bool $inline = true){
        extract($this->get_info_to_response($file_path, $replace_filename));
        set_time_limit(300);

        header("Content-Type: application/octet-stream");
        header("Content-Transfer-Encoding: Binary");
        header("Pragma: no-cache");
        header("Content-Length: $length");

        if(!$inline) {header(sprintf("Content-disposition: attachment; filename=\"%s\"", $filename));}
        else {header("Content-disposition: inline");}

        if($length <= $fragment_length) { readfile($file_path); return; }

        $resource = fopen($file_path, 'rb');

        while(!feof($resource)) {
            print @fread($resource, $fragment_length);
            ob_flush();
            flush();
        }

        fclose($resource);
    }
    private function get_info_to_response(string $file_path, string $replace_filename = null):array {
        $length = filesize($file_path);
        $filename = $replace_filename??$file_path;
        $fragment_length = 1024 ** 2;
        $mime = mime_content_type($file_path);
        
        return [
            "length" => $length,
            "filename" => $filename,
            "fragment_length" => $fragment_length,
            "mime" => $mime,
        ];
    }
}