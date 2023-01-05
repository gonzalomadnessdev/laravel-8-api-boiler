<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use jeremykenedy\LaravelRoles\App\Exceptions\PermissionDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        ApiException::class,
        PermissionDeniedException::class
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        $mensajeGenerico = "Ha ocurrido un error.";

        $statusCode = 400;
        $mensaje = $mensajeGenerico;

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();

            if ($exception instanceof UnauthorizedHttpException) {
                if ($exception->getMessage() == "The token has been blacklisted") {
                    $mensaje = "El Token ha sido deshabilitado.";
                } else if ($exception->getMessage() == "Token has expired") {
                    $mensaje = "El Token ha expirado.";
                } else if ($exception->getMessage() == "Token not provided") {
                    $mensaje = "El Token no ha sido proporcionado.";
                } else if ($exception->getMessage() == "Token Signature could not be verified.") {
                    $mensaje = "El Token no pudo ser verificado.";
                } else if ($exception->getMessage() == "Wrong number of segments") {
                    $mensaje = "El Token esta mal formado.";
                } else if (mb_strpos($exception->getMessage(), 'Could not decode token:') !== false) {
                    $mensaje = "El Token no pudo ser verificado.";
                }

            }else if ($exception instanceof NotFoundHttpException) {
                $mensaje = "La ruta no existe.";
            }

            if ($exception->getMessage() == 'Las credenciales no son válidas.'){
                $mensaje = $exception->getMessage();
            }

        }else if($exception instanceof Exception){

            if ($exception instanceof PermissionDeniedException) {

                $statusCode = 200;
                $mensaje = "No tiene los permisos necesarios.";

            }else if ($exception instanceof ValidationException) {

                $statusCode = 200;

                $erroresTodos = $exception->errors();
                $mensaje_error = [];
                foreach ($erroresTodos as $errores) {
                    foreach ($errores as $error) {
                        $mensaje_error[] = $error;
                    }
                }
                $mensaje = implode(" ", $mensaje_error);


            }else if($exception instanceof QueryException){
                $mensaje = "La consulta contiene errores.";

            }else if ($exception instanceof ApiException){

                $mensaje = $exception->getMessage();
                $statusCode = 200;
            }
        }

        return response()->json([
            'status' => 'error',
            'mensaje_error' => $mensaje
        ], $statusCode);
    }
}
