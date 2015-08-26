<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;

    public function responseSuccess()
    {
        return response([
            'success' => true
        ]);
    }

    public function responseFail()
    {
        return response([
            'success' => false
        ]);
    }
}
