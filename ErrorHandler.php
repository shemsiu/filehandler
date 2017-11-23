<?php

namespace Updev\FileHandler;

class ErrorHandler
{
    private $errors = [];

    private $safe = true;

    public function add($error)
    {
        $this->errors[] = $error;
    }

    public function all()
    {
        return $this->runInSecureMode() ? $this->errors :  [];
    }

    public function isEmpty()
    {
        return $this->runInSecureMode() ? count($this->errors) : 0;
    }

    public function setUnsafe($safe)
    {
        $this->safe = $safe;
    }

    private function runInSecureMode()
    {
        return $this->safe;
    }
}
