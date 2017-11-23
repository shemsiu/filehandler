<?php

namespace Updev\FileHandler;

use Illuminate\Http\UploadedFile;
use Illuminate\Http\Testing\MimeType;
use Updev\FileHandler\FileHandlerController;

class FileHandler
{
    private $controller;

    public function __construct()
    {
        $this->controller = new FileHandlerController(new MimeType);
    }

    public function file(UploadedFile $file = null)
    {
        if ($file == null) {
            return $this->controller->getFile();
        }
        $this->controller->setFile($file);
        return $this;
    }

    public function maxSize($bytes = null)
    {
        if ($bytes == null) {
            return $this->controller->getRule('maxSize');
        }

        $this->controller->setMaxSize($bytes);
        return $this;
    }

    public function maxHeight(int $pixels = null)
    {
        if ($pixels == null) {
            return $this->controller->getRule('maxHeight');
        }

        $this->controller->setMaxHeight($pixels);
        return $this;
    }

    public function maxWidth(int $pixels = null)
    {
        if ($pixels == null) {
            return $this->controller->getRule('maxWidth');
        }

        $this->controller->setMaxWidth($pixels);
        return $this;
    }

    public function minHeight(int $pixels = null)
    {
        if ($pixels == null) {
            return $this->controller->getRule('minHeight');
        }

        $this->controller->setMinHeight($pixels);
        return $this;
    }

    public function minWidth(int $pixels = null)
    {
        if ($pixels == null) {
            return $this->controller->getRule('minWidth');
        }

        $this->controller->setMinWidth($pixels);
        return $this;
    }

    public function accept($list = null)
    {
        if ($list == null) {
            return $this->controller->getRule('whitelist');
        }

        $this->controller->setWhitelist(is_array($list) ? $list : func_get_args());
        return $this;
    }

    public function whitelist($list = null)
    {
        if ($list == null) {
            return $this->controller->getRule('whitelist');
        }

        $this->controller->setWhitelist(is_array($list) ? $list : func_get_args());
        return $this;
    }

    public function blacklist($list = null)
    {
        if ($list == null) {
            return $this->controller->getRule('blacklist');
        }

        $this->controller->setBlacklist(is_array($list) ? $list : func_get_args());
        return $this;
    }

    public function filename(string $filename = null)
    {
        if ($filename == null) {
            return $this->controller->getFilename();
        }

        $this->controller->setFilename($filename);
        return $this;
    }

    public function path(string $storagePath = null)
    {
        if ($storagePath == null) {
            return $this->controller->getStoragePath();
        }
        $this->controller->setStoragePath($storagePath);
        return $this;
    }

    public function safe(bool $safe = true)
    {
        $this->controller->setSafe($safe);
        return $this;
    }

    public function unsafe()
    {
        $this->controller->setSafe(false);
        return $this;
    }

    public function save(string $storagePath = null, string $filename = null)
    {
        if ($storagePath != null) {
            $this->controller->setStoragePath($storagePath);
        }
        if ($filename != null) {
            $this->controller->setFilename($filename);
        }
        return $this->controller->save();
    }

    public function exists(string $storagePath)
    {
        return $this->controller->exists($storagePath);
    }

    public function download(string $storagePath)
    {
        return $this->controller->getFullPath($storagePath);
    }

    public function delete(string $storagePath)
    {
        return $this->controller->delete($storagePath);
    }
}
