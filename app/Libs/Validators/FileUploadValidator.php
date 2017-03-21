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
use App\Libs\Exceptions\NoFileUploadException;
use Exception;

class FileUploadValidator extends FilesystemValidator
{
    /**
     * @param $info
     *
     * @return bool
     *
     * @throws Exception
     * @throws FileAlreadyExistsException
     * @throws NoFileUploadException
     * @throws PathNotAllowException
     * @throws WriteProtectException
     */
    public function validate($info)
    {
        $file = $info['file'];
        $uploadDir = $info['uploadDir'];
        $rootDir = $info['rootDir'];
        $upload_max_file_size = $info['upload_max_file_size'];

        if (!$file) {
            throw new NoFileUploadException(trans('messages.no_file_upload'));
        }

        // File name
        $filename = $file->getClientOriginalName();

        // File size(bytes)
        $fileSize = $file->getClientSize();

        // Full upload path
        $uploadPath = $this->fs->mergePaths([$uploadDir, $filename]);

        // Full upload folder
        $realUploadDir = $this->fs->mergePaths([$rootDir, $uploadDir]);

        // Full upload folder / File name
        $realUploadPath = $this->fs->mergePaths([$realUploadDir, $filename]);

        if ($this->fs->exists($realUploadPath)) {
            throw new FileAlreadyExistsException(trans('messages.file_already_exists', ['name' => $filename]), 1);
            // Write protected folder
        } elseif (!File::isWritable($realUploadDir)) {
            throw new WriteProtectException(trans('messages.permission_deny'), 1);
            // Check directory traversal
        } elseif (!$this->fs->isSecurePath($rootDir, $uploadDir)) {
            throw new PathNotAllowException(trans('messages.path_not_allow'), 1);
            // Check file size
        } elseif ($fileSize > $upload_max_file_size) {
            throw new Exception(trans('messages.exceeds_upload_size', ['name' => $filename]), 1);
            // Check file extension
        } elseif (false) {
            throw new Exception(trans('messages.file_type_not_allowed'), 1);
        }

        return true;
    }
}
