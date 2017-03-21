<?php

/**
 * Created by Nay Zaw Oo<nayzawoo.me@gmail.com>
 * User: nay
 * Date: D/M/Y
 * Time: MM:HH PM.
 */
namespace App\Libs\Validators;

use File;
use App\Libs\Exceptions\FileAlreadyExistsException;
use App\Libs\Exceptions\PathNotAllowException;
use App\Libs\Exceptions\WriteProtectException;
use Exception;

class FilePreUploadValidator extends FilesystemValidator
{
    /**
     * @param $file $_FILE
     * @param $uploadPath [Upload request from user]
     * @param $realUploadPath [Upload path merged with root path]
     *
     * @return bool
     *
     * @throws Exception
     */
    public function validate($filename, $uploadDir, $rootDir, $fileSize, $upload_max_file_size)
    {
        // uploads/file.md
        $uploadPath = $this->fs->mergePaths([$uploadDir, $filename]);

        // /var/www/html/project/uploads
        $realUploadDir = $this->fs->mergePaths([$rootDir, $uploadDir]);

        // /var/www/html/project/uploads/file.md
        $realUploadPath = $this->fs->mergePaths([$realUploadDir, $filename]);

        if ($this->fs->exists($realUploadPath)) {
            throw new FileAlreadyExistsException('Already Exist', 1);
            // Write protect
        } elseif (!File::isWritable($realUploadDir)) {
            throw new WriteProtectException('Permission deny', 1);
            // is secure path
        } elseif (!$this->fs->isSecurePath($rootDir, $uploadDir)) {
            throw new PathNotAllowException('Path not allow', 1);
            // size
        } elseif ($fileSize > $upload_max_file_size) {
            throw new Exception("This file \"$filename\" exceeds the maximum upload size.", 1);
            // Extension
        } elseif (false) {
            throw new Exception('Not allow extension', 1);
        }

        return true;
    }
}
