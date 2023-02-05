<?php

namespace App\Plugins\Repositories\edusharing;

class CurlResult {
    public $content;
    public $error;
    public $info;
    public function __construct(
        string $content,
        int $error,
        array $info
    ) {
        $this->content = $content;
        $this->error = $error;
        $this->info = $info;
    }
}
