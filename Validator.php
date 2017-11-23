<?php

namespace Updev\FileHandler;

use Updev\FileHandler\File;
use Updev\FileHandler\ErrorHandler;
use Illuminate\Http\Testing\MimeType;

class Validator
{
    const EXTENSIONS_IMAGES = [
        'ani', 'bmp', 'cal', 'fax', 'gif', 'img', 'jpe', 'jpeg',
        'jpg', 'mac', 'pbm', 'pcd', 'pcx', 'pct', 'pgm', 'png',
        'ppm', 'psd', 'ras', 'tga', 'tif', 'tiff', 'wmf', 'svg'
    ];
    const EXTENSIONS_DOCUMENTS = [
        // General
        'pdf', 'xml', 'txt', 'rtf', 'odt', 'json', 'svg', 'dat', 'sql', 'tex', 'log',
        // Office Word
        'doc', 'dot', 'wbk', 'docx', 'docm', 'dotx', 'dotm', 'docb',
        // Office Excel
        'xls', 'xlt', 'xlm', 'xlsx', 'xlsm', 'xltx', 'xltm', 'xlsb', 'xla', 'xlam', 'xll', 'xlw',
        // Office Powerpoint
        'ppt', 'pot', 'pps', 'pptx', 'pptm', 'potx', 'potm',
        // Office Publisher
        'pub', 'xps',
    ];

    private $errors;
    private $mimetype;

    public function __construct(ErrorHandler $errors, MimeType $mimetype)
    {
        $this->errors = $errors;
        $this->mimetype = $mimetype;
    }

    public function run(array $rules, File $file)
    {
        $this->validateFileIsValid($file->isValid());

        $this->validateBlacklist($rules['blacklist'], $file->extension());
        $this->validateWhitelist($rules['whitelist'], $file->extension(), $rules['blacklist']);

        $this->validateMaxHeight($rules['maxHeight'], $file->height());
        $this->validateMaxWidth($rules['maxWidth'], $file->width());

        $this->validateMinHeight($rules['minHeight'], $file->height());
        $this->validateMinWidth($rules['minWidth'], $file->width());

        $this->validateSize($rules['maxSize'], $file->size());

        $this->validateSafe($rules['safe']);

        return $this;
    }

    private function validateSafe($safe)
    {
        $this->errors->setUnsafe($safe);
        return;
    }

    private function validateBlacklist($blacklist, $extension)
    {
        if ($blacklist == null) {
            return;
        }

        $image = array_search('image', $blacklist);
        $document = array_search('document', $blacklist);

        if (is_int($image)) {
            if (in_array($extension, self::EXTENSIONS_IMAGES)) {
                $this->errors
                    ->add($extension . ' billede formater ikke tilladte.');
            }
            return;
        }

        if (is_int($document)) {
            if (in_array($extension, self::EXTENSIONS_DOCUMENTS)) {
                $this->errors
                    ->add($extension . ' dokument formater er ikke tilladte.');
            }
            return;
        }

        if (in_array($extension, $blacklist)) {
            $this->errors
                ->add($extension . ' format er ikke tilladt.');
        }

        return;
    }

    private function validateWhitelist($whitelist, $extension, $blacklist)
    {
        // If both black and whitelist is defined then exclude whitelist
        if ($blacklist != null) {
            return;
        }

        if ($whitelist == null) {
            return;
        }

        $all = array_search('all', $whitelist);
        $image = array_search('image', $whitelist);
        $document = array_search('document', $whitelist);

        if (is_int($all)) {
            if (!in_array($extension, array_keys($this->mimetype->get()))) {
                $this->errors
                    ->add($extension . ' format er ikke tilladt.');
            }

            return;
        }

        if (is_int($image)) {
            if (!in_array($extension, self::EXTENSIONS_IMAGES)) {
                $this->errors
                    ->add($extension . ' format er ikke billede format.');
            }
            return;
        }

        if (is_int($document)) {
            if (!in_array($extension, self::EXTENSIONS_DOCUMENTS)) {
                $this->errors
                    ->add($extension . ' format er ikke et dokument format');
            }
            return;
        }

        if (!in_array($extension, $whitelist)) {
            $this->errors
                ->add($extension . ' format er ikke på listen over tilladte formater');
        }

        return;
    }

    private function validateFileIsValid($valid)
    {
        if (!$valid) {
            $this->errors
                ->add('Filen er ikke gyldig.');
        }
    }

    private function validateSize($maxSize, $currentSize)
    {
        if ($maxSize == null) {
            return;
        }

        if ($currentSize > $maxSize) {
            $this->errors
                ->add('Max filstørrelse: ' . $maxSize . ' bytes');
        }
    }

    private function validateMaxHeight($maxHeight, $currentHeight)
    {
        if ($maxHeight == null || $currentHeight == null) {
            return;
        }

        if ($currentHeight > $maxHeight) {
            $this->errors
                ->add('Max højde: ' . $maxHeight . ' pixels');
        }
    }

    private function validateMaxWidth($maxWidth, $currentWidth)
    {
        if ($maxWidth == null || $currentWidth == null) {
            return;
        }

        if ($currentWidth > $maxWidth) {
            $this->errors
                ->add('Max bredde: ' . $maxWidth . ' pixels');
        }
    }


    private function validateMinHeight($minHeight, $currentHeight)
    {
        if ($minHeight == null || $currentHeight == null) {
            return;
        }

        if ($currentHeight < $minHeight) {
            $this->errors
                ->add('Minimumshøjde: ' . $minHeight . ' pixels');
        }
    }

    private function validateMinWidth($minWidth, $currentWidth)
    {
        if ($minWidth == null || $currentWidth == null) {
            return;
        }

        if ($currentWidth < $minWidth) {
            $this->errors
                ->add('Minimumsbredde: ' . $minWidth . ' pixels');
        }
    }

    public function errors()
    {
        return $this->errors->isEmpty();
    }

    public function getErrors()
    {
        return $this->errors->all();
    }
}
