<?php

namespace App\Exceptions;

use App\Libs\Response\ApiResponse;
use App\MongoAdmin\Exceptions\MongoAdminException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use MongoConnectionException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    use ApiResponse;

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
        ModelNotFoundException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof MongoConnectionException) {
            return $this->responseBadRequest($e->getMessage());
        }

        if ($e instanceof MongoAdminException) {
            return $this->responseBadRequest(trans('errors.'.$e->getMessage()));
        }

        return parent::render($request, $e);
    }
}
