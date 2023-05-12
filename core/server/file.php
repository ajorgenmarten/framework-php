<?php
namespace core\server;

use core\server\interfaces\IFile;

class File implements IFile {
    public ?string $name = null;
    public ?string $full_path = null;
    public ?string $type = null;
    public ?string $tmp_name = null;
    public ?int $error = null;
    public ?int $size = null;
    private bool $tmp_name_is_data = false;

    function __construct(bool $tmp_name_is_data = false)
    {
        $this->tmp_name_is_data = $tmp_name_is_data;
    }

    function put(string $path): void {
        if($this->error !== UPLOAD_ERR_OK) return;
        if($this->tmp_name_is_data) {
            $resource = fopen($path, "w");
            fwrite($resource, $this->tmp_name);
            fclose($resource);
            return;
        }
        move_uploaded_file($this->tmp_name, $path);
    }
}