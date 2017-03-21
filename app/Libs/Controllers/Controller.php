<?php

namespace App\Libs\Controllers;

/*
 * Created by Nay Zaw Oo<nayzawoo.me@gmail.com>
 * User: nay
 * Date: D/M/Y
 * Time: MM:HH PM
 */

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Input;

abstract class Controller extends BaseController
{
    use DispatchesCommands, ValidatesRequests;

    public function fileUpload($name, $prefix, $model = null)
    {
        if (!Input::file($name) || !Input::file($name)->isValid()) {
            return;
        }
        $destinationPath = uploadPath();
        $extension = Input::file($name)->getClientOriginalExtension();
        $fileName = $prefix.md5(microtime().rand()).'.'.$extension;
        Input::file($name)->move($destinationPath, $fileName);

        // Remove Old Image
        if (!is_null($model) && $model->$name != '' && ($model instanceof Model)) {
            $oldFile = uploadPath().$model->$name;
            if (is_file($oldFile) && $oldFile != uploadPath()) {
                unlink($oldFile);
            }
        }

        return $fileName;
    }
}
