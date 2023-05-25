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
    function send_file(string $file_path){
        $mime = mime_content_type($file_path);
        if (!$mime) throw new Exception("File path to send not found (\"$file_path\")");
        $content = file_get_contents($file_path);
        header("Content-Type: $mime");
        echo $content;
    }
}