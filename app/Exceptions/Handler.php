<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

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
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {

        if ($exception instanceof TokenMismatchException) {
            return redirect('/login')->with('error', 'Your session has expired. Please log in again.');
        }

        if ($exception instanceof InvalidSignatureException) {
            return redirect()->route('login')
                ->with('accountnotverify', __('messages.emailVerifyFail'))
                ->with('error', __('messages.emailVerifyFail'));
        }

        // if ($exception instanceof NotFoundHttpException) {
        //     if (auth()->check()) {
        //         return response()->view('errors.404-authenticated', [], 404);
        //     } else {
        //         return redirect('/login');
        //     }
        // }

        return parent::render($request, $exception);

    }
}
