<?php

/**
 * Created by Nay Zaw Oo<naythurain.071@gmail.com>
 * User: nay
 * Date: D/M/Y
 * Time: MM:HH PM.
 */
namespace App\Libs\Validators;

use App\Libs\Filesystem\Filesystem;

abstract class FilesystemValidator
{
    /**
     * @var Filesystem
     */
    public $fs;

    public function __construct()
    {
        $this->fs = Filesystem::getInstance();
    }
}
