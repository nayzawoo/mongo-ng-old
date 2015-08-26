<?php

namespace App\Libs\Filesystem;

/*
 * Created by Nay Zaw Oo<naythurain.071@gmail.com>
 * User: nay
 * Date: D/M/Y
 * Time: MM:HH PM
 */
use App\Libs\Filesystem\Exceptions\FilesystemException;
use File;
use Symfony\Component\Finder\Finder;

/**
 * Slim Filesystem.
 *
 * @author Nay Zaw Oo<naythurain.071@gmail.com>
 *
 * @todo
 **/
class Filesystem
{
    /**
     * @var Filesystem
     */
    public static $instance = null;

    /**
     * Default file size units.
     *
     * @var string
     */
    public $units = array(
        'terabyte' => 'Tb',
        'gigabyte' => 'Gb',
        'megabyte' => 'Mb',
        'kilobyte' => 'Kb',
        'bytes'    => 'Bytes',
        'byte'     => 'Byte',
    );

    /**
     * Constructor.
     **/
    public function __construct()
    {
        if (!defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
        }
    }

    /**
     * [getMaxUploadSize description].
     *
     * @return int
     */
    public function getUploadMaxSize($custom_max_upload = 99999)
    {
        $max_upload   = (int) (ini_get('upload_max_filesize'));
        $max_post     = (int) (ini_get('post_max_size'));
        $memory_limit = (int) (ini_get('memory_limit'));

        return min($max_upload, $max_post, $memory_limit, $custom_max_upload);
    }

    /**
     * Set units lang] for file size.
     *
     * @param array $units
     **/
    public function setUnits(array $units)
    {
        $this->units = $units;
    }

    public function removeSlashes($path)
    {
        return preg_replace('#' . DS . '+#', DS, $path);
    }

    /**
     * @param $path
     *
     * @return bool
     **/
    public function exists($path)
    {
        return is_file($path) || is_dir($path) || is_link($path);
    }

    /**
     * @param $path
     *
     * @return bool
     */
    public function isValid($path)
    {
        return $this->isAbsolutePath($path);
    }

    /**
     * Check given path is secure
     * to prevent "Directory Traversal".
     *
     * @param string $basepath [Root path for user]
     * @param string $userpath [User given path]
     *
     * @return bool
     */
    public function isSecurePath($basepath, $givenPath)
    {
        $realBase = realpath($basepath);

        $userpath     = $basepath . $givenPath;
        $realUserPath = realpath($userpath);

        if ($realUserPath === false || strpos($realUserPath, $realBase) !== 0) {
            return false;
        }

        return true;
    }

    /**
     * Get path name.
     *
     * @param string
     *
     * @return string path name
     **/
    public function getName($path)
    {
        return pathinfo($path)['basename'];
    }

    /**
     * Return file size.
     *
     * @param $path
     * @param bool $dir
     *
     * @return int|string
     *
     * @throws FilesystemException
     **/
    public function getSize($path, $dir = false)
    {
        if (is_file($path) || is_link($path)) {
            return $this->sizeToString(filesize($path));
        } elseif ($dir && is_dir($path)) {
            return $this->getItemCount($path);
        }

        throw new \FilesystemException('Path not found. : ' . $path);
    }

    /**
     * Get all items list in path.
     *
     * @param ignoreDotFile bool
     * @param bool $ignoreDotFile
     *
     * @return array
     **/
    public function getItems($path, $ignoreDotFile = true, $depth = 0)
    {
        // FilesystemException
        $dirs  = $this->getDirectories($path, $ignoreDotFile, $depth);
        $files = $this->getFiles($path, $ignoreDotFile, $depth);

        return array_merge($dirs, $files);
    }

    /**
     * Get all of the files within a given directory.
     *
     * @param $path
     * @param bool $igdf
     *
     * @return array
     *
     * @internal param string $directory
     **/
    public function getFiles($path, $igdf = true, $depth = 0)
    {
        $files = array();
        $_tmp  = Finder::create()->in($path)
                                 ->sortByName()
                                 ->ignoreDotFiles($igdf)
                                 ->files()
                                 ->depth($depth);

        foreach ($_tmp as $dir) {
            $files[] = $dir->getPathname();
        }

        return $files;
    }

    /**
     * Get parent directory.
     *
     * @param $path
     *
     * @return string
     **/
    public function getParent($path)
    {
        return dirname($path);
    }

    /**
     * Get all of the directories within a given directory.
     *
     * @param $path
     * @param bool $ignore_dot
     *
     * @return array
     *
     * @throws FilesystemException
     **/
    public function getDirectories($path, $ignore_dot = true, $depth = 0)
    {
        if (!is_readable($path)) {
            throw new FilesystemException('Permission denied');
        }

        $directories = array();
        $_tmp        = Finder::create()->in($path)
                                       ->sortByName()
                                       ->ignoreDotFiles($ignore_dot)
                                       ->directories()
                                       ->depth($depth);
        foreach ($_tmp as $dir) {
            $directories[] = $dir->getPathname();
        }

        return $directories;
    }

