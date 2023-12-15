<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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

        $this->renderable(function (Throwable $e, $request) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'errors' => [
                        'message' => $e->getMessage(),
                    ],
                ], 500);
            }
        });
    }

//    public function render($request, Throwable $e)
//    {
//        // return json response with the exception message, inside errors array. print all errors in the array
//        return response()->json([
//            'errors' => [
//                'message' => $e->getMessage(),
//            ],
//        ], 400);
//    }


}
