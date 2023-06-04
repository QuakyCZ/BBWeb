<?php

namespace App\Utils\FileSystem;

use App\Repository\Primary\ServerRepository;
use Nette\Http\FileUpload;
use Nette\Utils\DateTime;

class LocalFileSystem implements IFileSystem
{

    /**
     * @param FileUpload $fileUpload
     * @param string $dir path from the www folder, must begin and end with /
     * @return string final file name (without dir)
     * @throws FileSystemException
     */
    public function saveFileUpload(FileUpload $fileUpload, string $dir): string {
        if (!$fileUpload->isOk()) {
            throw new FileSystemException("File is not ok!");
        }

        $fileName = $fileUpload->getSanitizedName() . '_' . (new DateTime())->getTimestamp() . '.' . $fileUpload->getImageFileExtension();
        $fileUpload->move(__DIR__. '/../../../www' . $dir.$fileName);
        return $fileName;
    }

    /**
     * @param string $directory
     * @return bool
     */
    private function prepareDirectory(string $directory): bool {
        return is_dir($directory) || (mkdir($directory, 0777, true) && is_dir($directory));
    }
}