<?php

namespace App\Libs\Controllers;

use Input;
use Eloquent as Model;

trait Uploadable
{
    public function upload($name, $prefix, $destinationPath, $model = null)
    {
        if (Input::file($name) && Input::file($name)->isValid()) {
            // $destinationPath = public_path('uploads');
            $extension = Input::file($name)->getClientOriginalExtension();
            $fileName = $prefix.md5(microtime().rand()).'.'.$extension;
            Input::file($name)->move($destinationPath, $fileName);

            // Remove Old Image
            // if (!is_null($model) && $model->$name != "" && ($model instanceof Model)) {
            //     $oldFile = public_path('uploads') . $model->$name;
            //     if (is_file($oldFile) && $oldFile != public_path('uploads')) {
            //         unlink($oldFile);
            //     }
            // }
            return $fileName;
        }

        return 'invalid';
    }
}