    /**
     * Rename file or dir.
     *
     * @param $path string
     * @param $new_name string
     *
     * @return bool
     *
     * @throws FilesystemException from move()
     **/
    public function rename($path, $new_name)
    {
        $target = $this->mergePaths([DS, $this->getParent($path), $new_name]);

        return $this->move($path, $target);
    }

    /**
     * Move path.
     *
     * @param $path
     * @param $target
     *
     * @return bool
     *
     * @throws FilesystemException
     **/
    public function move($path, $target)
    {
        return rename($path, $target);
    }

    public function makeLink($path, $target, $makeMirror = true)
    {
        try {
            return $this->symfony->symlink($target, $target, $makeMirror);
        } catch (IOException $e) {
            throw new FilesystemException($e, 1);
        }
    }

    /**
     * Return general file type
     * eg: audio, video, text.
     *
     * @param $path
     **/
    public function generalType($path)
    {
    }

    /**
     * Normalize path for merging.
     *
     * @param $path
     *
     * @return mixed
     **/
    public function normalizePath($path)
    {
        return preg_replace('/(\\' . DS . '){2,}/i', '/', $path);
    }

    /**
     * Auto prefix when paths concat.
     *
     * @param $paths
     *
     * @return mixed
     **/
    public function mergePaths($paths)
    {
        return $this->removeSlashes(implode(DS, $paths));
    }

    /**
     * Has items in path.
     *
     * @param $path
     *
     * @return bool
     **/
    public function hasDirs($path)
    {
        return 0 < count($this->getDirectories($path));
    }

    /**
     * H
     * as items in path.
     *
     * @param $path
     *
     * @return bool
     */
    public function hasItems($path)
    {
        return !$this->isDirectoryEmpty($path);
    }

    /**
     * Determine if the given path contains no files.
     *
     * @param string $directory
     *
     * @return bool
     *
     * @throws FilesystemException
     * @copy october\filesystem
     */
    public function isDirectoryEmpty($directory)
    {
        $handle = opendir($directory);
        while (false !== ($entry = readdir($handle))) {
            if ($entry != '.' && $entry != '..') {
                
                closedir($handle);

                return false;
            }
        }
        closedir($handle);

        return true;
    }

    /**
     * Converts a file size in bytes to human readable format.
     *
     * @param int $bytes
     *
     * @return string
     * @copy october\filesystem
     */
    public function sizeToString($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' ' . $this->units['gigabyte'];
        }

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' ' . $this->units['megabyte'];
        }

        if ($bytes >= 1024) {
            return $bytes = number_format($bytes / 1024, 2) . ' ' . $this->units['kilobyte'];
        }

        if ($bytes > 1) {
            return $bytes = $bytes . ' ' . $this->units['bytes'];
        }

        if ($bytes == 1) {
            return $bytes . ' ' . $this->units['byte'];
        }

        return '0' . ' ' . $this->units['byte'];
    }

    /**
     * Get latest modified date time.
     *
     * @param $path
     * @param string $format time format
     *
     * @return object
     *
     * @throws FilesystemException
     */
    public function getModifiedTime($path, $format = 'F d Y')
    {
        if (is_file($path) || is_dir($path)) {
            return date($format, filemtime($path));
        }

        throw new FilesystemException('Invalid path');
    }

    /**
     * Return Items counts in the directory.
     *
     * @param string
     *
     * @return string
     */
    public function getItemCount($path)
    {
        return count($this->getItems($path));
    }

    /**
     * Returns whether the file path is an absolute path.
     *
     * @param $path
     *
     * @return bool
     *
     * @internal param string $file A file path
     *
     * @see Symfony\Component\Filesystem -> isAbsolutePath()
     */
    public function isAbsolutePath($path)
    {
        if (strspn($path, '/\\', 0, 1)
            || (strlen($path) > 3 && ctype_alpha($path[0])
                && substr($path, 1, 1) === ':'
                && (strspn($path, '/\\', 2, 1))
            )
            || null !== parse_url($path, PHP_URL_SCHEME)
        ) {
            return true;
        }

        return false;
    }

    /**
     * Missing method call Illuminate\Filesystem;.
     *
     * @param $method
     * @param $params
     *
     * @return
     */
    public function __call($method, $params)
    {
        switch (count($params)) {
            case 1:
                return File::$method($params[0]);
                break;
            case 2:
                return File::$method($params[0], $params[1]);
                break;
            case 3:
                return File::$method($params[0], $params[1], $params[2]);
                break;
            default:
                return File::$method();
                break;
        }
    }

    public static function getInstance()
    {
        if (!static::$instance) {
            static::$instance = new self();
        }

        return static::$instance;
    }
}
