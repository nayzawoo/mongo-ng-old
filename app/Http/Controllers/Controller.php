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

    protected function responseErr($type)
    {
        $m = "Error";
        $code = 400;
        switch ($type) {
            case "db_not_found":
                $m = "Database Not Found";
                $code = 404;
                break;
            case "coll_not_found":
                $m = "Collection Not Found";
                $code = 404;
                break;
            case "doc_not_found":
                $m = "Document Not Found";
                $code = 404;
                break;
            case "invalid_id":
                $m = "Invalid MongoId";
                $code = 400;
                break;
        }

        return $this->responseError($m, $code, [], $type);
    }
}
