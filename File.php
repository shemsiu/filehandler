<?php

namespace Updev\FileHandler;

use Illuminate\Http\Testing\MimeType;

class File
{
    public $uploadedFile = null;

    private $extension;

    private $width;

    private $height;

    private $mimetype;

    public function __construct(MimeType $mimetype)
    {
        $this->mimetype = $mimetype;
    }

    public function init($uploadedFile)
    {
        list($width, $height) = getimagesize($uploadedFile->getPathName());

        $this->uploadedFile = $uploadedFile;

        $this->width = $width;
        $this->height = $height;

        $this->extension = $this->mimetype->search($uploadedFile->getClientMimeType());
    }

    public function isValid()
    {
        if ($this->uploadedFile == null) {
            return false;
        }

        return $this->uploadedFile->isValid();
    }

    public function name()
    {
        return $this->uploadedFile->getClientOriginalName();
    }

    public function size()
    {
        return $this->uploadedFile->getClientSize();
    }

    public function mimeType()
    {
        return $this->uploadedFile->getClientMimeType();
    }

    public function height()
    {
        return $this->height;
    }

    public function width()
    {
        return $this->width;
    }
    public function extension()
    {
        return $this->extension;
    }
}
