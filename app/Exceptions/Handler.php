<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    // //Metodo agregado por la IA OJO
    // /**
    //  * Render an exception into an HTTP response.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @param  \Throwable  $exception
    //  * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
    //  */
    // public function render($request, Throwable $exception)
    // {
    //     // Customize the response for AccessDeniedHttpException
    //     if ($exception instanceof AccessDeniedHttpException) {
    //         return response()->json(['error' => 'Acceso denegado.'], 403);
    //     }

    //     // Rest of the default rendering code

    //     return parent::render($request, $exception);
    // }
}
