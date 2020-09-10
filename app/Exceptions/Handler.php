<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Exception;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
    * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Exception $exception)
    {
        if($request->is('api/*')) {
            $request->headers->set('accept', 'application/json');
        }
        if($request->wantsJson()) {
            return response()->json([
                'status' => 'failed',
                'message' => $exception->getMessage(),
                'errors' => [
                    'details' => $exception->getMessage(),
                    'status_code' => 400
                ]
            ], 400);
        }
        return parent::render($request, $exception);
    }
}
