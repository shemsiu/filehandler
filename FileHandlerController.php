<?php

namespace Updev\FileHandler;

use Updev\FileHandler\File;
use Updev\FileHandler\Validator;
use Updev\FileHandler\ErrorHandler;
use Illuminate\Http\UploadedFile;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Testing\MimeType;

class FileHandlerController extends Filesystem
{

    protected $file;
    protected $validator;

    protected $filename = null;
    protected $storagePath = 'public/uploads';

    public $rules = [
        'safe' => true,
        'maxSize' => null,
        'maxHeight' => null,
        'maxWidth' => null,
        'minHeight' => null,
        'minWidth' => null,
        'whitelist' => null,
        'blacklist' => null,
    ];

    public function __construct(MimeType $mimetypes)
    {
        $this->file = new File($mimetypes);
        $this->validator = new Validator(new ErrorHandler, $mimetypes);
    }

    public function setFile(UploadedFile $uploadedFile)
    {
        $this->file->init($uploadedFile);
        return $this;
    }

    public function getFile()
    {
        return $this->$file;
    }

    public function setSafe($value)
    {
        $this->addRule('safe', $value);
        return $this;
    }

    public function setMaxSize($bytes)
    {
        $value = (int) preg_replace("/[^0-9]/", "", $bytes);
        $type = strtolower(preg_replace("/[^A-Za-z]/", "", $bytes));

        if (empty($type)) {
            $this->rules['maxSize'] = $value;
            return $this;
        }

        $size = null;
        if ($type == 'kb') {
            $size = $value * 1024;
        }
        if ($type == 'mb') {
            $size = $value * 1048576;
        }
        if ($type == 'gb') {
            $size = $value * 1073741824;
        }

        $this->addRule('maxSize', $size);
        return $this;
    }

    public function setMaxWidth($pixels)
    {
        $this->addRule('maxWidth', $pixels);
        return $this;
    }

    public function setMaxHeight($pixels)
    {
        $this->addRule('maxHeight', $pixels);
        return $this;
    }

    public function setMinWidth($pixels)
    {
        $this->addRule('minWidth', $pixels);
        return $this;
    }

    public function setMinHeight($pixels)
    {
        $this->addRule('minHeight', $pixels);
        return $this;
    }

    public function setWhitelist($list)
    {
        $this->addRule('whitelist', $list);

        return $this;
    }

    public function setBlacklist($list)
    {
        $this->addRule('blacklist', $list);

        return $this;
    }

    public function setFilename($name)
    {
        $this->filename = $name;
        return $this;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function setStoragePath($storagePath)
    {
        $this->storagePath = $storagePath;
        return $this;
    }

    public function getStoragePath()
    {
        return $this->storagePath;
    }

    public function exists($storagePath)
    {
        return parent::exists(storage_path('app/' . $storagePath));
    }

    public function getFullpath($storagePath)
    {
        if ($this->exists($storagePath)) {
            return storage_path('app/' . $storagePath);
        }

        return null;
    }

    public function delete($storagePath)
    {
        return Storage::delete($storagePath);
    }

    public function save()
    {
        $filename = $this->defineFilename();

        // run validations
        $result = $this->validator->run($this->rules, $this->file);

        if ($result->errors()) {
            return $this->saveResult($filename, false);
        };

        if ($name = Storage::putFileAs($this->storagePath, $this->file->uploadedFile, $filename)) {
            return $this->saveResult($name);
        }

        return $this->saveResult(null, false, ['Filen kunne ikke uploades. Prøv igen senere.']);
    }

    public function getRule($key)
    {
        return $this->rules[$key];
    }

    private function defineFilename()
    {
        if ($this->filename == null) {
            return $this->removeSpacing(time() . '_' . $this->file->name());
        }

        return $this->removeSpacing($this->filename . '.' . $this->file->extension());
    }

    private function removeSpacing($value)
    {
        return preg_replace('/\s+/', '', $value);
    }

    private function addRule($key, $value)
    {
        $this->rules[$key] = $value;
    }

    private function saveResult($path, $success = true, $errors = null)
    {
        if ($errors == null) {
            $errors = $this->validator->getErrors();
        }

        $temp = new \stdClass();

        if ($success == true) {
            $path = storage_path('app/' . $path);

            $temp->name = $this->name($path) ?: null;
            $temp->basename = $this->basename($path) ?: null;
            $temp->dirname = $this->dirname($path) ?: null;
            $temp->extension = $this->extension($path) ?: null;
            $temp->type = $this->type($path) ?: null;
            $temp->mimeType = $this->mimeType($path) ?: null;
            $temp->size = $this->size($path) ?: null;
            $temp->lastModified = $this->size($path) ?: null;
            $temp->isDirectory = $this->isDirectory($path) ?: null;
            $temp->isReadable = $this->isReadable($path) ?: null;
            $temp->isWritable = $this->isWritable($path) ?: null;
            $temp->isFile = $this->isFile($path) ?: null;
        } else {
            $temp->errors = $errors;
        }

        $temp->valid = $success;

        return $temp;
    }
}
