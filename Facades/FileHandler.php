<?php

namespace Updev\FileHandler\Facades;

use Illuminate\Support\Facades\Facade;

class FileHandler extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'FileHandler';
    }
}
