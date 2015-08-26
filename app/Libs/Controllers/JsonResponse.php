<?php

namespace App\Libs\Controllers;

trait JsonResponse
{

    protected $defaultResponse = [
        "status"  => "ok",
        "message" => "Success",
        "data"    => [],
    ];

    public function makeJsonResponse($response, $code = null)
    {
        $response   = array_merge($this->defaultResponse, $response);
        $status     = strtoupper($response['status']);
        $httpStatus = 'HTTP_' . $status;
        if (!$code) {
            $code = constant("\Symfony\Component\HttpFoundation\Response::{$httpStatus}");
        }
        return response($response, $code);
    }

    protected function responseSuccess($message = "SUCCESS", $data = [])
    {
        return $this->makeJsonResponse(['data' => $data]);
    }

    protected function responseBadRequest($message = "BAD_REQUEST", $data = [])
    {
        return $this->makeJsonResponse([
            "status"  => "BAD_REQUEST",
            "message" => $message,
            "data"    => $data,
        ]);
    }

    protected function responseError($message = "BAD_REQUEST", $data = [])
    {
        return $this->responseBadRequest($message, $data);
    }
}
