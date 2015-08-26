<?php

/**
 * Created by PhpStorm.
 * User: nay
 * Date: 8/19/15
 * Time: 11:01 PM
 */

namespace App\Libs\Response;


use Symfony\Component\HttpFoundation\Response;

trait ApiResponse {


    /**********************************/
    /**
     * Create response for bad request.
     *
     * @param null $message
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|SymfonyResponse
     */
    public function responseBadRequest($message = null, $data = null)
    {
        if (!$message) {
            $message =  Response::$statusTexts[Response::HTTP_BAD_REQUEST];
        }

        return $this->responseError($message, Response::HTTP_BAD_REQUEST, $data);
    }

    /**
     * Create response for error request.
     *
     * @param $message
     * @param $code
     *
     * @param null $data
     * @param string $type
     * @return SymfonyResponse|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function responseError($message, $code, $data = null, $type = 'bad_request')
    {
        return response([
            'error' => true,
            'success' => false,
            'code' => $code,
            'message' => $message,
            'type' => $type,
            'data' => $data
        ], $code);
    }

    /**
     * Create response for error request.
     *
     * @param array $data
     * @param int $code
     * @return \Illuminate\Contracts\Routing\ResponseFactory|SymfonyResponse
     * @internal param $message
     */
    public function responseSuccess($data = [], $code = 200)
    {
        return response(array_merge([
            'error' => false,
            'success' => true,
            'code' => $code,
            'message' => 'Success',
            'type' => 'success',
        ], $data), $code);
    }

    public function responseSuccessWithData($data = [], $code = 200) {
        return $this->responseSuccess(['data' => $data], $code);
    }

    /**
     * Create response for forbidden.
     *
     * @param null $message
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|SymfonyResponse
     */
    public function responseForbidden($message = null)
    {
        if (!$message) {
            $message = Response::$statusTexts[Response::HTTP_FORBIDDEN];
        }

        return $this->responseError($message, Response::HTTP_FORBIDDEN);
    }
}