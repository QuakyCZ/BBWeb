<?php

namespace App\Utils\FileSystem;

use Nette\Http\FileUpload;

interface IFileSystem
{
    public function saveFileUpload(FileUpload $fileUpload, string $dir): string;
}